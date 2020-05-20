<?php

namespace app\helpers;

use app\models\User;
use app\models\Content;
use app\models\Product;
use app\models\News;
use app\models\Article;
use app\models\Picture;
use app\models\Cart;
use app\models\Order;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use Yii;


class Morph
{
    /**
     * для полиморфных связей: item_type + item_id (как в laravel: commentable_type + commentable_id);
     * для того, чтобы не вставлять в поле item_type значение 'users' или '\App\Models\User';
     * лучше туда сунуть число; а чтобы циферки не хранить в голове - есть словесные константы
     */
    const USER                  = 1;
    const CONTENT               = 2;
    const PRODUCT               = 3;
    const NEWS                  = 4;
    const ARTICLE               = 5;
    const PICTURE               = 6;
    const CART                  = 7;
    const ORDER                 = 8;

    private static function data() {
        return [
            static::USER => [
                'prefix' => 'user',
                'prefix2' => 'users',
                'class' => User::class,
                'table' => User::tableName()
            ],
            static::CONTENT => [
                'prefix' => 'content',
                'prefix2' => 'contents',
                'class' => Content::class,
                'table' => Content::tableName()
            ],
            static::PRODUCT => [
                'prefix' => 'product',
                'prefix2' => 'products',
                'class' => Product::class,
                'table' => Product::tableName()
            ],
            static::NEWS => [
                'prefix' => 'news',
                'prefix2' => 'news',
                'class' => News::class,
                'table' => News::tableName()
            ],
            static::ARTICLE => [
                'prefix' => 'article',
                'prefix2' => 'articles',
                'class' => Article::class,
                'table' => Article::tableName()
            ],
            static::PICTURE => [
                'prefix' => 'picture',
                'prefix2' => 'pictures',
                'class' => Picture::class,
                'table' => Picture::tableName()
            ],
            static::CART => [
                'prefix' => 'cart',
                'prefix2' => 'carts',
                'class' => Cart::class,
                'table' => Cart::tableName()
            ],
            static::ORDER => [
                'prefix' => 'order',
                'prefix2' => 'orders',
                'class' => Order::class,
                'table' => Order::tableName()
            ],
        ];
    }

    /**
     * @param ActiveRecord $model
     * @return bool|int
     */
    public static function getItemType($model) {
        $data = static::data();
        $class = get_class($model);
        foreach ($data as $itemType => $d) {
            if ($d['class'] == $class) {
                return $itemType;
            }
        }
        return false;
    }

    public static function getItemTypeForPrefix2($prefix2) {
        $data = static::data();
        foreach ($data as $k => $v) {
            if ($v['prefix2'] == $prefix2) {
                return $k;
            }
        }
        return 0;
    }

    /**
     * @param int $itemType
     * @return string
     */
    public static function getClass($itemType) {
        return static::data()[$itemType]['class'];
    }

    /**
     * @param int $itemType
     * @return string
     */
    public static function getPrefix($itemType) {
        return static::data()[$itemType]['prefix'];
    }

    /**
     * @param int $itemType
     * @return string
     */
    public static function getPrefix2($itemType) {
        return static::data()[$itemType]['prefix2'];
    }

    /**
     * @param $itemType
     * @return mixed
     */
    public static function getTableName($itemType) {
        $class = static::data()[$itemType]['class'];
        return $class::tableName();
    }

    public static function getItemTypes() {
        return array_keys(static::data());
    }
}
