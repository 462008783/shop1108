<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property int $goods_id 商品编号
 * @property string $amount 商品数量
 * @property int $user_id 用户id
 */
class Cart extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id','amount','user_id'], 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品编号',
            'amount' => '商品数量',
            'user_id' => '用户id',
        ];
    }
}
