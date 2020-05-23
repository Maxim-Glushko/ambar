<?php

namespace app\models;

use app\helpers\Common as CH;
use app\helpers\Morph;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper as AH;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "products".
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
 * @property string $price
 * @property string $discount цена со скидкой
 * @property int $measure за сколько
 * @property int $unit_id в каких единицах
 * @property string $vendorcode
 * @property int $availability наличие
 * @propery bool $recommended рекомендуем
 * @property int $sequence очередность в показе
 * @property int $status 0 - не показывать вовсе, 123 скоро выпуск, снят с производства, ожидается поставка...
 * @property int $created_at
 * @property int $updated_at
 */
class Product extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    public $picture_data;
    public $file;
    public $cat_id;
    public $cat_ids;

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

    public static function statuses() {
        return (Yii::$app->language == 'ru-RU')
            ? [
                0 => 'не показывать',
                1 => 'в наличии',
                2 => 'заканчивается',
                3 => 'ожидается',
                4 => 'нет в наличии',
                5 => 'снято с производства',
                6 => 'акция',
            ] : [
                0 => 'не показувати',
                1 => 'в наявності',
                2 => 'закінчується',
                3 => 'очікується',
                4 => 'немає в наявності',
                5 => 'знято з виробництва',
                6 => 'акція',
            ];
    }

    const STATUS_UNAVAILABLE = 4;

    public function lngStatus() {
        return AH::getValue(
            static::statuses(),
            ($this->availability > 0) ? $this->status : $this::STATUS_UNAVAILABLE,
            ''
        );
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
            ['slug', 'string', 'max' => 255],
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

            [['name_ua', 'name_ru', 'title_ua', 'title_ru', 'keywords_ua', 'keywords_ru'], 'string', 'max' => 191],
            [['description_ua', 'description_ru', 'text_ua', 'text_ru'], 'string', 'max' => 65535],
            [['measure', 'price', 'discount'], 'number'],
            [['unit_id', 'availability', 'sequence', 'status'], 'integer'],
            [['vendorcode'], 'string', 'max' => 255],
            [['recommended'], 'boolean'],
            [['cat_id', 'cat_ids', 'picture_data'], 'safe']
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
            'price' => Yii::t('admin', 'Price'),
            'discount' => Yii::t('admin', 'Price with discount'),
            'measure' => Yii::t('admin', 'Measure'),
            'unit_id' => Yii::t('admin', 'Unit'),
            'vendorcode' => Yii::t('admin', 'Vendorcode'),
            'availability' => Yii::t('admin', 'Availability'),
            'recommended' => Yii::t('admin', 'Recommended'),
            'sequence' => Yii::t('admin', 'Sequence'),
            'status' => Yii::t('admin', 'Status'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
            'cat_id' => Yii::t('admin', 'Main Category'),
            'cat_ids' => Yii::t('admin', 'Categories'),
        ];
    }

    /** @return ActiveQuery */
    public function getProductContent() {
        return $this->hasMany(ProductContent::class, ['product_id' => 'id']);
    }

    /** @return ActiveQuery */
    public function getProductContentMain() {
        return $this->hasOne(ProductContent::class, ['product_id' => 'id'])
            ->where(['main' => 1]);
    }

    /** @return ActiveQuery */
    public function getContents() {
        return $this->hasMany(Content::class, ['id' => 'content_id'])
            ->via('productContent');
    }

    /** @return ActiveQuery */
    public function getContent() {
        return $this->hasOne(Content::class, ['id' => 'content_id'])
            ->via('productContentMain');
    }

    /** @return ActiveQuery */
    public function getPictures() {
        return $this->hasMany(Picture::class, ['item_id' => 'id'])
            ->where(['item_type' => Morph::PRODUCT]);
    }

    /** @return ActiveQuery */
    public function getPicture() {
        return $this->hasOne(Picture::class, ['item_id' => 'id'])
            ->where(['item_type' => Morph::PRODUCT, 'sequence' => 1]);
    }

    /** @return ActiveQuery */
    public function getUnit() {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    /*public static $sorts = [
        'default-asc' => ['sequence' => SORT_ASC], // если не указывать $sort - первый будет умолчанием
        'default-desc' => ['sequence' => SORT_DESC],
        'price-asc' => ['price' => SORT_ASC],
        'price-desc' => ['price' => SORT_DESC],
    ];*/

    public static $maxPage;

    /**
     * @param $contentId
     * @param $page
     * @param $sort
     * @return array|ActiveRecord[]
     */
    /*public static function findByParams($contentId, $page, $sort)
    {
        $orderBy = AH::getValue(self::$sorts, $sort, false)
            ?? self::$sorts[array_key_first(self::$sorts)];
        $query = self::find()
            ->innerJoin('product_content', 'products.id = product_content.product_id');
            //->innerJoin('contents', 'contents.id = product_content.content_id')

        if ($contentId == 1) {
            $query
                ->with(['picture', 'unit', 'content'])
                ->where(['recommended' => 1])
                ->orderBy('RAND()')
                ->limit(24);
        } else {
            $query
                ->with(['picture', 'unit'])
                ->where(['product_content.content_id' => $contentId])
                ->limit(24)
                ->offset(($page - 1) * 24)
                ->orderBy($orderBy);
        }
        $query
            ->andWhere('products.availability > 0')
            ->all();

        static::$maxPage = floor($query->count() / 24 - 1) + 1;

        return $query->all();
    }*/

    /**
     * @param $field string
     * @return string
     */
    public function v($field) {
        return CH::getField($this, $field);
    }

    public function curPrice() {
        return (($this->discount * 1) && ($this->discount < $this->price))
            ? ($this->discount * 1)
            : ($this->price * 1);
    }

    /**
     * @param $id int
     * @param $cat_id int
     * @param $cat_ids array
     */
    public static function contentSync($id, $cat_id, $cat_ids) {
        $product = static::findOne($id);
        if ($product) {
            $originCatId =$product->productContentMain->content_id ?? 0;
            $originSequence = $product->sequence;
            $cat_ids = (empty($cat_ids) || !is_array($cat_ids)) ? [] : $cat_ids;
            if (!empty($cat_id)) {
                array_unshift($cat_ids, $cat_id);
            }
            $cat_ids = array_unique($cat_ids);
            if (empty($cat_ids)) {
                $cat_ids[] = array_keys(Content::forSelect())[0];
            }
            ProductContent::deleteAll(['product_id' => $id]);
            $i = 0;
            foreach ($cat_ids as $c_id) {
                if (!$i) {
                    $nextCatId = $c_id;
                }
                $pc = new ProductContent();
                $pc->product_id = $id;
                $pc->content_id = $c_id;
                $pc->main = $i ? 0 : 1;
                $pc->save(false);
                $i++;
            }
            if ($originCatId <> $nextCatId) {
                // на старом месте всех сестёр с большим sequence сделать его на единицу меньше
                $sisters = static::find()
                    ->innerJoin('product_content', 'product_content.product_id = products.id')
                    ->where(['product_content.main' => 1, 'product_content.content_id' => $originCatId])
                    ->andWhere('products.sequence > :sequence', ['sequence' => $originSequence])
                    ->all();
                if ($sisters && count($sisters)) {
                    $sis_ids = AH::getColumn($sisters, 'id');
                    Product::updateAllCounters(['sequence' => -1], ['id' => $sis_ids]);
                }
                // на новом месте узнать максимальный sequence и сделать его у нашего продукта на единицу больше
                $newSequence = static::find()
                    ->innerJoin('product_content', 'product_content.product_id = products.id')
                    ->where(['product_content.main' => 1, 'product_content.content_id' => $nextCatId])
                    ->max('products.sequence') + 1;
                $product->sequence = $newSequence;
                $product->save(false);
            }
        } else {
            ProductContent::deleteAll(['product_id' => $id]);
        }
    }
}
