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
		//send(from_id,to_id,message,language);
		
        $chat = Yii::createObject(Chat::className());
		
		$chat->from_id 		= Yii::$app->request->post('from_id');
		$chat->to_id 			= Yii::$app->request->post('to_id');
		$chat->message 		= Yii::$app->request->post('message');
		$chat->language 		= Yii::$app->request->post('language');
		
		
		if($chat->validate()){
		
			# Translate language
			/*
			switch($chat->language){
				case 'en':
					$translation = Yii::$app->translate->translate('en', 'pa', $chat->message);
					if(isset($translation['data'])){
						$message_en = $chat->message;
						$message_es = $translation['data']['translations'][0]['translatedText'];
					}
				break;
				case 'es':
					$translation = Yii::$app->translate->translate('es', 'en', $chat->message);
					if(isset($translation['data'])){
						$message_en = $translation['data']['translations'][0]['translatedText'];
						$message_es = $chat->message;
					}
				break;
			}
			*/
			if($chat->save()){
				# Save English version
				$chatTranslation = new ChatTranslation();
				$chatTranslation->chat_id = $chat->id;
				$chatTranslation->languages_id = 1;
				$chatTranslation->chat_message_translation = $message_en;
				$chatTranslation->save();
				# Save Spanish version
				$chatTranslation = new ChatTranslation();
				$chatTranslation->chat_id = $chat->id;
				$chatTranslation->languages_id = 2;
				$chatTranslation->chat_message_translation = $message_es;
				$chatTranslation->save();
				throw new \yii\web\HttpException(201, 'Message created successfully.');
			} else{
				throw new \yii\web\HttpException(422, 'error');
			}
		} else{
			$errors = $chat->getErrors();
			$errors = Json::encode($errors);
			throw new \yii\web\HttpException(422,$errors);
		}
    }
}