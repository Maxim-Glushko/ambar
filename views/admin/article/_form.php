<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Article;
use kartik\datetime\DateTimePicker;
use app\models\Picture;

/**
 * @var $this View
 * @var $model Article
 * @var $form ActiveForm
 */

if(!$model->published_at) {
    $model->published_at = $model->created_at ?: time();
}
$model->published_at = date("d.m.Y H:i", (integer) $model->published_at);
?>

<div class="article-form">
    <div class="box box-primary">
        <div class="box-body">

            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-sm-9">
                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-3">

                    <?= $form->field($model, 'published_at')->widget(DateTimePicker::class,[
                        'name' => 'published_at',
                        'type' => DateTimePicker::TYPE_INPUT,
                        'options' => ['placeholder' => 'Select operating time ...'],
                        'convertFormat' => true,
                        'value'=> date("d.m.Y h:i",(integer) $model->published_at),
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose'=>true,
                            'weekStart'=>1, //неделя начинается с понедельника
                            'startDate' => '10.05.2020 00:00', //самая ранняя возможная дата
                            'todayBtn'=>true, //снизу кнопка "сегодня"
                            // 'todayHighlight' => true
                        ]
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name_ua')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title_ua')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'keywords_ua')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'keywords_ru')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'description_ua')->textarea(['rows' => 3]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'description_ru')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'text_ua')->textarea(['rows' => 10]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'text_ru')->textarea(['rows' => 10]) ?>
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
    </div>
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
