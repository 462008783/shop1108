<?php
/**
 * Created by PhpStorm.
 * User: 秦鹍
 * Date: 2018/3/31
 * Time: 11:51
 */

namespace frontend\components;


use backend\models\Goods;
use frontend\models\Cart;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

class ShopCart extends Component
{

    //静态属性
    private $cart;

    public function __construct(array $config = [])
    {
        //得到cookie对象
        $getCookie = \Yii::$app->request->cookies;

        //找到原来购物车的数据
        $this->cart = $getCookie->getValue('cart',[]);
        parent::__construct($config);
    }

    //添加
    public function add($id,$num){
        //判断当前加入商品ID是否存在
        if (array_key_exists($id,$this->cart)) {
            //已存在
            $this->cart[$id]+=$num;
        }else{
            //新的
            $this->cart[$id]=(int)$num;
        }

        return $this;
    }

    //列表
    public function show(){
        //从cookie中取出购物车数据数据
        return $this->cart;
    }

    //修改
    public function update($id,$amount){

        //修改对应商品ID数量
        $this->cart[$id]=$amount;
        return $this;
    }

    //删除
    public function del($id){
        unset($this->cart[$id]);
        return $this;
    }

    //同步
    public function dbSyn(){
        //获取当前用户id
        $userId = \Yii::$app->user->id;

        //循环添加到数据库
        foreach ($this->cart as $goodsId=>$num){

            //判断当前用户及当前商品是否存在
            $cartDb = Cart::findOne(['goods_id'=>$goodsId ,'user_id'=>$userId]);
            if ($cartDb) {
                //存在增加
                $cartDb->amount+=$num;
            }else{
                //不存在新增
                $cartDb = new Cart();
                //赋值
                $cartDb->user_id=$userId;
                $cartDb->goods_id=$goodsId;
                $cartDb->amount=$num;

            }
            $cartDb->save();
        }
        return $this;
    }

    //清空Cookie购物车数据
    public function flush(){
        $this->cart=[];
        return $this;
    }

    //保存
    public function save(){
        //创建设置cookie对象
        $setCookie =  \Yii::$app->response->cookies;

        //创建一个cookie对象 存入数据
        $cookie = new Cookie([
            'name'=> 'cart',
            'value' => $this->cart,
            'expire' => time()+3600*24*30*12,
        ]);

        //通过设置cookie对象添加一个cookie
        $setCookie->add($cookie);

        return $this;
    }



}