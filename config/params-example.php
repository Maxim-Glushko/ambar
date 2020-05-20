<?php

return [
    'adminEmail' => 'admin@site.ua',
    'supportEmail' => 'support@site.ua',

    'user.emailConfirmTokenExpire'  => 60 * 60 * 24 * 40, // время на активацию аккаунта, после удаление
    'user.passwordResetTokenExpire' => 60 * 60 * 1, // время на восстановление пароля, потом генерация нового кода
    'user.minUsernameLength'        => 1,
    'user.maxUsernameLength'        => 30,
    'user.maxEmailLength'           => 150,
    'user.minPasswordLength'        => 4,
    'user.maxPasswordLength'        => 100,

    'lngs'      => ['ru-RU' => 'ru',    'uk-UA' => 'ua'], // первый язык - умолчание
    'pLngs'     => ['ru-RU' => '',      'uk-UA' => '/ua'],
    'nLngs'     => ['ru-RU' => 'рус',   'uk-UA' => 'укр'],

    /*'bsVersion' => '3.x',
    'bsDependencyEnabled' => false,*/
];
