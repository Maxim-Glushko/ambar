<?php

use yii\web\View;
use app\models\Content;
use app\models\Article;
use yii\helpers\Url;
use app\helpers\Common as CH;
use yii\helpers\Html;
use app\models\Picture;

/**
 * @var $this View
 * @var $model Article
 * @var $content Content
 */

$this->title = $model->v('title') ?: $model->v('name');

foreach (['keywords', 'description'] as $key) {
    if ($content->v($key)) {
        $this->registerMetaTag(['name' => $key, 'content' => $content->v($key)]);
    }
}

$this->params['breadcrumbs'][] = [
    'label' => $content->v('name'),
    'url' => CH::$pLng . '/' . $content->slug
];
$this->params['breadcrumbs'][] = $model->v('name');

?>

<div class="content-view">
    <?php /*if (CH::isAdmin()) { ?>
        <a href="<?= Url::to(['admin/article/update', 'id' => $model->id]) ?>"
                class="btn btn-success pull-left" target="_blank" style="margin: 25px 20px 0 0;">
            <?= Yii::t('admin', 'Update') ?>
        </a>
    <?php }*/ ?>

    <h1><?= $model->v('name') ?></h1>

    <div class="content-text">
        <?php if ($model->img) { ?>
            <img src="<?= $model->img ?>" alt="<?= Html::encode($model->v('name')) ?>" class="article-img" />
        <?php } ?>

        <?= nl2br($model->v('text')) ?>
    </div>
</div>
