<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\captcha\Captcha;
use app\helpers\Common as CH;
use himiklab\yii2\recaptcha\ReCaptcha3;

$this->title = Yii::t('login', 'Registration');
$this->params['bodyclass'] = 'hold-transition register-page';
?>

<div class="register-box">
    <div class="register-logo">
        <a href="<?= CH::$pLng ?: '/' ?>/"><b>Амбар</b>.od.ua</a>
        <?php /* <a href="#"><b>Admin</b>LTE</a>' */ ?>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">
            <?= Yii::t('login', 'Please fill out the following fields to signup:') ?>
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'method' => 'post',

            'validateOnChange' => true,
            'validateOnType' => true,
        ]); ?>
            <div class="form-group has-feedback">
                <?= $form->field($model, 'username', [
                    'enableAjaxValidation' => true
                ])->textInput([
                    'autofocus' => true,
                    'placeholder' => Yii::t('login', 'Username'),
                ])->label(false) ?>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput([
                    'placeholder' => Yii::t('login', 'Email')
                ])->label(false) ?>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => Yii::t('login', 'Password'),
                    'class' => 'form-control need-magic'
                ])->label(false) ?>
                <span class="glyphicon glyphicon-lock form-control-feedback" style="z-index:999;"></span>
            </div>

            <?php if (!YII_ENV_DEV) { ?>
                <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha3::class, [
                    'action' => 'homepage'
                ])->label(false) ?>
            <?php } ?>

            <?php /*
            <div class="form-group has-feedback">
                <?= $form->field($model, 'password_repeat')->passwordInput([
                    'placeholder' => Yii::t('login', 'Retype password')
                ])->label(false) ?>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            */ ?>

            <?php /*
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'template' => '<div class="row">
                    <div class="col-lg-7">{input}</div>
                    <div class="col-lg-5">{image}</div>
                </div>',
                'imageOptions' => [
                    'title' => Yii::t('login', 'Refrash'),
                    'style' => 'cursor: pointer;width:118px;height:46px;margin-top:-4px;'
                ],
            ])->label(false) ?>
            */ ?>


                <?php /*<div class="col-xs-7">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox">
                            <?= Yii::t('login', 'I agree to the {b}terms{e}',[
                                'b' => '<a href="/" target="_blank">',
                                'e' => '</a>'
                            ]); ?>
                        </label>
                    </div>
                </div>
                */ ?>
                <?php /*
                <div class="col-xs-7">
                    <p style="margin-top:-4px;"><?= Yii::t('login', 'I agree to the {b}terms{e}',[
                        'b' => '<a href="/" target="_blank">',
                        'e' => '</a>'
                    ]); ?></p>
                </div>
                */ ?>

            <?= Html::submitButton(Yii::t('login', 'Register'), [
                'class' => 'btn btn-primary btn-block btn-flat',
                'name' => 'register-button'
            ]) ?>

        <?php ActiveForm::end(); ?>

        <?php /*
        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
                Google+</a>
        </div>
        */ ?>
    </div>

    <div class="text-center" style="padding:12px 20px;background:#f0f0f0;">
        <a href="<?= Url::to('login') ?>" class="text-center">
            <?= Yii::t('login', 'I already have a membership') ?>
        </a>
    </div>
</div>