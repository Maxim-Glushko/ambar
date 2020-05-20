<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Product;

/**
 * @var $this View
 * @var $model Product
 */

$this->title = Yii::t('admin', 'Create Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">
    <div class="box box-primary">
        <div class="box-body">
            <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
