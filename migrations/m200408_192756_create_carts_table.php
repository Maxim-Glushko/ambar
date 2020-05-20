<?php

use yii\db\Migration;

class m200408_192756_create_carts_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('carts', [
            'id' => $this->primaryKey()->unsigned(),
            'key' => $this->string(23)->comment('пока юзер не зарегистрирован'),
            'user_id' => $this->integer()->unsigned(),
            //'status' => $this->tinyInteger()->unsigned(),
            //'note' => $this->text()->comment('примечания заказчика'),
            //'comment' => $this->text()->comment('комментарий оператора, обслуживающего заказ'),
            //'sum' => $this->decimal(8,2),
            //'data' => $this->text()->comment('json разных данных: email, phone, address...'),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->execute('ALTER TABLE `carts` MODIFY `key` varchar(23) CHARSET utf8 COLLATE utf8_unicode_ci');

        $this->execute("ALTER TABLE `carts` ADD KEY `str10-carts-key` (`key`(23))");
        // 10 символов timestamp + 13 случайных символов

        $this->createIndex('idx-carts-user_id', 'carts', 'user_id');
        //$this->createIndex('idx-carts-status', 'carts', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('carts');
    }
}
