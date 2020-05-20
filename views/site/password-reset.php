<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Common as CH;
use himiklab\yii2\recaptcha\ReCaptcha3;

$this->title = Yii::t('login', 'Password reset');
$this->params['bodyclass'] = 'hold-transition register-page';
?>

<div class="register-box">
    <div class="register-logo">
        <a href="<?= CH::$pLng ?: '/' ?>"><b>Амбар</b>.od.ua</a>
        <?php /* <a href="#"><b>Admin</b>LTE</a>' */ ?>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">
            <?= Yii::t('login', 'Please fill out your email. A link to reset password will be sent there.') ?>
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'password-reset-form',
            'method' => 'post'
        ]); ?>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'placeholder' => Yii::t('login', 'Email')
            ])->label(false) ?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

        <?php if (!YII_ENV_DEV) { ?>
            <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha3::class, [
                'action' => 'homepage'
            ])->label(false) ?>
        <?php } ?>

        <div class="row">
            <div class="col-xs-12">
                <?= Html::submitButton(Yii::t('login', 'Send'), [
                    'class' => 'btn btn-primary btn-block btn-flat',
                    'name' => 'send-button'
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>