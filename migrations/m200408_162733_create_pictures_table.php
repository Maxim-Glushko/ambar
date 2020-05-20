<?php

use yii\db\Migration;
use app\helpers\Morph;
use app\models\Product;

class m200408_162733_create_pictures_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('pictures', [
            'id' => $this->primaryKey()->unsigned(),
            'item_type' => $this->integer()->unsigned()
                ->comment('к какой сущности привязана галерея рисунков (пока планируется только продукт)'),
            'item_id' => $this->integer()->unsigned(),
            'src' => $this->string(),
            'sequence' => $this->integer()->unsigned()->defaultValue(1)->comment('очередность в показе'),
            'description_ua'  => $this->text(),
            'description_ru'  => $this->text(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $this->execute('ALTER TABLE `pictures` MODIFY `src` varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci');

        $this->createIndex('idx-pictures-item_type', 'pictures', 'item_type');
        $this->createIndex('idx-pictures-item_id', 'pictures', 'item_id');

        /*
        $rows = [];
        $time = time();
        $productsCount = Product::find()->count();
        $srcs = ['keks.jpeg', 'maska_minion.jpg', 'minion.jpg', 'orange.jpg', 'ralph.jpg', 'red.png', 'milk.jpg', 'pineapple.jpg',
            'vaneloppe.jpg', 'rolls.jpeg', 'bread2.jpg', 'apple.jpg', 'mario.png', 'phila.jpg', 'chheesse.jpeg', 'dragon.jpg',
            'pig2.jpg', 'GIULANI-CARTOON-UKELELE-2.jpg', 'dry-sausage.jpg'];
        for ($i = 1; $i <= $productsCount; $i++) {
            $rows[] = [Morph::PRODUCT, $i, '/img/test/' . $srcs[mt_rand(0, count($srcs) - 1)], 1, 'опис', 'описание', $time];
            $rows[] = [Morph::PRODUCT, $i, '/img/test/' . $srcs[mt_rand(0, count($srcs) - 1)], 2, 'опис', 'описание', $time];
            $rows[] = [Morph::PRODUCT, $i, '/img/test/' . $srcs[mt_rand(0, count($srcs) - 1)], 3, 'опис', 'описание', $time];
        }
        $this->batchInsert('pictures', ['item_type', 'item_id', 'src', 'sequence', 'description_ua', 'description_ru', 'created_at'],
            $rows
        );
        */
    }

    public function safeDown()
    {
        $this->dropTable('pictures');
    }
}
