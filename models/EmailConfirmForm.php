<?php

namespace app\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;

class EmailConfirmForm extends Model {
    /**
     * @var User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  int $id
     * @param  string $token
     * @param  array $config
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($id, $token, $config = []) {
        if (empty($id) || empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(Yii::t('login', 'Email confirm token cannot be blank.'));
        }
        $this->_user = User::findOne($id);
        if (!$this->_user || ($this->_user->email_confirm_token != $token)) {
            throw new InvalidArgumentException(Yii::t('login', 'Wrong token.'));
        }
        parent::__construct($config);
    }

    /**
     * Confirm email.
     *
     * @return boolean if email was confirmed.
     */
    public function confirmEmail() {
        $user = $this->_user;
        $user->confirmed_at = time();
        $user->removeEmailConfirmToken();
        return $user->save();
    }
}
