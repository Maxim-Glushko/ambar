<?php

namespace app\models;

use app\helpers\Common as CH;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $slug
 * @property string $name_ua
 * @property string $name_ru
 * @property string $title_ua
 * @property string $title_ru
 * @property string $keywords_ua
 * @property string $keywords_ru
 * @property string $description_ua
 * @property string $description_ru
 * @property string $text_ua
 * @property string $text_ru
 * @property string $img
 * @property int $show 0 - не показывать
 * @property int $created_at
 * @property int $updated_at
 * @property int $published_at если нужно заранее запланировать публикацию
 */
class News extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    public $file;

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
            [['slug', 'name_ua', 'name_ru'], 'required'],


            ['slug', 'filter', 'filter' => function($value) {
                return CH::toSlug($value);
            }],
            [['slug'], 'string', 'max' => 255],
            ['slug', function($attribute, $params, $validator) {
                $error = false;
                if (!$this->slug) {
                    $error = 'Slug must not be empty';
                } elseif ($this->slug != preg_replace("!([^a-z0-9-]+)!si","-", $this->slug)) {
                    $error = 'Slug must contain latin symbols, numeral and hyphen';
                }
                if ($error) {
                    $this->addError($attribute, Yii::t('admin', $error));
                }
            }, 'skipOnEmpty' => false],
            [['slug'], 'unique'],

            [['name_ua', 'name_ru', 'title_ua', 'title_ru'], 'string', 'max' => 191],
            [['keywords_ua', 'keywords_ru', 'description_ua', 'description_ru', 'text_ua', 'text_ru', 'img'], 'string', 'max' => 65535],

            [['show'], 'integer'],

            ['published_at', 'filter', 'filter' => function($value) {
                return strtotime($value);
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'slug' => Yii::t('admin', 'Slug'),
            'name_ua' => Yii::t('admin', 'Name Ua'),
            'name_ru' => Yii::t('admin', 'Name Ru'),
            'title_ua' => Yii::t('admin', 'Title Ua'),
            'title_ru' => Yii::t('admin', 'Title Ru'),
            'keywords_ua' => Yii::t('admin', 'Keywords Ua'),
            'keywords_ru' => Yii::t('admin', 'Keywords Ru'),
            'description_ua' => Yii::t('admin', 'Description Ua'),
            'description_ru' => Yii::t('admin', 'Description Ru'),
            'text_ua' => Yii::t('admin', 'Text Ua'),
            'text_ru' => Yii::t('admin', 'Text Ru'),
            'img' => Yii::t('admin', 'Img'),
            'show' => Yii::t('admin', 'Show'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
            'published_at' => Yii::t('admin', 'Published At'),
        ];
    }

    public function v($field) {
        return CH::getField($this, $field);
    }
}
