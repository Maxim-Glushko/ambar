<?php

namespace app\models;

use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator3;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    public $reCaptcha;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
        if (!YII_ENV_DEV) {
            $rules[] = [['reCaptcha'], ReCaptchaValidator3::class,
                'threshold' => 0.5,
                'action' => 'homepage',
            ];
        }
        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('login', 'Email'),
            'password' => Yii::t('login', 'Password'),
            'rememberMe' => Yii::t('login', 'Remember Me'),
        ];
    }

    /**
     * Validates the username and password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('login', 'Incorrect email or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            //$this->_user = User::findByUsername($this->username);
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
}
