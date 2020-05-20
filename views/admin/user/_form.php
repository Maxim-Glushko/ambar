<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;
use app\models\User;

/**
 * @var $this View
 * @var $model User
 * @var $form ActiveForm
 */

$model->phone = $model->phone ? ('+380' . $model->phone) : '';
?>




<div class="box box-primary">
    <div class="box-body">
        <div class="user-form">

            <?php $form = ActiveForm::begin(); ?>

            <?php /*= $form->field($model, 'id')->textInput() */ ?>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'role')->widget(Select2::class, [
                        'data' => $model::rolesForSelect(),
                        'hideSearch' => true,
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
