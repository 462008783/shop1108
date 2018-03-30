<?php

namespace frontend\controllers;

use backend\models\Goods;

class GoodsController extends \yii\web\Controller
{


    public function actionGoods($id)
    {
        //根据id找到商品
        $goods = Goods::findOne($id);

        return $this->render('goods',compact('goods'));
    }
}
