<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\AuthItem;
use \backend\models\LoginForm;
use function Sodium\compare;
use yii\helpers\ArrayHelper;

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {

        return $this->render('index');

    }

    public function actionShow()
    {
        //拿到数据
        $model = Admin::find()->all();

        //数据分配  引入视图
        return $this->render('show',compact('model'));

    }

    /**管理员登录
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        //创建模型
        $model = new LoginForm();

        //POST提交
        if (\Yii::$app->request->isPost) {

            //数据绑定
            $model->load(\Yii::$app->request->post());


            //后台验证
            if ($model->validate()) {

                $admin = Admin::findOne(['username'=>$model->username,'status'=>1]);

                //判断用户名
                if ($admin) {

                    //验证密码
                    if (\Yii::$app->security->validatePassword($model->password,$admin->password_hash)) {

                        //插件登录
                        \Yii::$app->user->login($admin,$model->rememberMe?3600*24*7:0);

                        \Yii::$app->session->setFlash('success','登录成功');

                        return $this->redirect(['show']);

                    }else{

                        //错误提示
                        $model->addError('password','密码不正确');
                    }

                }else{

                    //错误提示
                    $model->addError('username','帐号输入错误或权限不够');
                }

            }else{

                // TODO
                var_dump($model->errors);exit;
            }
        }



        //视图引入
        return $this->render('login',compact('model'));
    }


    /**管理员添加
     * @return string
     */
    public function actionAdd()
    {
        //创建模型对象
        $model =new Admin();
        //加载场景
        $model->setScenario('add');


        //判断post 后台验证
        if ( $model->load(\Yii::$app->request->post()) && $model->validate()) {
            //密码加密
            $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);

            //设置令牌
            $model->auth_key=\Yii::$app->security->generateRandomString();

            //保存
            if ($model->save()) {
                //提示信息
                \Yii::$app->session->setFlash('success','添加管理员成功');
                $this->redirect('show');
            }else{
                //TODO
                var_dump($model->errors);exit;
            }
        }

        return $this->render('add',compact('model'));

    }


    /**管理员编辑
     * @param $id
     * @return string
     */
    public function actionEdit($id)
    {
        //找到编辑对象
        $model =Admin::findOne($id);
        $password = $model->password_hash;
        $model->setScenario('edit');

        //判断post 后台验证
        if ( $model->load(\Yii::$app->request->post()) && $model->validate()) {

            //判断是否修改密码
            $model->password_hash=$model->password_hash?\Yii::$app->security->generatePasswordHash($model->password_hash):$password;

            //设置令牌
            $model->auth_key=\Yii::$app->security->generateRandomString();

            //保存
            if ($model->save()) {

                //提示信息
                \Yii::$app->session->setFlash('success','编辑成功');
                 return $this->redirect('show');
                }



            }


        $model->password_hash=null;
        return $this->render('add',compact('model'));

    }

    /**管理员退出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * 管理员删除
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDel($id)
    {
        //判断是否是当前用户 本登录用户不能删除
        if (\Yii::$app->user->identity->id==$id) {
            //提示信息
            \Yii::$app->session->setFlash('danger','不能删除本登录管理员！');
            return $this->redirect('show');
        }
        if (Admin::findOne($id)->delete()) {
            //提示信息
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect('show');
        }

    }
}
