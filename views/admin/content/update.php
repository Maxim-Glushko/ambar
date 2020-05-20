<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Content;

/**
 * @var $this View
 * @var $model Content
 */

$this->title = Yii::t('admin', 'Update Content: {name}', [
    'name' => $model->v('name'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Categories'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->v('name') ?: $model->slug, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
$this->params['breadcrumbs'][] = $model->v('name') ?: $model->slug;
?>
<div class="content-update">
    <div class="box box-primary">
        <div class="box-body">
            <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
