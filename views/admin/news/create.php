<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\News;

/**
 * @var $this View
 * @var $model News
 */

$this->title = Yii::t('admin', 'Create News');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
