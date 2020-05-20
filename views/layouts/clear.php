<?php

use yii\helpers\Html;
use app\assets\ClearAsset;
use app\widgets\Alert;
use yii\web\View;

/**
 * @var $this View
 * @var $content string
 */

ClearAsset::register($this);

$this->beginPage();
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <?= $this->head() ?>
</head>

<body class="<?= $this->params['bodyclass'] ?? '' ?>">
<?php $this->beginBody() ?>

    <?= Alert::widget() ?>
    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html><?php $this->endPage(); ?>