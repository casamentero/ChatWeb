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
use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class ProfileController extends ActiveController
{
	public $modelClass = 'app\modules\api\models\Profile';
	
	public function behaviors()
	{
		$behaviors = parent::behaviors();
	
		$arr = [
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['create','index','view','update','authenticate'],  // in a controller
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
        unset($actions['update']);
        return $actions;
    }
	
	#PATCH /profiles/3
	/*
	curl -X PUT -d '{"first_name":"Jack","last_name":"Sparrow"}' "http://localhost/ChatWeb/web/api/profiles/3"
	*/
	//http://api.chatndate.com/web/api/profiles/1
	
    public function actionUpdate($id){
        // implement here your code

        $model = Yii::createObject(Profile::className());
		$profile = $model->findOne($id);
		
		$jsonRawBody = Yii::$app->request->getRawBody();
		$param = Json::decode($jsonRawBody);

		$profile->attributes = $param;
		
        if($profile->validate()) {
			if($profile->save()){
				throw new \yii\web\HttpException(200, 'Profile updated successfully.');
			} else{
				throw new \yii\web\HttpException(422, 'error');
			}
		} else{
			$errors = $profile->getErrors();
			$errors = Json::encode($errors);
			throw new \yii\web\HttpException(422,$errors);
		}
	}	
	
	
}