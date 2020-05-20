<?php

use yii\db\Migration;

class m200416_162221_create_order_product_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('order_product', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->unsigned(),
            'product_id' => $this->integer()->unsigned(),
            'product_name' => $this->string(),// для истории, на случай, если продукт будет удалён
            'quantity' => $this->integer()->unsigned(),
            'price' => $this->decimal(8,2),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->createIndex('idx-order_product-order_id', 'order_product', 'order_id');
        $this->createIndex('idx-order_product-product_id', 'order_product', 'product_id');
    }

    public function safeDown()
    {
        $this->dropTable('order_product');
    }
}
