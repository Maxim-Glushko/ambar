<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\Exception;


/**
 * User model
 *
 * @property integer $id
 * @property string $email
 * @property string $username
 * @property string $phone
 * @property string $address
 * @property string $role
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password write-only password
 *
 * @property string $email_confirm_token
 * @property string $password_reset_token
 *
 * @property integer $registered_at
 * @property integer $confirmed_at
 * @property integer $last_visit
 * @property integer $ban_expiration
 * @property string $comment
 *
 */
class User extends ActiveRecord implements IdentityInterface {

    /**
     * @return string
     */
    public static function tableName() {
        return 'users';
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'username' => Yii::t('admin', 'Username'),
            'email' => Yii::t('admin', 'Email'),
            'phone' => Yii::t('admin', 'Phone'),
            'address' => Yii::t('admin', 'Address'),
            'role' => Yii::t('admin', 'Role'),
            'registered_at' => Yii::t('admin', 'RegisteredAt'),
            'confirmed_at' => Yii::t('admin', 'ConfirmedAt'),
            'last_visit' => Yii::t('admin', 'LastVisit'),
            'comment' => Yii::t('admin', 'Comment'),
        ];
    }

    /** @return array */
    public function rules() {
        return [
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

            ['phone', 'match', 'pattern' => static::$phone_pattern,
                'message' => Yii::t('common', 'Phone has incorrect format')],
            ['phone', 'filter', 'filter' => function($v) {
                return User::phoneConvert(trim($v));
            }, 'when' => function($model) {
                return trim($model->phone);
            }],
            ['address', 'string', 'max' => 2000],
            ['comment', 'string', 'max' => 65535],
        ];
    }

    public function tabooValidate($attribute, $params) {
        $error = static::hasTaboo($this->$attribute);
        if ($error) {
            $this->addError($attribute, $error);
        }
    }

    // думал ещё греческий дописать, но добавлю по мере надобности
    public static $username_pattern = '/^[a-zA-Zа-яА-ЯїЇіІєЄґҐёЁўЎZąćęłńóśźżĄĆĘŁŃÓŚŹŻÀ-ÿ]'
        . '[a-zA-Zа-яА-ЯїЇіІєЄґҐёЁўЎZąćęłńóśźżĄĆĘŁŃÓŚŹŻÀ-ÿ\w\s\'\.,_-]*$/u';
    // Русский [а-яА-ЯёЁ]
    // Украинский [а-щА-ЩЬьЮюЯяЇїІіЄєҐґ']
    // Белорусский [ёа-зй-шы-яЁА-ЗЙ-ШЫІіЎў]
    // Польский [a-pr-uwy-zA-PR-UWY-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]
    //      В польском языке нет заглавных букв Q, V and X; польский + латиница: [a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]
    // Болгарский [а-ъьюяА-ЪЬЮЯ]
    // Сербский [А-ИК-ШЂЈ-ЋЏа-ик-шђј-ћџ]
    // Вся латиница плюс акцентированные символы (?![×÷])[A-Za-zÀ-ÿ]
    // Греческий и коптский вместе [\u0370-\u03FF\u1F00-\u1FFF]
    // Испанский [a-zA-ZáéíñóúüÁÉÍÑÓÚÜ]
    // Итальянский [a-zA-ZàèéìíîòóùúÀÈÉÌÍÎÒÓÙÚ]
    // Немецкий алфавит [a-zA-ZäöüßÄÖÜẞ]
    // Норвежский [a-zA-ZæøåÆØÅ]
    // Румынский [a-zA-ZĂÂÎȘȚăâîșț]
    // Французский [a-zA-ZàâäôéèëêïîçùûüÿæœÀÂÄÔÉÈËÊÏÎŸÇÙÛÜÆŒ]
    // Шведский [a-zA-ZäöåÄÖÅ]

    public static $taboo = [
        'сучара', 'хуйня', 'блят', 'бляд', 'пидарас', 'ипись', 'изъеб', 'еблан', 'ебеный', 'ебущий', 'ебанашка',
        'ебырь', 'хуище', 'гребан', 'уебище', 'уебан', 'феееб', '6ляд', 'сцука', 'ебали', 'пестато', 'ебало', 'ебли',
        'ебло', 'ебанут', 'ебут', 'заебу', 'выебу', 'хуйло', 'нехе', 'неху', 'ниху', 'нихе', 'ибанут', 'fuck', 'хули',
        'хуля', 'хуе', 'хуё', 'мудл', 'хер', 'пидар', 'наху', 'педер', 'пидер', 'пидир', 'ёбну', 'ебну', 'ебыр', 'заеб',
        'заёб', 'ебен', 'блятc', 'ебли', 'аебли', 'ебло', 'заебло', 'переебло', 'отебло', 'отъебло', 'отьебло', 'ебеш',
        'выеб', 'отъеб', 'отьеб', 'перееб', 'хуйла', 'заеб', 'хую', 'иннах', '6ля', '6ля', 'блЯ', 'бля', 'бля', 'хуило',
        'хуюше', 'сука', 'ъеб', 'ъёб', 'бляд', 'блябу', 'бля бу', 'залупа', 'хера', 'пизжен', 'ёпта', 'епта',
        'пистапол', 'пизда', 'залупить', 'ебать', 'мудо', 'манда', 'мандавошка', 'мокрощелка', 'муда', 'муде', 'муди',
        'мудн', 'мудо', 'пизд', 'хуе', 'похую', 'похуй', 'охуи', 'ебля', 'пидорас', 'пидор', 'херн', 'щлюха', 'хуй',
        'нах', 'писдеш', 'писдит', 'писдиш', 'нехуй', 'ниибаца',

        'admin', 'moder', 'админ', 'модер',
    ];

    public static function hasTaboo($string) {
        $string = mb_strtolower($string);
        foreach (static::$taboo as $taboo) {
            if (strpos($string, $taboo) !== false) {
                return Yii::t('login', 'Nick contains forbidden words.');
                // мандарин, употреблять, учёба, скипидар, ребус и плохую потом доделаю
                // пока мандарины пусть регистрируются на других сайтах
            }
        }
        return false;
    }

    // главное, чтобы после +380 были 9 цифр
    public static $phone_pattern = '/^\+3[-\s]*8[-\s]*0[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}[-\s]*\d{1}$/';

    public static function phoneConvert($phone) {
        $phone = preg_replace('/\D+/', '', $phone);
        $length = strlen($phone);
        $phone = substr($phone, $length - 9);
        return $phone;
    }

    public static $allowableRoles = ['admin', 'manager', 'customer'];

    public static function rolesForSelect() {
        $result = [];
        foreach (static::$allowableRoles as $r) {
            $result[$r] = $r;
        }
        return $result;
    }

    /*public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            echo 'true';
            return true;
        }
        echo 'false';
        return false;
    }*/

    /**
     * @param int|string $id
     * @return User|null|IdentityInterface
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return void|IdentityInterface
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    /**
     * @param $email
     * @return User|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email]);
    }

    /**
     * @return int|mixed|string
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param $password
     * @throws Exception
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @param $token
     * @return User|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne(['password_reset_token' => $token]);
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        return ($timestamp + Yii::$app->params['user.passwordResetTokenExpire'] >= time());
    }

    /**
     * @throws Exception
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString(7) . '_' . time();
    }

    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * @param string $email_confirm_token
     * @param int $id
     * @return static|null
     */
    public static function findByEmailConfirmToken($token, $id) {
        $user = static::findOne(['id' => $id]);
        return ($user && ($user->email_confirm_token == $token)) ? $user : null;
    }

    /**
     * Generates email confirmation token
     * @throws Exception
     */
    public function generateEmailConfirmToken() {
        $this->email_confirm_token = Yii::$app->security->generateRandomString(7);
        // т.к. у меня добавляется user_id, длина уже не имеет такого значения
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmToken() {
        $this->email_confirm_token = null;
    }

    /**
     * @return false|string
     */
    public function isBanned() {
        return (!$this->ban_expiration || ($this->ban_expiration > time()))
            ? false // не забанен
            : date('d.m.Y H:i:s', $this->ban_expiration); // дата выхода из бана
    }

    /**
     * @return false|string
     */
    public function isNotConfirmed() {
        return $this->confirmed_at
            ? false // имейл подтверждён
            : date('d.m.Y H:i:s', (int) $this->registered_at + Yii::$app->params['user.emailConfirmTokenExpire']);
                // крайняя дата подтверждения аккаунта
    }

    /**
     * @return int
     */
    // в крон, например, чтобы короткие ники не занимали те, кто и не собирался оставаться на сайте
    public static function notConfirmedDelete() {
        return static::deleteAll([
            'confirmed_at' => NULL,
            ['>', 'registered_at', time() - Yii::$app->params['user.emailConfirmTokenExpire']]
        ]);
    }

    public function setRole($roleSlug) {
        if (!in_array($roleSlug, self::$allowableRoles)) {
            return false;
        }
        $this->role = $roleSlug;
        $this->save(false);
        return true;
    }

    public function isConfirmed() {
        return (bool) $this->confirmed_at;
    }

    public function isAdmin() {
        return $this->role == 'admin';
    }

    public function isManager() {
        return $this->role == 'manager';
    }

    public function isCustomer() {
        return $this->role == 'customer';
    }

    public function isAdminOrManager() {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function getOrders() {
        return $this->hasMany(Order::class, ['user_id' => 'id']);
    }


}
