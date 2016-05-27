<?php
namespace app\commands;

use yii\console\Controller;

use yii\helpers\Json;
use yii\web\Response;
use PhpAmqpLib\Message\AMQPMessage;
use app\modules\api\models\Chat;

/**
* Webserver related functions
*/
class ChatworkerController extends Controller{
    public function actionIndex()
    {
		set_time_limit(0);

		$queueName 		= "chat.message.user.db";
		$exchangeName 	= 'chat.message.exchange';
		$routingKey 	= 'chat.message.user.#';
		
		$connection = \Yii::$app->amqp->getConnection();
		$channel = $connection->channel();
		
		$channel->exchange_declare($exchangeName, 'topic', $passive=false, $durable=true, $auto_delete=false);
		$channel->queue_declare($queueName, $passive=false, $durable=true, $exclusive=false, $auto_delete=false);
		$channel->queue_bind($queueName, $exchangeName, $routingKey);

		$callback = function($msg){
			$body = JSON::decode($msg->body);
			$chat = new Chat();
			$chat->from_id 			= $body['from_id'];
			$chat->to_id 			= $body['to_id'];
			$chat->chat_message 	= $body['chat_message'];
			$chat->chat_message_id 	= $body['chat_message_id'];
			$chat->languages_id 	= $body['languages_id'];
			$chat->created_at 		= $body['created_at'];
			$chat->save();
		};

		$channel->basic_consume($queueName, '', false, true, $exclusive=false, false, $callback);

		while(count($channel->callbacks)) {
			$channel->wait();
		}
		
		#Close connections
		$channel->close();
		$connection->close();
    }

}

?>