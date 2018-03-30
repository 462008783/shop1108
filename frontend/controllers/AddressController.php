<?php

namespace frontend\controllers;

use frontend\models\Address;
use yii\helpers\Json;

class AddressController extends \yii\web\Controller
{
    public function actionIndex()
    {
        //找到所有数据
         $model = Address::find()->where(['user_id'=>\Yii::$app->user->identity->getId()])->all();



        return $this->render('index',compact('model'));
    }


    public function actionAdd()
    {
        //创建数据对象
        $model=new Address();

        //POST提交
        if (\Yii::$app->request->isPost) {

            //绑定数据
            $model->load(\Yii::$app->request->post());

            //后台验证
            if ($model->validate()) {

                //保存当前用户id
                $model->user_id=\Yii::$app->user->identity->getId();

                //判断状态
                if ($model->status!=null){
                    Address::updateAll(['status'=>0],['user_id'=>$model->user_id]);
                    $model->status=1;
                }else{
                    $model->status=0;
                }


                //数据保存
                if ($model->save()) {
                    $result = [
                        'status'=>1,
                        'msg'=>'保存成功',
                        'data'=>"",
                    ];
                    return Json::encode($result);
                }
            }else{
                $result = [
                    'status'=>0,
                    'msg'=>'未保存成功',
                    'data'=>$model->errors,
                ];
                return Json::encode($result);
            }


        }
        return $this->render('index',compact('model'));
    }


    /**
     *
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        if (Address::findOne(['id'=>$id,'user_id'=>\Yii::$app->user->id])->delete()) {
            $result = [
                'status'=>1,
                'msg'=>'删除成功',
            ];
            return Json::encode($result);
        }

    }

}
