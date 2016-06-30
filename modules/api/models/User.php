<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\modules\api\models;

use dektrium\user\Finder;
use dektrium\user\helpers\Password;
use dektrium\user\Mailer;
use dektrium\user\Module;
use dektrium\user\traits\ModuleTrait;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use dektrium\user\models\Token;

/**
 * User ActiveRecord model.
 *
 * @property bool    $isAdmin
 * @property bool    $isBlocked
 * @property bool    $isConfirmed
 *
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $unconfirmed_email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $flags
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile   $profile
 *
 * Dependencies:
 * @property-read Finder $finder
 * @property-read Module $module
 * @property-read Mailer $mailer
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends \dektrium\user\models\User
{
	public 	$user_image = 'http://lorempixel.com/100/100/people/';

    public function fields()
    {
		$fields = parent::fields();
		$fields['user_image'] = 'user_image';
		return $fields;
    }
	
	public function extraFields()
	{
		return ['profile'];
	}
	
	public function init(){
		//Create handler for event triggered by User class after successful registration
		$this->on(self::AFTER_REGISTER, [$this,'registeredCallback']);
	}
	
	public function registeredCallback(){
		$this->rabbitmq_exchange_name 	= "chat.message.exchange";
		$this->rabbitmq_queue_name 		= "chat.message.user.".$this->id;
		$this->rabbitmq_routing_key 	= "chat.message.user.".$this->id;
		$this->save();
	}
	
	
	public function auth($username, $password)
	{
		if(($username!="") && ($password!=""))
		{
			if(filter_var($username, FILTER_VALIDATE_EMAIL)){
				$user = \app\models\User::findOne(['email' => $username]);
			} else{
				$user = \app\models\User::findOne(['username' => $username]);
			}
			
			if ($user === null || !\dektrium\user\helpers\Password::validate($password, $user->password_hash)) {
				throw new \yii\web\HttpException(404, 'Invalid login or password');
			} else{
				return $user;
			}
		} else{
			throw new \yii\web\HttpException(404, 'Invalid login or password');
		}
	}
	
}
