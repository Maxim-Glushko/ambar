<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$link = Url::to(['email-confirm', 'id' => $user->id, 'token' => $user->email_confirm_token], true);
?>

<?= Yii::t('login', 'Hello {username}!', ['username' => Html::encode($user->username)]) ?>

<?= Yii::t('login', 'Please follow the link for email confirm:') ?>

<?= Html::encode($link) ?>

<?= Yii::t('login', 'If you did not register on our site, then just delete this email.') ?>