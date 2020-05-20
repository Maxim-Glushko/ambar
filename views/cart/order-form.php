<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\Setting;
use app\models\OrderForm;
use app\models\Cart;
use app\models\Order;
use kartik\select2\Select2;
use himiklab\yii2\recaptcha\ReCaptcha3;
use app\helpers\Common as CH;

/** @param $model OrderForm */

$model->phone = $model->phone ?? '+380';
$old = Order::oldInfa();
$model->name = $model->name ?? (Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username);
$sum = Cart::cartSum();
$freeDelivery = ($sum > intval(Setting::v('delivery-sum-free')));

?>

<div class="order-box-body">

    <p class="order-sum"><?= $sum ?>  ₴</p>

    <p class="delivery-description">
        <?= Setting::v($freeDelivery ? 'delivery-free-text' : 'delivery-text') ?>
    </p>

    <p class="order-description"><?= Setting::v('order-description') ?></p>

    <?php $form = ActiveForm::begin([
        'id' => 'order-form',
        'method' => 'post',

        'validateOnChange' => true,
        'validateOnType' => true,
    ]); ?>

    <?= empty($old['phone'])
        ? $form->field($model, 'phone')->textInput(['autofocus' => true])
        : $form->field($model, 'phone')->widget(Select2::class, [
                'data' => $old['phone'],
                'language' => CH::$lng,
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '',
                    'tags' => true,
                ],
                //'hideSearch' => true,
    ]) ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= empty($old['address'])
        ? $form->field($model, 'address')->textarea(['rows' => 2])
        : $form->field($model, 'address')->widget(Select2::class, [
            'data' => $old['address'],
            'language' => CH::$lng,
            'pluginOptions' => [
                'allowClear' => true,
                'placeholder' => '',
                'tags' => true,
            ],
            //'hideSearch' => true,
    ]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>

    <?php if (!YII_ENV_DEV) { ?>
        <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha3::class, [
            'action' => 'homepage'
        ])->label(false) ?>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(
            '<span class="fa fa-shopping-bag"></span> ' . Yii::t('common', 'Buy'),
            ['class' => 'btn btn-warning done-button']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
