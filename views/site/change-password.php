<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Common as CH;

$this->title = Yii::t('login', 'Password Change');
$this->params['bodyclass'] = 'hold-transition register-page';

?>

<div class="register-box">
    <div class="register-logo">
        <a href="<?= CH::$pLng ?: '/' ?>"><b>Амбар</b>.od.ua</a>
        <?php /* <a href="#"><b>Admin</b>LTE</a>' */ ?>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">
            <?= Yii::t('login', 'Please choose your new password:') ?>
        </p>

        <?php $form = ActiveForm::begin([
            'id' => 'change-password-form',
            'method' => 'post'
        ]); ?>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password')->passwordInput([
                'autofocus' => true,
                'placeholder' => Yii::t('login', 'Password'),
                'class' => 'form-control need-magic'
            ])->label(false) ?>
            <span class="glyphicon glyphicon-lock form-control-feedback" style="z-index:999;"></span>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <?= Html::submitButton(Yii::t('login', /*'Save'*/ 'Change Password'), [
                    'class' => 'btn btn-primary btn-block btn-flat',
                    'name' => 'save-button'
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>