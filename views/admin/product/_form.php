<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\Product;
use app\models\Content;
use yii\helpers\ArrayHelper as AH;
use app\helpers\Common as CH;
use kartik\select2\Select2;
use app\models\Unit;
use app\models\Picture;
use kartik\switchinput\SwitchInput;

/**
 * @var $this View
 * @var $model Product
 * @var $form ActiveForm
 */

$cat_id = empty($model->cat_id)
    ? ($model->isNewRecord ? array_keys(Content::forSelect())[0] : $model->productContentMain->content_id)
    : $model->cat_id;

if (empty($model->cat_ids)) {
    $cat_ids = [];
    $contents = $model->contents;
    if ($contents && count($contents)) {
        foreach ($contents as $co) {
            $cat_ids[] = $co->id;
        }
    }
} else {
    $cat_ids = $model->cat_ids;
}

if (empty($model->picture_data)) {
    $picture_data = [];
    if ($model->pictures && count($model->pictures)) {
        $i = 1;
        foreach ($model->pictures as $picture) {
            $picture_data[$i]['src'] = $picture->src;
            $picture_data[$i]['description_ua'] = $picture->description_ua;
            $picture_data[$i++]['description_ru'] = $picture->description_ru;
        }
    }
} else {
    $picture_data = $model->picture_data;
}

$model->status = $model->status ?? 1;

?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-3">
            <?= $form->field($model, 'cat_id')->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'cat_id',
                'data' => Content::forSelect(),
                'hideSearch' => true,
                //'value' => $model->isNewRecord ? $content_ids[0] : $model->productContentMain->content_id,
                'options' => [
                    'value' => $cat_id,
                ],
            ]) ?>
        </div>
        <div class="col-xs-9">
            <?= $form->field($model, 'cat_ids')->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'cat_id',
                'data' => Content::forSelect(),
                'hideSearch' => true,
                'options' => [
                    'multiple' => true,
                    'value' => $cat_ids,
                ]
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'vendorcode')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($model, 'recommended')->widget(SwitchInput::class, []) ?>
        </div>
    </div>
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
            <?= $form->field($model, 'description_ua')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'description_ru')->textarea(['rows' => 6]) ?>
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
    <div class="row">
        <div class="col-xs-2">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'measure')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'unit_id')->widget(Select2::class, [
                'data' => Unit::forSelect(),
                'hideSearch' => true,
            ]) ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'availability')->textInput() ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'status')->widget(Select2::class, [
                'data' => Product::statuses(),
                'hideSearch' => true,
            ]) ?>
        </div>
    </div>

    <div class="admin-product-imgs">
        <ul>
            <?php if (!empty($picture_data)) {
                foreach ($picture_data as $i => $picture) { ?>
                    <?= $this->render('one-picture', compact('form', 'picture', 'model', 'i')) ?>
                <?php }
            } ?>
            <li>
                <div class="plus" data-index="<?= ++$i ?>" data-url="">
                    <i class="fa fa-file-photo-o"></i>
                </div>
            </li>
        </ul>
    </div>

    <?php /*= $form->field($model, 'sequence')->textInput() */ ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'style' => 'width: 100%;']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div style="display: none;">
    <?php $form = ActiveForm::begin([
        'id' => 'productUploadForm',
        'action' => '/admin/product/upload',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <?= $form->field($model, 'file')->fileInput() ?>
    <?php ActiveForm::end(); ?>
</div>
