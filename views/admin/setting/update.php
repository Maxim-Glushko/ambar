<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Setting;

/**
 * @var $this View
 * @var $model Setting
 */

$this->title = Yii::t('admin', 'Update Setting: {name}', [
    'name' => $model->slug . ' (' . $model->description . ')',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Settings'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->slug, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>

<div class="setting-update">
    <div class="box box-primary">
        <div class="box-body">
            <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
