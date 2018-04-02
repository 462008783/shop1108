<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_delivery".
 *
 * @property int $id
 * @property string $name 配送方式名称
 * @property string $money
 * @property string $content 运费标准
 */
class OrderDelivery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_delivery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'money'], 'required'],
            [['money'], 'number'],
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
            'name' => '配送方式名称',
            'money' => 'Money',
            'content' => '运费标准',
        ];
    }
}
