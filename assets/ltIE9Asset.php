<?php
namespace app\assets;

use yii\web\AssetBundle;

class ltIE9Asset extends AssetBundle
{
    public $js = [
        'https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js',
        'https://oss.maxcdn.com/respond/1.4.2/respond.min.js'
    ];
    public $jsOptions = [
        'condition' => 'lt IE9',
        'position' => \yii\web\View::POS_HEAD
    ];
}
