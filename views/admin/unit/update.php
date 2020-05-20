<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Unit;

/**
 * @var $this View
 * @var $model Unit
 */

$this->title = Yii::t('admin', 'Update Unit: {name}', [
    'name' => $model->name_ua . ' / ' . $model->name_ru,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Units'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $model->name_ua . ' / ' . $model->name_ru;
?>
<div class="unit-update">
    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
