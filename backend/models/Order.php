<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $name 收货人
 * @property string $province 省
 * @property string $city 市
 * @property string $county 区县
 * @property string $address 详细地址
 * @property string $mobile 电话
 * @property int $delivery_id 配送方式ID
 * @property string $delivery_name 配送名称
 * @property string $delivery_price 配送价格
 * @property int $pay_id 支付方式ID
 * @property string $pay_name 支付名称
 * @property string $price 订单总金额
 * @property string $trade_no 订单号
 * @property int $order_status 订单状态 0已取消 1待付款 2待发货 3待收货 4完成
 * @property int $created_at 创建时间
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name', 'province', 'city', 'county', 'address', 'mobile', 'delivery_name', 'delivery_price', 'pay_id', 'pay_name', 'price', 'trade_no', 'order_status', 'created_at'], 'required'],
            [['user_id', 'delivery_id', 'pay_id', 'order_status', 'created_at'], 'integer'],
            [['trade_no'],'unique'],
            [['delivery_price', 'price'], 'number'],
            [['name', 'province', 'city', 'county', 'address', 'delivery_name', 'pay_name', 'trade_no'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'county' => '区县',
            'address' => '详细地址',
            'mobile' => '电话',
            'delivery_id' => '配送方式ID',
            'delivery_name' => '配送名称',
            'delivery_price' => '配送价格',
            'pay_id' => '支付方式ID',
            'pay_name' => '支付名称',
            'price' => '订单总金额',
            'trade_no' => '订单号',
            'order_status' => '订单状态 0已取消 1待付款 2待发货 3待收货 4完成',
            'created_at' => '创建时间',
        ];
    }
}
