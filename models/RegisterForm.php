<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use himiklab\yii2\recaptcha\ReCaptchaValidator3;

/**
 * Class RegisterForm
 * @package app\models
 */
class RegisterForm extends Model {
    public $username;
    public $email;
    public $password;
    public $reCaptcha;
    //public $password_repeat;
    public $verifyCode;

    /** @return array */
    public function rules() {
        $rules = [
            [['username', 'email'], 'trim'],
            ['username', 'filter', 'filter' => function($value) {
                $value = preg_replace('/(\s+)/', ' ', $value);
                $value = preg_replace('/(-+)/', '-', $value);
                $value = preg_replace('/(_+)/', '_', $value);
                $value = preg_replace('/(\.+)/', '.', $value);
                $value = preg_replace('/(,+)/', ',', $value);
                $value = preg_replace('/(\'+)/', '\'', $value);
                return $value;
            }],
            ['username', 'required'],
            /*['username', 'unique', 'targetClass' => User::class,
                'message' => Yii::t('login','This username has already been taken.')],*/
            ['username', 'string', 'min' => 1, 'max' => Yii::$app->params['user.maxUsernameLength']],
            ['username', 'match', 'pattern' => User::$username_pattern,
                'message' => Yii::t('login','Invalid characters in username.')],
            ['username', 'tabooValidate'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'min' => Yii::$app->params['user.minEmailLength'],
                'max' => Yii::$app->params['user.maxEmailLength']],
            ['email', 'unique', 'targetClass' => User::class,
                'message' => Yii::t('login', 'This email address has already been taken.')],
            // также можно пройтись по списку имейлов штрафников, удалённых из таблицы юзеров

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.minPasswordLength'],
                'max' => Yii::$app->params['user.maxPasswordLength']],
            //['password', 'compare']
            //['verifyCode', 'captcha'],
        ];
        if (!YII_ENV_DEV) {
            $rules[] = [['reCaptcha'], ReCaptchaValidator3::class,
                'threshold' => 0.5,
                'action' => 'homepage',
            ];
        }
        return $rules;
    }

    public function attributeLabels() {
        return [
            'email' => Yii::t('login', 'Email'),
            'password' => Yii::t('login', 'Password'),
            //'password_repeat' => Yii::t('login', 'Retype password'),
            'username' => Yii::t('login', 'Username'),
            //'verifyCode' => Yii::t('login', 'Captcha'),
        ];
    }

    public function tabooValidate($attribute, $params) {
        $error = User::hasTaboo($this->$attribute);
        if ($error) {
            $this->addError($attribute, $error);
        }
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function signup() {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->role = 'customer';
            $user->setPassword($this->password);
            $user->registered_at = time();
            //$user->last_visit = time();
            $user->generateAuthKey();
            $user->generateEmailConfirmToken();

            if ($user->save()) {
                Yii::$app->mailer->compose(['html' => 'email-confirm', 'text' => 'email-confirm-txt'],
                        ['user' => $user])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject(Yii::t('login', 'Email confirmation for ') . Yii::$app->name)
                    ->send();
                return $user;
            }
        }
        return null;
    }
}
