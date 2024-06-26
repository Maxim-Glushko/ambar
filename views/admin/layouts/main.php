<?php
use yii\helpers\Html;
use yii\web\View;
use app\assets\AdminAsset;
use dmstr\helpers\AdminLteHelper;

/**
 * @var $this View
 * @var $content string
 */

AdminAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$this->beginPage();
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition <?= AdminLteHelper::skinClass() ?> sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'content.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html><?php $this->endPage() ?>