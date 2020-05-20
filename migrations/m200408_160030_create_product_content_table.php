<?php

use yii\db\Migration;
use app\models\Product;
use app\models\Content;
use yii\helpers\ArrayHelper as AH;

class m200408_160030_create_product_content_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('product_content', [
            'id' => $this->primaryKey()->unsigned(),
            'product_id' => $this->integer()->unsigned(),
            'content_id' => $this->integer()->unsigned(),
            'main' => $this->tinyInteger()->unsigned()->defaultValue(1)->comment('главная для этого продукта категория'),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->createIndex('idx-product_content-product_id', 'product_content', 'product_id');
        $this->createIndex('idx-product_content-content_id', 'product_content', 'content_id');

        $cats = [
            ['cat_ids' => [2], 'count' => 26],
            ['cat_ids' => [3], 'count' => 72],
            ['cat_ids' => [4], 'count' => 8],
            ['cat_ids' => [7], 'count' => 69],
            ['cat_ids' => [7, 3], 'count' => 17],
        ];
        $time = time();
        $rows = [];
        $prod_id = 1;
        foreach ($cats as $data) {
            for ($i = 0; $i < $data['count']; $i++) {
                $main = 1;
                foreach ($data['cat_ids'] as $cat_id) {
                    $rows[] = [$cat_id, $prod_id, $main, $time++];
                    $main = 0;
                }
                $prod_id++;
            }
        }
        $this->batchInsert('product_content', ['content_id', 'product_id', 'main', 'created_at'], $rows);


        /*
        $productsCount = Product::find()->count();
        $rows = [];
        $time = time();
        for ($i = 1; $i <= $productsCount; $i++) {
            $catId = mt_rand(2, 9);
            $rows[] = [$i, $catId, 1, $time];
            if (mt_rand(1, 7) > 6) {
                $catId2 = mt_rand(2, 9);
                if ($catId == $catId2) {
                    $catId2++;
                    if ($catId2 > 9) {
                        $catId2 = 2;
                    }
                }
                $rows[] = [$i, $catId2, 0, $time];
            }
        }
        $this->batchInsert('product_content', ['product_id', 'content_id', 'main', 'created_at'], $rows);

        $ids = AH::getColumn(Content::find()->select('id')->where(['showmenu' => 1])->all(), 'id');
        foreach ($ids as $id) {
            $products = Product::find()
                ->innerJoin('product_content', 'product_content.product_id = products.id')
                ->where('product_content.content_id = ' . $id . ' AND product_content.main = 1')
                ->all();
            $sequence = 1;
            if ($products) foreach ($products as $product) {
                $product->sequence = $sequence++;
                $product->updateAttributes(['sequence']);
            }
        }
        */
    }

    public function safeDown()
    {
        $this->dropTable('product_content');
    }
}
