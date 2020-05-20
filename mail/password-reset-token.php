<?php

/**
 * @var \yii\web\User $user
 */
use yii\helpers\Html;
use yii\helpers\Url;

$link = Url::to(['change-password', 'token' => $user->password_reset_token], true);
?>

<div class="password-reset">
    <p><?= Yii::t('login', 'Hello {username}!', ['username' => Html::encode($user->username)]) ?></p>
    <p><?= Yii::t('login', 'Follow the link below to reset your password:') ?></p>
    <p><?= Html::a(Html::encode($link), $link) ?></p>
    <p><?= Yii::t('login', 'If you did not try to reset your password, then just delete this email.') ?></p>
</div>