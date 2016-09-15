<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $hash
 * @property integer $created
 * @property string $email
 * @property string $ban
 * @property string $role
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['created'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['email', 'email'],
            [['username', 'password', 'hash', 'email', 'ban', 'role'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'hash' => Yii::t('app', 'Hash'),
            'created' => Yii::t('app', 'Created'),
            'email' => Yii::t('app', 'Email'),
            'ban' => Yii::t('app', 'Ban'),
            'role' => Yii::t('app', 'Role'),
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->isNewRecord){
                $settings = Settings::find()->where(array('id' => 1))->one();
                if ($settings->defaultStatusUser == 0) {
                    $this->ban = 0;
                } else {
                    $this->ban = 1;
                }
                $this->created = time();
                $this->role = 'user';
                $this->password = md5('sa1t' . $this->password . '4_pr0tect10n');
                $this->hash = md5($this->username . $this->created);
                $message = "Please follow the link for activation http://yii2.loginer/site/confirm?conf={$this->hash}";
                Yii::$app->mailer->compose()
                    ->setTo($this->email)
                    ->setFrom([Yii::$app->params['adminEmail'] => 'noReply'])
                    ->setSubject('confirm your account')
                    ->setTextBody($message)
                    ->send();
            }
            
            return true;
        } else {
            return false;
        }
    }

    public function getAuthKey() {
        return $this->hash;
    }

    public function getId() {
        return $this->id;
    }

    public function validateAuthKey($authKey) {
        return $this->hash === $authKey;
    }

    public static function findIdentity($id) {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        throw new \yii\base\NotSupportedException();
    }
    
    public static function findByUserName($username){
        return self::findOne(array('username' => $username));
    }
    
    public function validatePassword($password){
        //xdebug_break();
        $text = md5('sa1t' . $password . '4_pr0tect10n');
        echo '<br><br><br>';
        echo "$text : $this->password";
        return md5('sa1t' . $password . '4_pr0tect10n') === $this->password;
    }
    public function activation($hash){
        $model = $this->find()->where(array('hash' => "$hash"))->one();
        $flag = false;
        if($model){
            $model->load($model->ban = '0',$model->hash = '');
            if($model->validate() && $model->save()){
                $flag = true;
            }
        }
        return $flag;
    }
}
