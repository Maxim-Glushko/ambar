<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidArgumentException;
use yii\base\Exception;

/**
 * Password reset form
 */
class ChangePasswordForm extends Model
{

    public $password;

    /**
     * @var \app\models\User
     */
    public $_user;

    /**
     * Creates a form model given a token
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = []) {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('login', 'Password reset token cannot be blank.'));
        }

        $this->_user = User::findOne(['password_reset_token' => $token]);

        if (!$this->_user) {
            throw new InvalidArgumentException(\Yii::t('login', 'Wrong password reset token.'));
        }

        if (!$this->_user->confirmed_at) {
            // если он ранее не подтвердил, зачем его два раза гонять,
            // он-то с имейла пришёл по правильному токену, даже если сейчас не введёт новый пароль
            $this->_user->confirmed_at = time();
            $this->_user->save(false);
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.minPasswordLength'],
                'max' => Yii::$app->params['user.maxPasswordLength']],
        ];
    }

    public function attributeLabels() {
        return [
            'password' => Yii::t('login', 'Password'),
        ];
    }

    /**
     * Resets password.
     * @return bool if password was reset.
     * @throws Exception
     */
    public function resetPassword() {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }
}
