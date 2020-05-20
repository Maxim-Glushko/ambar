<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Setting;

/**
 * @var $this View
 * @var $model Setting
 * @var $form ActiveForm
 */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php /*= $form->field($model, 'slug')->textInput(['maxlength' => true]) */ ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'value_ua')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'value_ru')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
