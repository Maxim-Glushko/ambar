<?php

use yii\db\Migration;

class m200408_174741_create_news_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('news', [
            'id' => $this->primaryKey()->unsigned(),
            'slug' => $this->string(),

            'name_ua'  => $this->string(191),
            'name_ru'  => $this->string(191),
            'title_ua'  => $this->string(191),
            'title_ru'  => $this->string(191),
            'keywords_ua'  => $this->string(191),
            'keywords_ru'  => $this->string(191),
            'description_ua'  => $this->text(),
            'description_ru'  => $this->text(),
            'text_ua'  => $this->text(),
            'text_ru'  => $this->text(),
            'img'  => $this->text(),

            'show' => $this->tinyInteger()->unsigned()->comment('0 - не показывать'),

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'published_at' => $this->integer()->unsigned()->comment('если нужно заранее запланировать публикацию'),
        ]);

        $this->execute('ALTER TABLE `news` MODIFY `slug` varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci');

        $this->execute("ALTER TABLE `news` ADD KEY `str10-news-slug` (`slug`(10))");
    }

    public function safeDown()
    {
        $this->dropTable('news');
    }
}
