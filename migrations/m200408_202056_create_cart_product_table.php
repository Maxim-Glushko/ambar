<?php

use yii\db\Migration;

class m200408_202056_create_cart_product_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('cart_product', [
            'id' => $this->primaryKey(),
            'cart_id' => $this->integer()->unsigned(),
            'product_id' => $this->integer()->unsigned(),
            'quantity' => $this->integer()->unsigned(),
            //'price' => $this->decimal(8,2),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->createIndex('idx-cart_product-cart_id', 'cart_product', 'cart_id');
        $this->createIndex('idx-cart_product-product_id', 'cart_product', 'product_id');
    }

    public function safeDown()
    {
        $this->dropTable('cart_product');
    }
}
