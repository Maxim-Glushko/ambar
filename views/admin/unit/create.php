<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Unit;

/**
 * @var $this View
 * @var $model Unit
 */

$this->title = Yii::t('admin', 'Create Unit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-create">
    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
