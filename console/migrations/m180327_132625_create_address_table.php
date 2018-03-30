<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180327_132625_create_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'username'=>$this->string()->comment('收货人'),
            'area'=> $this->string()->comment('所在地区'),
            'goods_take'=> $this->string()->comment('详细地址'),
            'mobile'=>$this->string(20)->comment("手机号码"),
            'user_id'=>$this->integer()->comment("用户id"),
            'is_default'=>$this->integer()->comment('默认地址')

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('address');
    }
}
