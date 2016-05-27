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
use app\modules\api\models\User;
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
		return [
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
	}

	public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
       // unset($actions['index']);
        return $actions;
    }
	
	/*
	public function actionIndex()
	{
		$q = new yii\db\Query;
		$query = $q->select('user.*, profile.*')
		->from('user, profile')
		->where('user.id = profile.user_id');
		return new ActiveDataProvider([
		'query' => $query
		]);
	}
	*/	

    public function actionCreate(){
	
        // implement here your code
		//send(from_id,to_id,chat_message,languages_id,rabbitmq_exchange_name,rabbitmq_queue_name,rabbitmq_routing_key);
		
        $chat = Yii::createObject(Chat::className());
		
		$chat->from_id 				= Yii::$app->request->post('from_id');
		$chat->to_id 				= Yii::$app->request->post('to_id');
		$chat->chat_message 		= Yii::$app->request->post('chat_message');
		$chat->chat_message_id 		= $this->generateMessageId($chat->from_id,$chat->to_id);
		$chat->languages_id 		= Yii::$app->request->post('languages_id');
		
		//Chat message object
		$message = new \stdClass;
		$message->from_id 			= $chat->from_id;
		$message->to_id 			= $chat->to_id;
		$message->chat_message 		= $chat->chat_message;
		$message->chat_message_id 	= $chat->chat_message_id;
		$message->languages_id 		= $chat->languages_id;
		$message->created_at 		= time();
		
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
				
				$message = new \stdClass;
				$message->chat_message_id 	= $chat->chat_message_id;
				$message = Json::encode($message);
				throw new \yii\web\HttpException(201, $message);
			} catch(Exception $e) {
				throw new \yii\web\HttpException(422, 'error');
			}
			
			/*
			if($chat->save()){
				throw new \yii\web\HttpException(201, 'Message created successfully.');
			} else{
				throw new \yii\web\HttpException(422, 'error');
			}
			*/
			
		} else{
			$errors = $chat->getErrors();
			$errors = Json::encode($errors);
			throw new \yii\web\HttpException(422,$errors);
		}
    }
	
	private function generateMessageId($from_id,$to_id){
		$time = time();
		$id = rand(1,100).$from_id.$to_id.$time;
		return $id;
	}
	
}