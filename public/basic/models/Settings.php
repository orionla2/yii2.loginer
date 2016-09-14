<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property integer $defaultStatusComment
 * @property integer $defaultStatusUser
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['defaultStatusComment', 'defaultStatusUser'], 'required'],
            [['defaultStatusComment', 'defaultStatusUser'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'defaultStatusComment' => Yii::t('app', 'Default Status Comment'),
            'defaultStatusUser' => Yii::t('app', 'Default Status User'),
        ];
    }
}
