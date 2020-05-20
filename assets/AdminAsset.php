<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot/static/';
    public $baseUrl = '@web/static/';
    public $css = [
        'css/admin.css',
    ];
    public $js = [
        'js/main.js',
        'js/admin.js',
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}
