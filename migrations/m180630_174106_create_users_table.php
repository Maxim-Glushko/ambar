<?php

use yii\db\Migration;

class m180630_174106_create_users_table extends Migration
{
    public function safeUp()
    {
        if (preg_match('/dbname=([^;]*)/', Yii::$app->getDb()->dsn, $match)) {
            $this->execute('ALTER DATABASE ' . $match[1] . ' CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci');
            //$this->execute('ALTER DATABASE ' . $match[1] . ' CHARACTER SET = utf8 COLLATE = utf8_unicode_ci');
        }

        $this->createTable('users', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(Yii::$app->params['user.maxUsernameLength'])->notNull(),
            'email' => $this->string(Yii::$app->params['user.maxEmailLength'])->notNull()->unique(),
            'phone' => $this->string(9),
            'address' => $this->text(),
            'role' => $this->string(10), // admin, manager, customer (без permissions)
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(60)->notNull(),
            'email_confirm_token' => $this->string(7),
            'password_reset_token' => $this->string(18),
            'registered_at' => $this->integer()->unsigned(),
            'confirmed_at' => $this->integer()->unsigned()->defaultValue(NULL),
            'last_visit' => $this->integer()->unsigned(),
            'ban_expiration' => $this->integer()->unsigned()->defaultValue(NULL),
            'comment' => $this->text(),
        ]);

        // для экономии пространства, где нужна лишь латиница
        // а в следующих таблицах и для увеличения символов со 191 до  255
        $changes = [
            'email' => Yii::$app->params['user.maxEmailLength'],
            'phone' => 9,
            'role' => 10,
            'auth_key' => 32,
            'password_hash' => 60,
            'email_confirm_token' => 7,
            'password_reset_token' => 18,
        ];
        foreach ($changes as $name => $num) {
            $this->execute('ALTER TABLE `users` MODIFY `' . $name . '` varchar(' . $num
                . ') CHARSET utf8 COLLATE utf8_unicode_ci');
        }

        $this->execute("ALTER TABLE `users` ADD KEY `str10-users-email` (`email`(10))");
        $this->execute("ALTER TABLE `users` ADD KEY `str10-users-phone` (`phone`(9))");
        $this->execute("ALTER TABLE `users` ADD KEY `str10-users-username` (`username`(10))");
        $this->execute("ALTER TABLE `users` ADD KEY `str18-users-password_reset_token` (`password_reset_token`(18))");
        $this->createIndex('idx-users-registered_at', 'users', 'registered_at');
        $this->createIndex('idx-users-confirmed_at', 'users', 'confirmed_at');

        $time = time();

        $this->batchInsert('users', ['username', 'email', 'role', 'registered_at', 'confirmed_at', 'comment'], [
            ['Юрий', '7003443@gmail.com', 'admin', ++$time, ++$time, ''],
            ['Максим admin', 'glushko.me@gmail.com', 'admin', ++$time, ++$time, 'тестовый админ'],
            ['Максим manager', 'megl@ya.ru', 'manager', ++$time, ++$time, 'тестовый менеджер'],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
