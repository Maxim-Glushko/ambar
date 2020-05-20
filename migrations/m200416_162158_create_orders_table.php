<?php

use yii\db\Migration;

class m200416_162158_create_orders_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey()->unsigned(),
            'status' => $this->tinyInteger()->unsigned(),
            'key' => $this->string(23)->comment('пока юзер не зарегистрирован'),

            'user_id' => $this->integer()->unsigned(),
            'name' => $this->string(Yii::$app->params['user.maxUsernameLength']),
            'phone' => $this->string(9),
            'address' => $this->text(),

            'note' => $this->text()->comment('примечания заказчика'),
            'comment' => $this->text()->comment('комментарий оператора, обслуживающего заказ'),
            'sum' => $this->decimal(8,2),
            'data' => 'mediumtext',

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->createIndex('idx-orders-user_id', 'orders', 'user_id');
        $this->createIndex('idx-orders-status', 'orders', 'status');

        // 10 символов timestamp + 13 случайных символов
        $this->execute("ALTER TABLE `orders` ADD KEY `str10-orders-key` (`key`(23))");
        $this->execute("ALTER TABLE `orders` ADD KEY `str10-orders-phone` (`phone`(9))");
    }

    public function safeDown()
    {
        $this->dropTable('orders');
    }
}
