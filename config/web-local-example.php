<?php

// TODO: создать рядом аналогичный файл без "-example", в нём указать нужные настройки

return [
    'name' => 'Имя сайта',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2y_X3j9TV6LTae3Jf8UdKf_O2d_EfRY1',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // TODO
            // на проде useFileTransport false, а transport раскомментить и сконфигурировать
            'useFileTransport' => true,
            /*'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.your.site',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'your@email.ua',
                'password' => 'your_password',
                'port' => '25',// Port 25 is a very common port too
                //'port' => '143',
                //'port' => '465',
                //'encryption' => 'tls', // It is often used, check your provider or mail server specs
                //'encryption' => 'ssl',
                //'encryption' => 'ssl/tls',
            ],*/
        ],
        // TODO: скопировать и раскомментить в web-local и вввести ключи
        /*'reCaptcha' => [
            'class' => 'himiklab\yii2\recaptcha\ReCaptchaConfig',
            'siteKeyV2' => 'your siteKey v2',
            'secretV2' => 'your secret key v2',
            'siteKeyV3' => 'your siteKey v3',
            'secretV3' => 'your secret key v3',
        ],*/
    ],
];
