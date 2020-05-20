<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper as AH;
use app\helpers\Common as CH;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $slug
 * @property string $value_ua
 * @property string $value_ru
 */
class Setting extends ActiveRecord
{
    /** {@inheritdoc} */
    public static function tableName()
    {
        return 'settings';
    }

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['value_ua', 'value_ru'], 'string'],
            [['slug'], 'string', 'max' => 255],
        ];
    }

    /** {@inheritdoc} */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'slug' => Yii::t('admin', 'Slug'),
            'value_ua' => 'Значення',
            'value_ru' => 'Значение',
        ];
    }

    protected static $allValues = [];

    protected static function makeAllValues()
    {
        if (empty(static::$allValues)) {
            static::$allValues = AH::map(static::find()->all(), 'slug', 'value_' . CH::$lng);
        }
        return static::$allValues;
    }

    public static function v($slug)
    {
        return AH::getValue(static::makeAllValues(), $slug, '');
    }
}
