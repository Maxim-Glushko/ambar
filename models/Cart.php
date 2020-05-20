<?php

namespace app\models;

use yii\base\DynamicModel;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use Yii;

/**
 * This is the model class for table "carts".
 *
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 */
class Cart extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carts';
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
            [['user_id'], 'integer'],
            [['key'], 'string', 'max' => 23],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'ID'),
            'key' => Yii::t('admin', 'Key'),
            'user_id' => Yii::t('admin', 'User ID'),
            'created_at' => Yii::t('admin', 'Created At'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }

    /** @return ActiveQuery */
    public function getCartProducts() {
        return $this->hasMany(CartProduct::class, ['cart_id' => 'id']);
    }

    /** @return ActiveQuery */
    /*public function getProducts() {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->via('cartProduct');
    }*/

    /**
     * @param $where
     * @return array|ActiveRecord|null
     */
    public static function findWithProducts($where) {
        return self::find()
            ->with('cartProducts.product.unit', 'cartProducts.product.content')
            ->where($where)
            ->one();
    }

    // ключ, корзина и их вычисление должны быть здесь, а не в контроллере,
    // чтобы оба контроллера (order и cart) могли иметь к обоим доступ

    protected static $cartKey = false;
    protected static $needToMerge = -1;

    public static function extractKey() {
        // извлечь из сессии
        // если в сессии пусто:
        // если юзер залогинен - попытаться извлечь из таблицы carts его актуальный key
        // иначе - сформировать ключ и поместить в сессию (и сюда в  static::$cartKey)
        $session = Yii::$app->session;
        if (!static::$cartKey) {
            static::$cartKey = $session->get('cartKey', false);
            if (!static::$cartKey && !Yii::$app->user->isGuest) {
                $cart = static::find()
                    ->where(['user_id' => Yii::$app->user->id])
                    ->one();
                if ($cart) { // ключ обязателен, поэтому дальнейшие проверки опускаю
                    static::$cartKey = $cart->key;
                }
            }
            if (!static::$cartKey) {
                //10 символов timestamp + 13 случайных символов
                static::$cartKey = substr(time() . uniqid(), 0, 23);
            }
        }
        if (static::$needToMerge === -1) {
            static::$needToMerge = false;
            if (!Yii::$app->user->isGuest) {
                $cartsCount = static::find()
                    ->where(['OR', ['key' => static::$cartKey], ['user_id' => Yii::$app->user->id]])
                    //->where(['key' => static::$cartKey])
                    //->orWhere(['user_id' => Yii::$app->user->id])
                    ->count();
                if ($cartsCount > 1) {
                    static::$needToMerge = true;
                }
            }
        }
        $session->set('cartKey', static::$cartKey);
        return static::$cartKey;
    }

    protected static $currentCart = false;

    /**
     * @param bool $renew если нужно обновить содержимое корзины, а не брать константное
     * @return bool
     */
    public static function extract($renew = false) {
        try {
            if ((static::$currentCart === false) || $renew) {
                $cartKey = static::extractKey();

                // если нужно слияние
                // объединяем, удаляем ненужную корзину, переписываем ключ
                // заново извлекаем и отдаём
                // иначе
                // извлекаем корзину по ключу или user_id
                // если есть авторизация, но нет в базе user_id
                // записываем в базу user_id
                // заново извлекаем и выдаём

                if (static::$needToMerge) {
                    $cart1 = self::findWithProducts(['user_id' => Yii::$app->user->id]);
                    $cart0 = self::findWithProducts(['key' => $cartKey, 'user_id' => 0]);
                    // $cart0 - это корзина, которую набрал юзер, ещё будучи незарегистрированным

                    if ($cart1 && $cart0) { // не могу представить ситуацию, при которой бы это условие не выполнилось
                        $pluses = [];
                        if ($cart0->cartProducts && count($cart0->cartProducts)) {
                            foreach ($cart0->cartProducts as $cp0) {
                                $pluses[$cp0->product_id] = $cp0->quantity;
                            }
                        }
                        CartProduct::deleteAll(['cart_id' => $cart0->id]);
                        $cart0->delete();
                        if ($cart1->cartProducts && count($cart1->cartProducts) && $pluses && count($pluses)) {
                            // прибавляем к тем продуктам, что уже были в корзине
                            foreach ($pluses as $product_id => $quantity){
                                foreach ($cart1->cartProducts as $cp) {
                                    if ($cp->product_id == $product_id) {
                                        CartProduct::updateAll(
                                            ['quantity' => $cp->quantity + $quantity],
                                            ['product_id' => $product_id, 'cart_id' => $cart1->id]
                                        );
                                        unset($pluses[$product_id]);
                                    }
                                }
                            }
                        }
                        if (!empty($pluses) && count($pluses)) { // таких продуктов ещё не было в корзине
                            foreach ($pluses as $product_id => $quantity) {
                                $cartProduct = new CartProduct();
                                $cartProduct->cart_id = $cart1->id;
                                $cartProduct->product_id = $product_id;
                                $cartProduct->quantity = $quantity;
                                $cartProduct->save(false);
                            }
                        }
                        $cart1->key = $cartKey;
                        $cart1->save(false);
                        static::$currentCart = self::findWithProducts(['key' => $cartKey]);
                    } else { // такого в принципе быть не должно, ибо только что было две корзины
                        static::$currentCart = null;
                    }
                } else { // есть только одна корзина, или ни одной
                    if (!Yii::$app->user->isGuest) {
                        $cart = self::findWithProducts(['OR', ['key' => $cartKey], ['user_id' => Yii::$app->user->id]]);
                        if ($cart) {
                            /** @var $cart Cart */
                            if (!$cart->user_id || ($cart->key != $cartKey)) {
                                $cart->user_id = Yii::$app->user->id;
                                $cart->key = $cartKey;
                                $cart->save(false);
                                static::$currentCart = self::findWithProducts(['key' => $cartKey]);
                            } else {
                                static::$currentCart = $cart;
                            }
                        } else {
                            static::$currentCart = null;
                        }
                    } else {
                        static::$currentCart = self::findWithProducts(['key' => $cartKey]);
                    }
                }
            }
            return static::$currentCart;
        } catch (\Exception $e) {
            Yii::error('There is exception in cart forming: ' . $e->getMessage());
            return null;
        } catch (\Throwable $t) {
            Yii::error('There is Throwable in cart forming: ' . $t->getMessage());
            return null;
        }
    }

    /**
     * @return array
     */
    public static function extractProductIds() {
        $productIds = [];
        $cart = static::extract();
        /** @var $cart Cart */
        if ($cart && $cart->cartProducts && count($cart->cartProducts)) {
            foreach ($cart->cartProducts as $cp) {
                $productIds[] = $cp->product->id;
            }
        }
        return $productIds;
    }


    /**
     * @param array $data
     * @return array|bool
     * @throws InvalidConfigException
     */
    public static function add($data)
    {
        $v = DynamicModel::validateData($data, [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity'], 'integer', 'min' => 1],
            ['product_id', 'exist', 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id'],
                'filter' => 'availability > 0'],
        ]);
        if ($v->hasErrors()) {
            return ['error' => $v->firstErrors[0]];
        }

        $cart = static::extract();
        if (!$cart) {
            $cart = new static;
            $cart->key = static::extractKey();
            $cart->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
            $cart->save(false);
        }
        $cp = CartProduct::find()->where(['cart_id' => $cart->id, 'product_id' => $data['product_id']])->one();

        if ($cp) {
            $cp->quantity += $data['quantity'];
        } else {
            $cp = new CartProduct();
            $cp->cart_id = $cart->id;
            $cp->product_id = $data['product_id'];
            $cp->quantity = $data['quantity'];
        }
        $cp->save(false);
        return true;
    }

    public static function change($productId, $quantity) {
        if (!$quantity) {
            $cart = static::extract();
            $cartToDelete = false;
            /** @var Cart $cart */
            if ($cart) {
                if ($cart->cartProducts && count($cart->cartProducts)) {
                    $count = count($cart->cartProducts);
                    if (($count == 1) && ($cart->cartProducts[0]->product_id == $productId)) {
                        $cartToDelete = true;
                    }
                    CartProduct::deleteAll(['cart_id' => $cart->id, 'product_id' => $productId]);
                } else {
                    $cartToDelete = true;
                }
                if ($cartToDelete) {
                    $cart->delete();
                }
            }
            return true;
        } else {
            $product = Product::find()
                ->where(['and', ['id' => $productId], 'availability > 0'])
                ->count();
            if ($product) {
                $cart = static::extract();
                if (!$cart) {
                    $cart = new static;
                    $cart->key = static::extractKey();
                    $cart->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
                    $cart->save();
                    // TODO добавить валидацию и вывод ошибок здесь
                }
                $cp = CartProduct::find()->where(['cart_id' => $cart->id, 'product_id' => $productId])->one();
                if ($cp) {
                    $cp->quantity = $quantity;
                } else {
                    $cp = new CartProduct();
                    $cp->cart_id = $cart->id;
                    $cp->product_id = $productId;
                    $cp->quantity = $quantity;
                }
                $cp->save();
                // TODO добавить валидацию и вывод ошибок здесь

                return true;
            } else {
                return ['error' => Yii::t('common', 'There is not this product')];
            }
        }
    }

    /**
     * @param $data
     * @return array|bool
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public static function plusMinus($data)
    {
        $v = DynamicModel::validateData($data, [
            [['product_id', 'plus_minus'], 'required'],
            ['product_id', 'integer', 'min' => 1],
            ['plus_minus', 'in', 'range' => ['+', '-']],
            ['product_id', 'exist', 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id'],
                'filter' => 'availability > 0'],
        ]);
        if ($v->hasErrors()) {
            return ['error' => $v->firstErrors[0]];
        }

        $cart = static::extract();
        if (!$cart) {
            $cart = new static;
            $cart->key = static::extractKey();
            $cart->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
            $cart->save(false);
        }
        $cp = CartProduct::find()->where(['cart_id' => $cart->id, 'product_id' => $data['product_id']])->one();

        if ($data['plus_minus'] == '+') {
            if ($cp) {
                $cp->quantity += 1;
            } else {
                $cp = new CartProduct();
                $cp->cart_id = $cart->id;
                $cp->product_id = $data['product_id'];
                $cp->quantity = 1;
            }
            $cp->save(false);
        } else {
            if ($cp) {
                if ($cp->quantity > 1) {
                    $cp->quantity -= 1;
                    $cp->save(false);
                } else {
                    $cartToDelete = (!$cart->cartProducts || (count($cart->cartProducts) < 2));
                    CartProduct::deleteAll(['cart_id' => $cart->id, 'product_id' => $data['product_id']]);
                    if ($cartToDelete) {
                        $cart->delete();
                    }
                }
            }
        }
        return true;
    }

    public static function hasProduct($productId) {
        $cart = static::extract();
        /** @var $cart Cart */
        if (!$cart || !$cart->cartProducts || !count($cart->cartProducts)) {
            return 0;
        }
        foreach ($cart->cartProducts as $cp) {
            if ($cp->product_id == $productId) {
                return $cp->quantity;
            }
        }
        return 0;
    }

    /** @return bool */
    public static function isEmptyCart() {
        $cart = static::extract();
        /** @var $cart Cart */
        return (!$cart || !$cart->cartProducts || !count($cart->cartProducts));
    }

    /** @return double */
    public static function cartSum() {
        $cart = static::extract();
        if (static::isEmptyCart()) {
            return 0;
        } else {
            $sum = 0;
            /** @var $cart Cart */
            foreach ($cart->cartProducts as $cp) {
                $price = $cp->product ? $cp->product->curPrice() : 0;
                $sum += $price * $cp->quantity;
            }
            return $sum;
        }
    }
}
