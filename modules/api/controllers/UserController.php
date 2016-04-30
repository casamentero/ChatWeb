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
use app\modules\api\models\RegistrationForm;
use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class UserController extends ActiveController
{
	public $modelClass = 'app\modules\api\models\User';
	
public function behaviors()
{
    return [
        [
            'class' => 'yii\filters\ContentNegotiator',
            'only' => ['view', 'index'],  // in a controller
            // if in a module, use the following IDs for user actions
            // 'only' => ['user/view', 'user/index']
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

        $model = Yii::createObject(RegistrationForm::className());
        $user = Yii::createObject(User::className());
		
		$model->email 	 	= Yii::$app->request->post('email');
		$model->password 	= Yii::$app->request->post('password');
		$model->first_name 	= Yii::$app->request->post('first_name');
		$model->last_name 	= Yii::$app->request->post('last_name');
		
		//Generate username from user model by passing email
		$user->email = $model->email;
		$user->generateUsername();
		$model->username = $user->username;

        if($model->validate()) {
			if($model->register()){
				throw new \yii\web\HttpException(201, 'Account created successfully.');
			} else{
				throw new \yii\web\HttpException(422, '');
			}
		} else{
			$errors = $model->getErrors();
			$errors = Json::encode($errors);
			throw new \yii\web\HttpException(422,$errors);
		}
    }
}