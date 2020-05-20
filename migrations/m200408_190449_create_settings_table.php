<?php

use yii\db\Migration;

class m200408_190449_create_settings_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey()->unsigned(),
            'slug' => $this->string(),
            'value_ua' => $this->text(),
            'value_ru' => $this->text(),
            'description' => $this->text(),
        ]);

        $this->execute('ALTER TABLE `settings` MODIFY `slug` varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci');

        $this->execute("ALTER TABLE `settings` ADD KEY `str10-settings-slug` (`slug`(10))");

        $this->batchInsert('settings', ['slug', 'value_ua', 'value_ru', 'description'], [
            ['top-contacts',
                '<a class="phone" href="tel:+380682323222"><span>+38 068 23 23 222</span></a> <span class="address">Черняховського 24</span>',
                '<a class="phone" href="tel:+380682323222"><span>+38 068 23 23 222</span></a> <span class="address">Черняховского 24</span>',
                'Телефон и адрес в шапке'],
            ['empty-cart-text', 'У вашому кошику поки що порожньо', 'В вашей корзине пока пусто', 'Текст о пустой корзине'],
            ['min-order-sum', 0, 0, 'Минимальная сумма заказа'],
            ['min-sum-order-text', 'Мінімальна сума заказу 0 ₴. При 0 этот текст никто не увидит.',
                'Минимальная сумма заказа 0 ₴. При 0 этот текст никто не увидит.', 'Текст о минимальной сумме заказа'],
            ['delivery-price', 50, 50, 'Цена за доставку'],
            ['delivery-text', 'Доставка нижче суми 500 ₴ до 2км коштує 50 ₴', 'Доставка ниже суммы 500 ₴ в радиусе 2км стоит 50 ₴', 'Текст о цене доставки'],
            ['delivery-sum-free', 500, 500, 'Сумма заказа для бесплатной доставки'],
            ['delivery-free-text', 'Доставка до 2км безкоштовна',
                'Доставка до 2км бесплатна', 'Текст о сумме для бесплатной доставки'],
            ['order-description', 'Телефон обов`язково.', 'Телефон обязателен.', 'Описание при заказе'],
            ['after-order', 'Дякуємо за замовлення. Ми вже дзвонимо до Вас!', 'Спасибо за заказ. Мы уже звоним Вам!', 'Текст после заказа'],
            ['main-in-breadcrumbs', 'Доставка їжі', 'Доставка еды', 'Главная в хлебных крошках'],
            ['product-infa',
                '<h4>Я це хочу...</h4>
                <div class="row">
                    <div class="col-sm-7">
                        <p><b>Як замовити?</b></p>
                        <ul>
                            <li>Подзвонити за телефоном: <a href="tel:+380682323222"><span>+38 068 23 23 222</span></a></li>
                            <li>Або набрати кошик, замовити - і ми доставимо до вас</li>
                            <li>Або подивитись ближче у магазині на Черняховського 24</li>
                        </ul>
                    </div>
                    <div class="col-sm-5">
                        <p><b>Як оплатити?</b></p>
                        <ul>
                            <li>Готівкою кур\'єру</li>
                            <li>Карткою</li>
                        </ul>
                    </div>
                </div>',
                '<h4>Я это хочу...</h4>
                <div class="row">
                    <div class="col-sm-7">
                        <p><b>Как заказать?</b></p>
                        <ul>
                            <li>Позвонить по телефону: <a href="tel:+380682323222"><span>+38 068 23 23 222</span></a></li>
                            <li>Либо набрать корзинку, заказать - и мы доставим к вам</li>
                            <li>Либо посмотреть поближе в магазине на Черняховского 24</li>
                        </ul>
                    </div>
                    <div class="col-sm-5">
                        <p><b>Как оплатить ?</b></p>
                        <ul>
                            <li>Наличными курьеру</li>
                            <li>Карткою</li>
                        </ul>
                    </div>
                </div>',
                'Блок под продуктом']
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('settings');
    }
}
