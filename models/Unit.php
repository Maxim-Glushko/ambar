<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper as AH;
use app\helpers\Common as CH;
use Yii;

/**
 * This is the model class for table "units".
 *
 * @property int $id
 * @property string $name_ua
 * @property string $name_ru
 * @property int $sequence
 * @property int $created_at
 * @property int $updated_at
 */
class Unit extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'units';
    }

    /** @return array */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sequence'], 'integer'],
            [['name_ua', 'name_ru'], 'string', 'max' => 191],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'name_ua' => Yii::t('admin', 'Name Ua'),
            'name_ru' => Yii::t('admin', 'Name Ru'),
            'sequence' => Yii::t('admin', 'Sequence'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }

    public function v($field) {
        return CH::getField($this, $field);
    }

    protected static $unitSelect = [];

    public static function forSelect() {
        if (empty(static::$unitSelect)) {
            $models = static::find()->orderBy('sequence')->all();
            static::$unitSelect = array_merge([0 => '-'], AH::map($models, 'id', 'name_' . CH::$lng));
        }
        return static::$unitSelect;
    }
}
