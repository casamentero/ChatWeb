<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models\user;

use dektrium\user\traits\ModuleTrait;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $name
 * @property string  $public_email
 * @property string  $gravatar_email
 * @property string  $gravatar_id
 * @property string  $location
 * @property string  $website
 * @property string  $bio
 * @property User    $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class Profile extends \dektrium\user\models\Profile
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'first_name'        => ['first_name', 'safe'],
            'last_name'         => ['last_name', 'safe'],
            'languages_id'      => ['languages_id', 'safe'],
            'gender'         	=> ['gender', 'safe'],
            'appellido_paterno' => ['appellido_paterno', 'safe'],
            'appellido_mother'  => ['appellido_mother', 'safe'],
            'places_country_id' => ['places_country_id', 'safe'],
            'places_state_id'   => ['places_state_id', 'safe'],
            'address'         	=> ['address', 'safe'],
            'dob'         	   	=> ['dob', 'safe'],
            'height_ft'       	=> ['height_ft', 'safe'],
            'weight_kg'       	=> ['weight_kg', 'safe'],
            'profession'      	=> ['profession', 'safe'],
            'education'       	=> ['education', 'safe'],
            'description'     	=> ['description', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'           => \Yii::t('user', 'Name'),
            'public_email'   => \Yii::t('user', 'Email (public)'),
            'gravatar_email' => \Yii::t('user', 'Gravatar email'),
            'location'       => \Yii::t('user', 'Location'),
            'website'        => \Yii::t('user', 'Website'),
        ];
    }
}
