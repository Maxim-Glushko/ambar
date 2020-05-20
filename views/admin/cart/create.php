<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cart */

$this->title = Yii::t('admin', 'Create Cart');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Carts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
