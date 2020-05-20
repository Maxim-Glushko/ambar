<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Article;

/**
 * @var $this View
 * @var $model Article
 */

$this->title = Yii::t('admin', 'Update Article: {name}', [
    'name' => $model->v('name'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="article-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
