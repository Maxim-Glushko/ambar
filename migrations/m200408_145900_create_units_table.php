<?php

use yii\db\Migration;

class m200408_145900_create_units_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('units', [
            'id' => $this->primaryKey()->unsigned(),
            'name_ua' => $this->string(191),
            'name_ru' => $this->string(191),
            'sequence' => $this->integer()->unsigned(),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
        ]);

        $time = time();
        $this->batchInsert('units', ['name_ua', 'name_ru', 'sequence', 'created_at'], [
            ['г', 'г', 1, $time],
            ['кг', 'кг', 2, $time],
            ['мл', 'мл', 3, $time],
            ['л', 'л', 4, $time],
            ['штук', 'штук', 5, $time],
            ['листів', 'листов', 6, $time],
            ['пакетиків', 'пакетиков', 7, $time],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('units');
    }
}
