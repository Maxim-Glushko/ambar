<?php

use app\models\Picture;
use yii\widgets\ActiveForm;
use app\models\Product;

/**
 * @var $form ActiveForm
 * @var $picture array
 * @var $model Product
 * @var $i integer
 * @var $src null|string
 */

$src = $picture['src'] ?: $src;
?>

<li class="one-picture">
    <img src="<?= Picture::getThumb($src) ?>" />
    <span class="fa fa-times-rectangle" title="<?= Yii::t('admin', 'delete') ?>"></span>
    <i class="fa fa-arrow-circle-left left"></i>
    <i class="fa fa-arrow-circle-right right"></i>
    <?= $form->field($model, 'picture_data[' . $i . '][description_ua]')
        ->textarea(['rows' => 3, 'class' => 'picture-data-desc-ua', 'value' => $picture['description_ua'], 'placeholder' => 'ua'])
        ->label(false) ?>
    <?= $form->field($model, 'picture_data[' . $i . '][description_ru]')
        ->textarea(['rows' => 3, 'class' => 'picture-data-desc-ru', 'value' => $picture['description_ru'], 'placeholder' => 'ru'])
        ->label(false) ?>
    <?= $form->field($model, 'picture_data[' . $i . '][src]')
        ->hiddenInput(['class' => 'picture-data-src', 'value' => $src])->label(false) ?>
</li>
