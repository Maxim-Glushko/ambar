<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$link = Url::to(['email-confirm', 'id' => $user->id, 'token' => $user->email_confirm_token], true);
?>

<p><?= Yii::t('login', 'Hello {username}!', ['username' => Html::encode($user->username)]) ?></p>

<p><?= Yii::t('login', 'Please follow the link for email confirm:') ?></p>

<p><?= Html::a(Html::encode($link), $link) ?></p>

<p><?= Yii::t('login', 'If you did not register on our site, then just delete this email.') ?></p>