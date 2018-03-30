<?php
/**
 * Created by PhpStorm.
 * User: 秦鹍
 * Date: 2018/3/21
 * Time: 15:42
 */

namespace frontend\models;


use yii\base\Model;

class LoginForm extends Model
{

    //
    public $username;
    public $password;
    public $rememberMe;//记住密码\
    public $checkcode;

    public function rules()
    {
        return [
            [['username', 'password','checkcode'], 'required'],
            [['rememberMe'],'safe'],

            [['checkcode'],'captcha','captchaAction' => 'user/code'],//验证码
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '帐号',
            'password' => '密码',
            'rememberMe'=>'记住密码',
            'checkcode' => '验证码'
        ];
    }
}