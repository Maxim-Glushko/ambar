<?php

/**
 * @var \yii\web\User $user
 */
use yii\helpers\Html;
use yii\helpers\Url;

$link = Url::to(['change-password', 'token' => $user->password_reset_token], true);
?>

<?= Yii::t('login', 'Hello {username}!', ['username' => Html::encode($user->username)]) ?>
<?= Yii::t('login', 'Follow the link below to reset your password:') ?>
<?= $link ?>
<?= Yii::t('login', 'If you did not try to reset your password, then just delete this email.') ?>
