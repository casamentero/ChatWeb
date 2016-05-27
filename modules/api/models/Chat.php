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
 * @property string $chat_message
 * @property integer $languages_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $is_read
 *
 * @property Languages $languages
 * @property User $from
 * @property User $to
 */
class Chat extends \yii\db\ActiveRecord
{

	public $rabbitmq_exchange_name 	= "";
	public $rabbitmq_queue_name 	= "";
	public $rabbitmq_routing_key 	= "";

	public function extraFields()
	{
		return [
			'from'=>'from',
			'to'=>'to'
		];
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
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
    public function rules()
    {
        return [
            [['from_id', 'to_id', 'chat_message', 'chat_message_id', 'languages_id'], 'required'],
            [['from_id', 'to_id', 'chat_message_id', 'languages_id', 'created_at', 'updated_at', 'is_read'], 'integer'],
            [['chat_message'], 'string'],
            [['languages_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['languages_id' => 'id']],
            [['from_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_id' => 'id']],
            [['to_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_id' => 'id']],
			[['rabbitmq_exchange_name','rabbitmq_queue_name','rabbitmq_routing_key'],'safe'],
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
            'chat_message' => 'Chat Message',
            'chat_message_id' => 'Chat Message ID',
            'languages_id' => 'Languages ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_read' => 'Is Read',
        ];
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

	
}
