<?php

$adminControllers = 'content|product|user|picture|setting|unit|news|article|cart|order';
$productSorts = 'price|-price|sequence|-sequence';

return [
    '/<action:(login|logout|register|password-reset)>' => '/site/<action>',
    '/email-confirm/<id:\d+>/<token:[a-zA-Z0-9_.+-]+>' => '/site/email-confirm',
    '/change-password/<token:[a-zA-Z0-9_.+-]+>' => '/site/change-password',

    '/admin' => '/admin/order/index',
    '/admin/<controller:(' . $adminControllers . ')>' => '/admin/<controller>/index',
    '/admin/<controller:(' . $adminControllers . ')>/<id:\d+>' => '/admin/<controller>/view',
    '/admin/<controller:(' . $adminControllers . ')>/<action:[-a-zA-Z0-9]+>' => '/admin/<controller>/<action>',
    '/admin/<controller:(' . $adminControllers . ')>/<action:[-a-zA-Z0-9]+>/<id:\d+>' => '/admin/<controller>/<action>',

    //'/<controller:(news|article)' => '/<controller>/index',
    //'/<controller:(news|article)/page:<page:\d+>' => '/<controller>/index',
    //'/<controller:(news|article)/<slug:[-a-zA-Z0-9]+>' => '/<controller>/view',

    '/cart/add' => '/cart/add', // добавления товара в корзину (инфа в post: id и quantity)
    '/cart/del' => '/cart/del', // удаление товара из корзины
    '/cart/change' => '/cart/change', // изменение количества
    '/cart/plus-minus' => '/cart/plus-minus', // плюс-минус уже в корзине
    '/cart/order-form' => '/cart/order-form', // перекладывание из корзины в заказ, удаление корзины

    //'<controller:[-a-zA-Z0-9]+>/<id:\d+>' => '<controller>/view',
    //'<controller:[-a-zA-Z0-9]+>/<action:[-a-zA-Z0-9]+>/<id:\d+>' => '<controller>/<action>',
    //'<controller:[-a-zA-Z0-9]+>/<action:[-a-zA-Z0-9]+>' => '<controller>/<action>',

    '/<slug:[-a-zA-Z0-9]+>' => '/content/view',
    '/<slug:[-a-zA-Z0-9]+>/page:<page:\d+>' => '/content/view',
    '/<slug:[-a-zA-Z0-9]+>/sort:<sort:(' . $productSorts . ')>' => '/content/view',
    '/<slug:[-a-zA-Z0-9]+>/page:<page:\d+>/sort:<sort:(' . $productSorts . ')>' => '/content/view',

    '/<controller:(article|news)>/<slug:[-a-zA-Z0-9]+>' => '/<controller>/view',

    '/<slug:[-a-zA-Z0-9]+>/<pslug:[-a-zA-Z0-9]+>' => '/product/view',
    // во вьюхе view контента: содержимое самой категории + подкатегории (если есть) + список продуктов
    // со временем с сортировкой

    '' => '/content/view',
];