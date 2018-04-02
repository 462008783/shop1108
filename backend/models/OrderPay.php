<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_pay".
 *
 * @property int $id
 * @property string $name 支付方式
 * @property string $content 支付方式介绍
 */
class OrderPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '支付方式',
            'content' => '支付方式介绍',
        ];
    }
}
