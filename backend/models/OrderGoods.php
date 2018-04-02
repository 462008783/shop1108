<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_goods".
 *
 * @property int $id
 * @property int $goods_id 商品ID
 * @property int $order_id 订单ID
 * @property string $goods_name 商品名称
 * @property int $amount 数量
 * @property string $logo 商品logo
 * @property string $price 商品价格
 * @property string $total_price 小计
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'order_id', 'goods_name', 'amount', 'price', 'total_price'], 'required'],
            [['goods_id', 'order_id', 'amount'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['goods_name', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品ID',
            'order_id' => '订单ID',
            'goods_name' => '商品名称',
            'amount' => '数量',
            'logo' => '商品logo',
            'price' => '商品价格',
            'total_price' => '小计',
        ];
    }
}
