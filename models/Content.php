<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper as AH;
use app\helpers\Common as CH;

/**
 * This is the model class for table "contents".
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
 * @property int $parent_id
 * @property int $sequence
 * @property int $show
 * @property int $showmenu
 * @property int $created_at
 * @property int $updated_at
 */
class Content extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contents';
    }

    const MAIN_ID = 1;
    const ARTICLES_ID = 13;
    const NEWS_ID = 12;
    const ABOUT_ID = 10;
    const DELIVERY_ID = 11;
    const CONTACTS_ID = 14;

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

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['name_ua', 'name_ru'], 'required'],
            ['slug', 'filter', 'filter' => function($value) {
                return CH::toSlug($value);
            }],
            [['slug'], 'string', 'max' => 255],
            ['slug', function($attribute, $params, $validator) {
                $error = false;
                if ($this->id == static::MAIN_ID) {
                    if ($this->slug) {
                        $error = 'Main slug must be empty';
                    }
                } else {
                    if (!$this->slug) {
                        $error = 'Slug must not be empty';
                    } elseif ($this->slug != preg_replace("!([^a-z0-9-]+)!si","-", $this->slug)) {
                        $error = 'Slug must contain latin symbols, numeral and hyphen';
                    }
                }
                if ($error) {
                    $this->addError($attribute, Yii::t('admin', $error));
                }
            }, 'skipOnEmpty' => false],
            [['slug'], 'unique'],
            [['name_ua', 'name_ru', 'title_ua', 'title_ru'], 'string', 'max' => 191],
            [['keywords_ua', 'keywords_ru', 'description_ua', 'description_ru', 'text_ua', 'text_ru', 'img'], 'string', 'max' => 65535],
            [['parent_id','sequence'], 'filter', 'filter' => function($value) {
                return intval($value);
            }],
            ['parent_id', function($attribute, $params, $validator) {
                if ($this->parent_id) {
                    $parent = static::findOne($this->parent_id);
                    if (!$parent) {
                        $this->addError($attribute, Yii::t('admin', 'Parent page does not exist'));
                    }
                }
            }],
            [['show', 'showmenu'], 'filter', 'filter' => function($value) {
                return $this->isNewRecord ? 1 : ($value ? 1 : 0);
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
            'parent_id' => Yii::t('admin', 'Parent ID'),
            'sequence' => Yii::t('admin', 'Sequence'),
            'show' => Yii::t('admin', 'Show'),
            'showmenu' => Yii::t('admin', 'Showmenu'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }

    public function v($field) {
        return CH::getField($this, $field);
    }

    private static $categories = false;

    /**
     * @return Content[] | bool
     */
    public static function extractCategories() {
        if (!static::$categories) {
            static::$categories = Content::find()
                ->where(['parent_id' => 0, 'showmenu' => 1])
                ->groupBy('contents.id')
                ->orderBy(['sequence' => SORT_ASC])
                ->all();
        }
        return static::$categories;
    }

    public static function forSelect() {
        return AH::map(static::extractCategories(), 'id', 'name_' . CH::$lng);
    }

    protected static $topContents = [];

    public static function getTopContents() {
        if (empty(static::$topContents)) {
            $contents = static::find()
                ->where(['id' => [static::ABOUT_ID, static::DELIVERY_ID, static::CONTACTS_ID/*, static::ARTICLES_ID, static::NEWS_ID*/]])
                ->orderBy(['sequence' => SORT_ASC])
                ->all();
            $rows = [];
            foreach ($contents as $co) {
                $rows[$co->slug] = $co->v('name');
            }
            static::$topContents = $rows;
        }
        return static::$topContents;
    }

    /*public static function fullParents() {
        $parents = static::find()->alias('parents')
            ->select('parents.*')
            ->addSelect(new Expression('concat(parents.name_ua, \' / \', parents.name_ru) as name'))
            ->innerJoin('contents', 'contents.parent_id = parents.id')
            ->groupBy('parents.id')
            ->asArray()
            ->all();
        return AH::map($parents, 'id', 'name');
    }*/
}
