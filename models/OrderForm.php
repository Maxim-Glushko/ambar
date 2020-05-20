<?php

namespace app\models;

use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator3;


class OrderForm extends Model {
    public $name;
    public $phone;
    public $address;
    public $note;
    public $reCaptcha;

    /** @return array */
    public function rules() {
        $rules = [
            ['phone', 'required'],
            [['name', 'address', 'note'], 'safe'],
            [['name', 'phone', 'address', 'note'], 'trim'],
            ['phone', 'match', 'pattern' => User::$phone_pattern,
                'message' => Yii::t('common', 'Phone has incorrect format')],
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
            'name' => Yii::t('common', 'Name'),
            'phone' => Yii::t('common', 'Phone'),
            'address' => Yii::t('common', 'Address'),
            'note' => Yii::t('common', 'Note'),
        ];
    }
}
