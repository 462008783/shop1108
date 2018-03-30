<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $mobile 手机号码
 * @property int $login_time 登录时间
 * @property string $login_ip
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**自动添加时间
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                // 'value' => new Expression('NOW()'),
            ],
        ];
    }


    //设置属性
    public $password;//密码
    public $regPassword;//确认密码
    public $checkcode;//验证码
    public $captcha;//短信验证码
    public $rememberMe;//是否记住

    /**场景
     * @return array
     */
//    public function scenarios()
//    {
//
//        $scenario = parent::scenarios();
//        $scenario['login'] = ['username','checkcode','password'];
//        $scenario['reg'] = ['username','password','regPassword', 'email','mobile','checkcode','captcha'];
//        return $scenario;
//
//
//    }

    /**规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','checkcode','password_hash'],'required'],
            [['username','password','regPassword', 'email','mobile','checkcode'], 'required'],//不为空
            [['username'], 'unique','on'=>'reg'],//唯一
            [['mobile'], 'match','pattern'=>'/(13|14|15|17|18|19)[0-9]{9}/','message'=>'您输入电话格式有误！'],//电话规则
            [['email'], 'email','on'=>'reg'],//邮箱规则
            [['regPassword'],'compare','compareAttribute' => 'password'],//确认密码
            [['captcha'],'validateCaptcha','on'=>'reg'],//自定义规则
            [['checkcode'],'captcha','captchaAction' => 'user/code'],//验证码

            [['username','checkcode','password'],'required'],
            [['rememberMe'],'safe','on'=>'login']
        ];
    }




    //自定义规则方法
    public function validateCaptcha($attribute,$params)
    {
        //通过手机号取出session中的方法
        $oldCode = Yii::$app->session->get('tel_'.$this->mobile);

        //将保存好的session拿出来判断
        if ($this->captcha!=$oldCode) {
            $this->addError($attribute,'验证码错误！重新输入！！');
        }
        
    }


    /**
     * label
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password' => '密码',
            'regPassword' => '确认密码',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'mobile' => '手机号码',
            'login_time' => '最后登录时间',
            'login_ip' => '最后登录IP',
            'checkcode'=>'验证码'
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        //id得到用户对象
        return self::findOne(['id'=>$id,'status'=>1]);
        // TODO: Implement findIdentity() method.
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        //返回用户id
        return $this->id;
        // TODO: Implement getId() method.
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {

        //令牌返回
        return $this->auth_key;
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        //令牌验证
        return $this->auth_key===$authKey;
        // TODO: Implement validateAuthKey() method.
    }
}
