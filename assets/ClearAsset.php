<?php

namespace app\assets;

use yii\web\AssetBundle;

class ClearAsset extends AssetBundle
{
    public $basePath = '@webroot/static/';
    public $baseUrl = '@web/static/';
    public $css = [
        'font-awesome/css/font-awesome.min.css',
        'Ionicons/css/ionicons.min.css',
        'AdminLTE/css/AdminLTE.min.css',
        'Ionicons/css/ionicons.min.css',
        'plugins/iCheck/square/blue.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
        'css/login.css',
    ];
    public $js = [
        'plugins/iCheck/icheck.min.js',
        'js/login.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\ltIE9Asset',
    ];
}
