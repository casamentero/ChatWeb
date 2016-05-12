<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "languages".
 *
 * @property integer $id
 * @property string $language_name
 * @property string $language_code
 *
 * @property Chat[] $chats
 * @property ChatTranslation[] $chatTranslations
 */
class Languages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'language_name', 'language_code'], 'required'],
            [['id'], 'integer'],
            [['language_name'], 'string', 'max' => 20],
            [['language_code'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language_name' => 'Language Name',
            'language_code' => 'Language Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChats()
    {
        return $this->hasMany(Chat::className(), ['languages_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatTranslations()
    {
        return $this->hasMany(ChatTranslation::className(), ['languages_id' => 'id']);
    }
}
