<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\News;

/**
 * @var $this View
 * @var $model News
 */

$this->title = Yii::t('admin', 'Update News: {name}', [
    'name' => $model->v('name'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="news-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
