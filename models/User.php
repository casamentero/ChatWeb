<?php

namespace app\models;

use dektrium\user\models\Token;

class User extends \dektrium\user\models\User
{
	
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
		/*
		echo base64_encode('quafpgeAt9EXMd7aRV73JNHd3bYLnaHn:');
		*/
		$token = Token::find()->where(['code'=>$token])->one();
		
		if($token!==null){
			$user = \app\modules\api\models\User::find()->where(['id'=>$token->user_id])->one();
			return $user;
		} else{
			return null;
		}
        return null;
    }
}
