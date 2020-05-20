<?php

use yii\db\Migration;

class m200408_184920_create_articles_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('articles', [
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
            'sequence' => $this->integer()->unsigned(), // пока будут находиться в одной категории articles
            // но если понадобятся ещё - нужно будет добавить category_id

            'created_at' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'published_at' => $this->integer()->unsigned()->comment('если нужно заранее запланировать публикацию'),
        ]);

        $this->execute('ALTER TABLE `articles` MODIFY `slug` varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci');

        $this->execute("ALTER TABLE `articles` ADD KEY `str10-articles-slug` (`slug`(10))");
    }

    public function safeDown()
    {
        $this->dropTable('articles');
    }
}
