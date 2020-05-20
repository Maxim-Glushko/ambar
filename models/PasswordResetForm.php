<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use himiklab\yii2\recaptcha\ReCaptchaValidator3;

/**
 * Password reset request form
 */
class PasswordResetForm extends Model
{
    public $email;
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'message' => Yii::t('login', 'There is no user with such email.')
            ],
        ];
        if (!YII_ENV_DEV) {
            $rules[] = [['reCaptcha'], ReCaptchaValidator3::class,
                'threshold' => 0.5,
                'action' => 'homepage',
            ];
        }
        return $rules;
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    /**
     * @return bool
     * @throws Exception
     */
    public function sendEmail() {
        $user = User::findOne(['email' => $this->email]);
        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app->mailer->compose(
            ['html' => 'password-reset-token', 'text' => 'password-reset-token-txt'],
            ['user' => $user]
        )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }

}