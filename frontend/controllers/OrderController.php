<?php

namespace frontend\controllers;


use backend\models\Goods;
use backend\models\Order;
use backend\models\OrderDelivery;
use backend\models\OrderGoods;
use backend\models\OrderPay;
use frontend\models\Address;
use frontend\models\Cart;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use EasyWeChat\Foundation\Application;
use yii\helpers\Url;
use Endroid\QrCode\QrCode;

class OrderController extends \yii\web\Controller
{

    /**
     * 订单显示
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        //判断是否登陆
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['user/login','url'=>'/order/index']);
        }
        //收货地址
        //当前用户id
        $userId = \Yii::$app->user->id;
        //用户收货地址
        $address = Address::find()->where(['user_id'=>$userId])->orderBy('status desc')->all();

        //配送方式
        $delivery = OrderDelivery::find()->all();

        //支付方式
        $pay = OrderPay::find()->all();

        //购物车商品
        //从数据库拿到购物车数据
        $cart = Cart::find()->where(['user_id'=>\Yii::$app->user->id])->asArray()->all();
        $cart=ArrayHelper::map($cart,'goods_id','amount');
        //获取对应的所有数据id
        $goods_id = array_keys($cart);
        //找出购物车中的商品
        $goods = Goods::find()->where(['in','id',$goods_id])->all();


            return $this->render('index',compact('address','goods','cart','delivery','pay'));

    }

    /**
     * 提交订单
     * @return string
     */
    public function actionAdd()
    {
        $userId = \Yii::$app->user->id;

        //购物车商品
        //从数据库拿到购物车数据
        $cart = Cart::find()->where(['user_id'=>\Yii::$app->user->id])->asArray()->all();
        $cart=ArrayHelper::map($cart,'goods_id','amount');
        //获取对应的所有数据id
        $goods_id = array_keys($cart);
        //找出购物车中的商品
        $goods = Goods::find()->where(['in','id',$goods_id])->all();

        $shopPrice = "";//总价
        $shopNum="";//数量
        foreach ($goods as $value){
            $shopPrice = $value->shop_price*$cart[$value->id];
            $shopNum = $cart[$value->id];
        }
//        $shopPrice = number_format($shopPrice,2);
        $request = \Yii::$app->request;
        if ($request->isPost) {

            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();//开启事务

            try {

            //创建订单表对象
            $order = new Order();

            //取出数据
            //收货人
            $address = $request->post('address');
            $address= Address::find()->where(['id'=>$address,'user_id'=>$userId])->one();

            //配送方式
            $delivery = $request->post('delivery');
//            var_dump(121);exit;
            $delivery = OrderDelivery::findOne($delivery);

            //支付方式
            $pay = $request->post('pay');
            $pay = OrderPay::findOne($pay);

            //赋值
            $order->user_id=$userId;

            //地址信息
            $order->name = $address->name;
            $order->province = $address->province;
            $order->city = $address->city;
            $order->county = $address->county;
            $order->address = $address->address;
            $order->mobile = $address->mobile;

            //配送方式
            $order->delivery_id =$delivery->id;
            $order->delivery_name =$delivery->name;
            $order->delivery_price =$delivery->money;

            //支付方式
            $order->pay_id =$pay->id;
            $order->pay_name = $pay->name;

            //总金额
            $order->price =$shopPrice + $order->delivery_price;

            //订单号
            $order->trade_no = date('ymdHis').rand(1000,9999);
            $orderId = $order->id;

            //订单状态
            $order->order_status = 1 ;

            //创建时间
            $order->created_at = time();

            //保存
            if ($order->save()) {
                //循环商品入商品详情表
                foreach ($goods as $good){
                    //判断当前商品库存
                    //找到当前商品
                    $curGood =  Goods::findOne($good->id);
                    //判断库存
                    if ($cart[$good->id]>$curGood->stock) {
                        //抛出异常
//                        exit("库存不足");
                        throw new Exception("库存不足");
                    }

                    //订单商品表
                    //赋值
                    $orderGoods = new OrderGoods();
                    $orderGoods->goods_id= $good->id;
                    $orderGoods->order_id = $order->id;
                    $orderGoods->goods_name = $good->name;
                    $orderGoods->amount = $cart[$good->id];
                    $orderGoods->logo = $good->logo;
                    $orderGoods->price =$shopPrice;
                    $orderGoods->total_price = $good->shop_price*$orderGoods->amount;

                    //保存
                    if ($orderGoods->save()) {
                        //减少商品库存
                        $curGood->stock = $curGood->stock-$cart[$good->id];
                        $curGood->save(false);
                    }
                }
            }
            //清空购物车
            Cart::deleteAll(['user_id'=>$userId]);




                $transaction->commit();//提交事务

                return Json::encode([
                    'status'=>1,
                    'msg'=>'订单提交成功',
                    'orderId'=>$order->id

,                ]);
            } catch(Exception $e) {

                $transaction->rollBack();//事务回滚

                return Json::encode([
                    'status'=>0,
                    'msg'=>$e ->getMessage(),
                ]);
            }
        };
    }

    /**
     * 订单完成
     * @param $id 订单id
     * @return string
     */
    public function actionList($id)
    {
        //查出当前订单号
        $order = Order::findOne($id);

        return $this->render('list',compact('order'));
    }


    /**
     * 返回二维码
     * @param $id
     */
    public function actionWx($id)
    {
        //通过id找到对象
        $order = Order::findOne($id);

        //配置
        $options = \Yii::$app->params["weixin"];

        //创建操作微信对象
        $app = new Application($options);

        //通过$app得到脂肪油对象
        $payment = $app->payment;

        //订单详情信息
        $attributes = [
            'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...
            'body'             => 'One鹍',//标题
            'detail'           => OrderGoods::findOne(['order_id'=>$order->id])->goods_name,//
            'out_trade_no'     => $order->trade_no,//订单编号
            'total_fee'        => $order->price*100, // 单位：分
            'notify_url'       =>Url::to(['/order/notify'],true), // 支付结果通知网址，如果不设置则会使用配置里的默认地址 异步通知地址
            //'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];


        $order = new \EasyWeChat\Payment\Order($attributes);


        //统一订单编号
        $result = $payment->prepare($order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){


            $qrCode = new QrCode($result->code_url);

            header('Content-Type: '.$qrCode->getContentType());
            echo $qrCode->writeString();
        }
    }


    /**
     * 微信异步通信地址
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function actionNotify()
    {
        //配置
        $options = \Yii::$app->params["weixin"];

        //创建操作微信对象
        $app = new Application($options);

        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            //$order = 查询订单($notify->out_trade_no);
            $order = Order::findOne(['trade_no'=>$notify->out_trade_no]);

            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->order_status!=1) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }

            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->order_status = 2;
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
        });

        return $response;
    }
}
