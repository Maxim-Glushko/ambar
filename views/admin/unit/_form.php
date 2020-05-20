<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Unit;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $model Unit
 * @var $form ActiveForm
 */

$units = Unit::forSelect();
?>

<div class="row">
    <div class="col-xs-8">
        <div class="box box-primary">
            <div class="box-body">
                <div class="unit-form">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= $form->field($model, 'name_ua')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-xs-6">
                            <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="box box-primary">
            <div class="box-body">
                <?php foreach ($units as $k => $v) if ($k) { ?>
                    <?php if (!$model->isNewRecord && ($model->id == $k)) { ?>
                        <span style="font-size: 15px;">
                            <span class="fa fa-tag"></span>&nbsp;
                            <?= $v ?>
                        </span>
                    <?php } else { ?>
                        <a style="font-size: 15px;" href="<?= Url::to(['/admin/unit/update', 'id' => $k]) ?>">
                            <span class="fa fa-tag"></span>&nbsp;
                            <?= $v ?>
                        </a>
                    <?php } ?>
                    <br />
                <?php } ?>
                </a>
            </div>
        </div>
    </div>
</div>


