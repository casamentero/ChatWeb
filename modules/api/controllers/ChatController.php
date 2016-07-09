<?php
/*
200: OK. Everything worked as expected.
201: A resource was successfully created in response to a POST request. The Location header contains the URL pointing to the newly created resource.
204: The request was handled successfully and the response contains no body content (like a DELETE request).
304: The resource was not modified. You can use the cached version.
400: Bad request. This could be caused by various actions by the user, such as providing invalid JSON data in the request body, providing invalid action parameters, etc.
401: Authentication failed.
403: The authenticated user is not allowed to access the specified API endpoint.
404: The requested resource does not exist.
405: Method not allowed. Please check the Allow header for the allowed HTTP methods.
415: Unsupported media type. The requested content type or version number is invalid.
422: Data validation failed (in response to a POST request, for example). Please check the response body for detailed error messages.
429: Too many requests. The request was rejected due to rate limiting.
500: Internal server error. This could be caused by internal program errors.
*/

namespace app\modules\api\controllers;
use yii\rest\ActiveController;
use app\modules\api\models\Profile;
use app\modules\api\models\Chat;
use app\modules\api\models\ChatTranslation;
use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use PhpAmqpLib\Message\AMQPMessage;

class ChatController extends ActiveController
{
	public $modelClass = 'app\modules\api\models\Chat';
	
	public function behaviors()
	{
		$behaviors = parent::behaviors();
	
		$arr =  [
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['create','index','view'],  // in a controller
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
				'languages' => [
					'en',
					'de',
				],
			],
			
			[
				'class' => \yii\filters\Cors::className(),
				'cors' => [
					'Origin' => ['*'],
					'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Allow-Credentials' => true,
					'Access-Control-Max-Age' => 86400,
				],
			]
		];
		
		return array_merge($behaviors,$arr);
	}

	public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
       // unset($actions['index']);
		$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
		
        return $actions;
    }
	
	public function prepareDataProvider() 
	{
		/*
		http://api.chatndate.com/web/api/chats
		http://api.chatndate.com/web/api/chats?page=2
		http://api.chatndate.com/web/api/chats?users=1,2
		http://api.chatndate.com/web/api/chats?history=today
		http://api.chatndate.com/web/api/chats?history=yesterday
		http://api.chatndate.com/web/api/chats?history=currentweek
		http://api.chatndate.com/web/api/chats?history=currentmonth
		http://api.chatndate.com/web/api/chats?history=last2days
		http://api.chatndate.com/web/api/chats?history=last7days
		http://api.chatndate.com/web/api/chats?history=last10days
		http://api.chatndate.com/web/api/chats?history=last31days
		http://api.chatndate.com/web/api/chats?direction=backward&startpoint=78124568
		Options:
		direction = forward|backward
		startpoint: chat_message_id from where you want to get records
		*/
	
		$users 		= Yii::$app->request->get('users');
		$history 	= Yii::$app->request->get('history');
		$direction 	= Yii::$app->request->get('direction');
		$startpoint = Yii::$app->request->get('startpoint');
		
		$q = new yii\db\Query;
		$q = $q->select('chat.*')
		->from('chat');
		
		if($users!=""){
			$users = explode(',',$users);
			if(count($users)==2){
				$q->andWhere('(
				(chat.from_id = '.$users[0].') AND (chat.to_id  = '.$users[1].')
				OR 
				(chat.from_id = '.$users[1].') AND (chat.to_id  = '.$users[0].')
				)');
			} elseif(count($users)>2){
				$q->andWhere('((chat.from_id IN('.$users.')) AND (chat.to_id IN('.$users.')))');
			} elseif(count($users)==1){
				$q->andWhere('(
				(chat.from_id = '.$users[0].') || (chat.to_id  = '.$users[0].')
				)');
			}
		}
		
		if($history!=""){
			switch($history){
				case 'today':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))=(CURDATE()))');
				break;
				case 'yesterday':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))=(DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))))');
				break;
				case 'currentweek':
					$q->andWhere('((YEARWEEK(FROM_UNIXTIME(chat.created_at), 1))=(YEARWEEK(CURDATE(), 1)))');
				break;
				case 'currentmonth':
					$q->andWhere('((MONTH(FROM_UNIXTIME(chat.created_at)))=(MONTH(CURDATE())))');
				break;
				case 'last2days':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))>=(DATE(DATE_SUB(NOW(), INTERVAL 2 DAY))))');
				break;
				case 'last7days':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))>=(DATE(DATE_SUB(NOW(), INTERVAL 7 DAY))))');
				break;
				case 'last10days':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))>=(DATE(DATE_SUB(NOW(), INTERVAL 10 DAY))))');
				break;
				case 'last31days':
					$q->andWhere('(DATE(FROM_UNIXTIME(chat.created_at))>=(DATE(DATE_SUB(NOW(), INTERVAL 31 DAY))))');
				break;
			}
		}
		
		$orderby = "DESC";
		
		if($direction!=""){
			switch($direction){
				case 'forward':
				$orderby = "ASC";
				break;
				case 'backward':
				$orderby = "DESC";
				break;
			}
		}	
		
		if($startpoint!=""){
			$chat = Chat::find()->select('id')->where(['chat_message_id'=>$startpoint])->one();
			if($chat===null){
				throw new \yii\web\HttpException(404, 'error');
			}
			if($orderby=='ASC'){
				$q->andWhere('(id > '.$chat->id.')');
			} elseif($orderby=='DESC'){
				$q->andWhere('(id < '.$chat->id.')');
			}
		}	
		
		if($orderby==""){
			$orderby = "DESC";
		}
		
		$q->addOrderBy('chat.id '.$orderby.'');
		
		
		return new ActiveDataProvider([
			'query' => $q
		]);
	}
	
    public function actionCreate(){
	
        // implement here your code
		//send(from_id,to_id,chat_message_en,languages_id,rabbitmq_exchange_name,rabbitmq_queue_name,rabbitmq_routing_key);
		
        $chat = Yii::createObject(Chat::className());
		
		$chat->from_id 				= Yii::$app->request->post('from_id');
		$chat->to_id 				= Yii::$app->request->post('to_id');
		$chat->chat_message_en 		= Yii::$app->request->post('chat_message_en');
		$chat->chat_message_es 		= Yii::$app->request->post('chat_message_es');
		$chat->chat_message_id 	= $this->generateMessageId($chat->from_id,$chat->to_id);
		$chat->languages_id 		= Yii::$app->request->post('languages_id');
		
		//Chat message object
		$message = new \stdClass;
		$message->from_id 				= $chat->from_id;
		$message->to_id 				= $chat->to_id;
		$message->chat_message_en 		= $chat->chat_message_en;
		$message->chat_message_es 		= $chat->chat_message_es;
		$message->chat_message_id 	= $chat->chat_message_id;
		$message->languages_id 			= $chat->languages_id;
		$message->created_at 			= time();
		
		$message = Json::encode($message);

		$chat->rabbitmq_exchange_name 	= Yii::$app->request->post('rabbitmq_exchange_name');
		$chat->rabbitmq_queue_name 		= Yii::$app->request->post('rabbitmq_queue_name');
		$chat->rabbitmq_routing_key 	= Yii::$app->request->post('rabbitmq_routing_key');
		
		if($chat->validate()){
		
			try {
				$exchangeName 	= $chat->rabbitmq_exchange_name;
				$routingKey 	= $chat->rabbitmq_routing_key;
				
				$connection = Yii::$app->amqp->getConnection();
				$channel = $connection->channel();
				
				$channel->exchange_declare($exchangeName, 'topic', $passive=false, $durable=true, $auto_delete=false);
				
				#Publish message to exchange for routing key of receiver
				$msg = new AMQPMessage($message);
				$channel->basic_publish($msg,$exchangeName,$routingKey);
				
				#Close connections
				$channel->close();
				$connection->close();
				
				$response = ["chat_message_id" => $chat->chat_message_id];
				return $response;
			} catch(Exception $e) {
				throw new \yii\web\HttpException(422, 'error');
			}
						
		} else{
			Yii::$app->response->statusCode = 422;
			$response = ["errors" => $chat->getErrors()];
			return $response;
		}
    }
	
	private function generateMessageId($from_id,$to_id){
		$time = time();
		$secs = microtime(true); 
		$milliseconds = number_format($secs,2); 
		$milliseconds = (integer)$milliseconds;
		
		if($from_id<$to_id){
			$id = $time.$milliseconds.$from_id.$to_id;
		} else{
			$id = $time.$milliseconds.$to_id.$from_id;
		}
		return (integer)$id;
	}
		
}