<?php

$routes = require __DIR__ . '/routes.php';
$db = require __DIR__ . '/db.php';
$params = require __DIR__ . '/params.php';

// TODO: db.php и web-local.php не запоминаются в гит,
// их скопировать из соответствующих копий -example.php и сконфигурировать

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //'language' => 'uk-UA',
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2y_X3C9TV4LTae3Jf2UuKd_O2d_RfAY1',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'on afterLogin' => function($event) {
                Yii::$app->user->identity->updateAttributes(['last_visit' => time()]);
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false, // Disable index.php
            'enablePrettyUrl' => true, // Disable r= routes
            'rules' => $routes,
        ],*/
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'class' => 'codemix\localeurls\UrlManager', // язык в url
            'enableLanguageDetection' => false,
            'enableLanguagePersistence' => false,
            'languages' => ['ru' => 'ru-RU', 'ua' => 'uk-UA'],
            'rules' => $routes,
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module'
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages', // по умолчанию стоит этот путь
                    'sourceLanguage' => 'en-US',
                    //'sourceLanguage' => 'uk-UA',
                    /*'fileMap' => [
                        'common'    => 'common.php',
                        'login'     => 'login.php',
                    ],*/
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-red',
                ],
                /*'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],*/
            ],
            //'linkAssets' => (YII_ENV_DEV),
        ],
    ],
    'modules' => [
        // у меня как таковых модулей нет, но картик отказывается работать в подпапках вьюх и контроллеров
        'gridview' => ['class' => 'kartik\grid\Module']
    ],
    'params' => $params,

    'on beforeRequest' => function($event) {
        // запуск какого-то кода или вызов методов
    },
    'on afterAction' => function($event) {
        // запуск какого-то кода или вызов методов
    }
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return yii\helpers\ArrayHelper::merge(
    $config,
    require __DIR__ . '/web-local.php'
);
