<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Article;

/**
 * @var $this View
 * @var $model Article
 */

$this->title = Yii::t('admin', 'Create Article');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
