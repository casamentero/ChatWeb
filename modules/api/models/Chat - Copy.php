<?php

namespace app\modules\api\models;

use Yii;
use app\modules\api\models\Languages;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "chat".
 *
 * @property string $id
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $languages_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $is_read
 *
 * @property User $from
 * @property User $to
 * @property Languages $languages
 * @property ChatTranslation[] $chatTranslations
 */
class Chat extends \yii\db\ActiveRecord
{
	public $message;
	public $language;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_id', 'to_id', 'languages_id'], 'required'],
            [['from_id', 'to_id', 'languages_id', 'created_at', 'updated_at', 'is_read'], 'integer'],
            [['from_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_id' => 'id']],
            [['to_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_id' => 'id']],
            [['languages_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['languages_id' => 'id']],
        ];
    }
	
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}	

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_id' => 'From ID',
            'to_id' => 'To ID',
            'languages_id' => 'Languages ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_read' => 'Is Read',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFrom()
    {
        return $this->hasOne(User::className(), ['id' => 'from_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTo()
    {
        return $this->hasOne(User::className(), ['id' => 'to_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasOne(Languages::className(), ['id' => 'languages_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatTranslations()
    {
        return $this->hasMany(ChatTranslation::className(), ['chat_id' => 'id']);
    }
	
    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
			$language = Languages::find()->where(['language_code'=>$this->language])->one();
			$this->languages_id = $language->id;
            return true;
        } else {
            return false;
        }
    }	
	
}
