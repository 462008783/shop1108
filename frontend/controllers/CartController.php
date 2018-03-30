<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Cart;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Cookie;

class CartController extends \yii\web\Controller
{


    /**
     * 加入购物车
     * @param $id
     * @param $amount
     * @return \yii\web\Response
     */
    public function actionAddCart($id,$amount)
    {

        //判断是否登录
        if (\Yii::$app->user->isGuest) {
            //未登录存入cookie
            //得到cookie对象
            $getCookie = \Yii::$app->request->cookies;

            //找到原来购物车的数据
            $cart = $getCookie->getValue('cart',[]);

            //判断当前加入商品ID是否存在
            if (array_key_exists($id,$cart)) {
                //已存在
                $cart[$id]+=$amount;
            }else{
                //新的
                $cart[$id]=(int)$amount;
            }

            //创建设置cookie对象
            $setCookie =  \Yii::$app->response->cookies;

            //创建一个cookie对象 存入数据
            $cookie = new Cookie([
               'name'=> 'cart',
                'value' => $cart,
            ]);

            //通过设置cookie对象添加一个cookie
            $setCookie->add($cookie);

        }else{
            //登录存入数据表
            //判断是否存在相同商品
            $cart_good=Cart::find()->where(['goods_id'=>$id,'user_id'=>\Yii::$app->user->id])->one();
            if ($cart_good){
                //存在相同商品
                $cart_good->amount+=$amount;
                //保存
                $cart_good->save();
            }else{
                //创建数据模型对象
                $cart = new Cart();
                //赋值
                $cart->user_id=\Yii::$app->user->id;
                $cart->goods_id=$id;
                $cart->amount=$amount;
                //保存
                $cart->save();
            }
        }

        return $this->redirect(['cart/list-cart']);
    }


    /**
     * 购物车列表
     * @return string
     */
    public function actionListCart()
    {
        //判断登录状态
        if (\Yii::$app->user->isGuest) {
            //未登录
            //从cookie中取出购物车数据数据
            $cart = \Yii::$app->request->cookies->getValue('cart',[]);

            //取出cookie中所有的数据key
            $goodKey = array_keys($cart);

            //找到cookie中购物车所有商品
            $goods = Goods::find()->where(['in','id',$goodKey])->all();

        }else{
            //已登录
            //从数据库拿到购物车数据
            $cart1 = Cart::find()->where(['user_id'=>\Yii::$app->user->id])->asArray()->all();
            $cart=ArrayHelper::map($cart1,'goods_id','amount');

            //获取对应的所有数据id
            $goods_id = array_keys($cart);
            //找出购物车中的商品
            $goods = Goods::find()->where(['in','id',$goods_id])->all();

        }

        return $this->render('list',compact('goods','cart'));
    }


    /**
     * 购物车数量
     * @param $id
     * @param $amount
     */
    public function actionUpdateCart($id,$amount)
    {
        //判断登录状态
        if (\Yii::$app->user->isGuest) {
            //未登录
            //从cookie中取出购物车数据数据
            $cart = \Yii::$app->request->cookies->getValue('cart',[]);

            //修改对应商品ID数量
            $cart[$id]=$amount;

            //创建设置cookie对象
            $setCookie =  \Yii::$app->response->cookies;

            //创建一个cookie对象 存入数据
            $cookie = new Cookie([
                'name'=> 'cart',
                'value' => $cart,
            ]);

            //通过设置cookie对象添加一个cookie
            $setCookie->add($cookie);

        }else{
            //已登录
            //找到他 改变他
            $cart=Cart::find()->where(['goods_id'=>$id,'user_id'=>\Yii::$app->user->id])->one();
            $cart->amount=$amount;
            //保存
            $cart->save();
        }

    }


    /**
     * 删除
     * @param $id
     * @return string
     */
    public function actionDelCart($id)
    {
        //判断登录状态
        if (\Yii::$app->user->isGuest) {
            //未登录
            //从cookie中取出购物车数据数据
            $cart = \Yii::$app->request->cookies->getValue('cart',[]);

            //删除cookie数据
            unset($cart[$id]);

            //创建设置cookie对象
            $setCookie =  \Yii::$app->response->cookies;

            //创建一个cookie对象 存入数据
            $cookie = new Cookie([
                'name'=> 'cart',
                'value' => $cart,
            ]);

            //通过设置cookie对象添加一个cookie
            $setCookie->add($cookie);

            return Json::encode([
               'status'=>1,
                'msg'=>'删除成功',
            ]);
        }else{
            //已登录
            if (Cart::findOne(['goods_id'=>$id,'user_id'=>\Yii::$app->user->id])->delete()) {
                return Json::encode([
                    'status'=>1,
                    'msg'=>'删除成功',
                ]);
            }

        }
    }

    public function actionText()
    {
        $getCookie = \Yii::$app->request->cookies;

        var_dump($getCookie->getValue('cart'));

    }
}
