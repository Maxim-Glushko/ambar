<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use app\models\Order;

/**
 * @var $this View
 * @var $model Order
 * @var $form ActiveForm
 */
?>

<div class="order-form" style="clear: both;">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <?php /* = $form->field($model, 'sum')->textInput(['maxlength' => true]) вывести с перечнем продуктов */ ?>

    <?php /*= $form->field($model, 'data')->textarea(['rows' => 6]) вывести для админов историю изменений */ ?>

    <div class="row">
        <div class="col-md-6 col-lg-4">
            <?= $form->field($model, 'status')->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'status',
                'data' => Order::statuses(),
                'hideSearch' => true,
            ]) ?>
        </div>
        <div class="col-md-6 col-lg-8">
            <div class="form-group" style="padding-top: 24px;">
                <?= Html::submitButton(Yii::t('admin', 'Correct'), ['class' => 'btn btn-primary', 'style' => 'width: 100%;']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
