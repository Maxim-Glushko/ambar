<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Product;

/**
 * @var $this View
 * @var $model Product
 */

$this->title = Yii::t('admin', 'Update Product: {name}', [
    'name' => $model->v('name'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->v('name');
?>
<div class="product-update">
    <div class="box box-primary">
        <div class="box-body">
            <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
            <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
        </div>
    </div>
</div>
