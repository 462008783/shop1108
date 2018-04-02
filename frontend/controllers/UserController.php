<?php

namespace frontend\controllers;

use frontend\components\ShopCart;
use frontend\models\LoginForm;
use frontend\models\User;
use Mrgoon\AliSms\AliSms;
use yii\helpers\Json;

class UserController extends \yii\web\Controller
{
    //验证码
    public function actions()
    {
        return [

            'code' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 3,
                'maxLength' => 3,
            ],
            'codeOne' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 3,
                'maxLength' => 3,
            ],
        ];
    }


//    public function actionIndex()
//    {
//        return $this->render('index');
//    }

    /**
     * 注册
     * @return string
     */
    public function actionReg()
    {
        //模型对象
        $user =new User();

        $user->setScenario('reg');
        //绑定数据
        if ($user->load(\Yii::$app->request->post())) {

            //后台验证
            if ($user->validate()) {

                $user->auth_key=\Yii::$app->security->generateRandomString();//令牌
                $user->password_hash =\Yii::$app->security->generatePasswordHash($user->password);//密码

                //保存数据
                if ($user->save(false)) {
                    $result = [
                      'status'=>1,
                      'msg'=>'注册成功',
                      'data'=>'',
                    ];

                    return Json::encode($result);
                }

            }else{
                $result = [
                    'status'=>0,
                    'msg'=>'注册失败',
                    'data'=>$user->errors,
                ];

                return Json::encode($result);
            }
        }

        //视图引入
        return $this->render('reg');
    }


    /**
     * 短信验证码生成
     * @param $mobile
     * @return int
     */
    public function actionSendSms($mobile)
    {
        //生成随机六位验证码
        $code = rand(100000,999999);

        //发送验证码给mobile
        $config = [
            'access_key' => 'LTAIh3jkg03yFPzJ',
            'access_secret' => 'Ttemkph2ZXOr9dBiWOdI9raPlndwRo',
            'sign_name' => '鹍One',//签名
        ];

        $aliSms = new AliSms();//短信发送对象
        $response = $aliSms->sendSms($mobile, 'SMS_128870342', ['code'=>$code], $config);

        //判断
        if ($response->Message=="OK") {
            //把code保存到session中
            \Yii::$app->session->set("tel_".$mobile,$code);


            //测试返回
            return $code;
        }else{

            var_dump($response->Message);
        }

   }


    /**
     * 用户登录
     * @return string
     */
    public function actionLogin()
    {
        //创建表=单模型
       $model =new LoginForm();

       //POST提交
        if (\Yii::$app->request->isPost) {
            //数据绑定
            $model->load(\Yii::$app->request->post());
            //后台验证
            if ($model->validate()) {
                //找到对象
                $user = User::findOne(['username'=>$model->username,'status'=>1]);
                //判断用户是否正确
                if ($user) {
                    //密码验证
                    if (\Yii::$app->security->validatePassword($model->password,$user->password_hash)) {

                        //插件登录
                        \Yii::$app->user->login($user,$model->rememberMe?3600*24*7:0);

                        //同步购物车Cookie数据
                        (new ShopCart())->dbSyn()->flush()->save();

                        $user->login_time = time();
                        $user->login_ip = ip2long(\Yii::$app->request->userIP);
                        if ($user->save(false)) {
                            $result = [
                                'status'=>1,
                                'msg'=>'登录成功',
                                'data'=>'',
                            ];
                            return Json::encode($result);
                        }
                    }else{
                        //错误提示
                        $result = [
                            'status'=>-1,
                            'msg'=>'密码错误',
                            'data'=>['password'=>["密码错误！"]],
                        ];
                        return Json::encode($result);
                    }

                }else{
                    //错误提示
                    $result = [
                        'status'=>-1,
                        'msg'=>'用户名错误',
                        'data'=>['username'=>["用户名错误！"]],
                    ];
                    return Json::encode($result);
                }

            }else{
                //错误提示
                $result = [
                    'status'=>-1,
                    'msg'=>'验证错误',
                    'data'=>$model->errors,
                ];
                return Json::encode($result);
            }
        }

        return $this->render('login');
   }


    /**管理员退出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->redirect('/index/index/');
    }

}
