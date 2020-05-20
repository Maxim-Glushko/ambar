<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\LoginForm;
use yii\web\View;
use app\helpers\Common as CH;
use himiklab\yii2\recaptcha\ReCaptcha3;

/**
 * @var $this View
 * @var $form ActiveForm
 * @var $model LoginForm
 */

$this->title = Yii::t('login', 'Login');
$this->params['bodyclass'] = 'hold-transition login-page';
?>

<div class="login-box">
    <div class="login-logo">
        <a href="<?= CH::$pLng ?: '/' ?>"><b>Амбар</b>.od.ua</a>
        <?php /* <a href="#"><b>Admin</b>LTE</a>' */ ?>
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">
            <?= Yii::t('login','Please fill out the following fields to login') ?>
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'method' => 'post',
        ]); ?>

            <div class="form-group has-feedback">
                <?= $form->field($model, 'email')->textInput([
                    'autofocus' => true,
                    'class' => 'form-control',
                    'placeholder' => Yii::t('login','Email'),
                    'type' => 'email',
                ])->label(false) ?>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>

            <div class="form-group has-feedback">
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => Yii::t('login','Password'),
                    'class' => 'form-control'
                ])->label(false) ?>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>

            <?php if (!YII_ENV_DEV) { ?>
            <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha3::class, [
                'action' => 'homepage'
            ])->label(false) ?>
            <?php } ?>

            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <?= $form->field($model, 'rememberMe')->checkbox([
                                'template' => "{input} {label}"
                            ])->label(null) ?>
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <?= Html::submitButton(Yii::t('login','Login'), [
                        'class' => 'btn btn-primary btn-block btn-flat',
                        'name' => 'login-button']
                    ) ?>
                </div>
            </div>

        <?php ActiveForm::end(); ?>

        <?php /*
        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                Google+</a>
        </div>
        <!-- /.social-auth-links -->
        */ ?>

    </div>

    <div style="padding:12px 20px;background:#f0f0f0;">
        <a href="<?= Url::to('password-reset') ?>" class="pull-left">
            <?= Yii::t('login', 'Forgot password?') ?>
        </a>
        <a href="<?= Url::to('register') ?>" class="pull-right">
            <?= Yii::t('login', 'Register') ?>
        </a>
        <div style="clear:both;"></div>
    </div>
</div>
