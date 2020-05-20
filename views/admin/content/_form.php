<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Content;
use app\models\Picture;

/**
 * @var $this View
 * @var $model Content
 * @var $form ActiveForm
 */
?>

<div class="content-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'name_ua')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'title_ua')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'keywords_ua')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'keywords_ru')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'description_ua')->textarea(['rows' => 3]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'description_ru')->textarea(['rows' => 3]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'text_ua')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'text_ru')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="for-content-img">
        <?php $src = Picture::getThumb($model->img); ?>
        <img src="<?= $src ?: '/static/servimg/default.jpg' ?>" class="content-img" data-src="/static/servimg/default.jpg"
             title="<?= Yii::t('admin', 'upload/change picture') ?>" />
        <span class="fa fa-times-rectangle" title="<?= Yii::t('admin', 'delete') ?>"
            <?= $src ? '' : 'style="display: none;"' ?>></span>
    </div>
    <?= $form->field($model, 'img')->hiddenInput(['class' => 'content-input-img'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div style="display: none;">
    <?php $form = ActiveForm::begin([
        'id' => 'contentUploadForm',
        'action' => '/admin/picture/upload',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <?= $form->field($model, 'file')->fileInput() ?>
    <?php ActiveForm::end(); ?>
</div>