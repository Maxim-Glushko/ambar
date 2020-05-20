<?php

namespace app\models;

use app\helpers\Morph;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use app\helpers\Common as CH;
use yii\helpers\ArrayHelper as AH;
use yii\helpers\Html;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int $status
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property string $note примечания заказчика
 * @property string $comment комментарий оператора, обслуживающего заказ
 * @property string $sum
 * @property string $data json разных данных
 * @property int $created_at
 * @property int $updated_at
 */
class Order extends ActiveRecord
{
    /** {@inheritdoc} */
    public static function tableName()
    {
        return 'orders';
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

    public static function statuses() {
        return (CH::$lng == 'ru')
            ? [
                1 => 'заказан',
                2 => 'принят менеджером',
                3 => 'отказ покупателя',
                4 => 'отклонён менеджером',
                5 => 'отправлен',
                6 => 'доставлен',
                7 => 'покупатель отказался после доставки'
            ] : [
                1 => 'замовлено',
                2 => 'прийнято менеджером',
                3 => 'відмова покупця',
                4 => 'відхилено менеджером',
                5 => 'відправлено',
                6 => 'доставлено',
                7 => 'покупець відмовився після доставки'
            ];
    }

    // статусы, при которых покупатель расплатился
    public static $successfulStatuses = [5, 6];

    public static $customerImages = [
        '/static/servimg/customer1.jpg',
        '/static/servimg/customer2.jpg',
        '/static/servimg/customer3.jpg',
        '/static/servimg/customer4.jpg',
        '/static/servimg/customer5.jpg',
        '/static/servimg/customer6.jpg',
        '/static/servimg/customer7.jpg',
    ];

    public static function customerImg() {
        $rand = mt_rand(0, count(static::$customerImages) - 1);
        return static::$customerImages[$rand];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'status', 'name', 'address', 'comment', 'note', 'data', 'sum', 'user_id', 'phone'], 'safe'],
            [['key', 'name', 'address', 'comment', 'note', 'data', 'phone'], 'string'],
            [['sum'], 'number'],
            [['status'], 'integer'],
            ['phone', 'filter', 'filter' => function($v) {
                return User::phoneConvert(trim($v));
            }, 'when' => function($model) {
                return trim($model->phone);
            }],
            ['name', 'filter', 'filter' => function($v) {
                $v = trim($v);
                return (strlen($v) > Yii::$app->params['user.maxUsernameLength'])
                    ? substr($v, 0, Yii::$app->params['user.maxUsernameLength'])
                    : $v;
            }, 'when' => function($model) {
                return trim($model->name);
            }],
            [['address', 'note'], 'filter', 'filter' => function($v) {
                $v = trim($v);
                return (strlen($v) > 1000) ? substr($v, 0, 1000) : $v;
            }, 'when' => function($model) {
                return trim($model->name);
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
            'user_id' => Yii::t('admin', 'Registration'),
            'name' => Yii::t('admin', 'UserName'),
            'key' => Yii::t('admin', 'Key'),
            'status' => Yii::t('admin', 'Status'),
            'note' => Yii::t('admin', 'Note'),
            'comment' => Yii::t('admin', 'ManagerComment'),
            'sum' => Yii::t('admin', 'Sum'),
            'data' => Yii::t('admin', 'Data'),
            'created_at' => Yii::t('admin', 'Date'),
            'updated_at' => Yii::t('admin', 'Updated At'),
        ];
    }

    protected static $old = false;

    public static function oldInfa()
    {
        if (static::$old === false) {
            static::$old = ['name' => [], 'phone' => [], 'address' => []];
            $key = Cart::extractKey();
            $query = static::find()->where(['key' => $key]);
            if (!Yii::$app->user->isGuest) {
                $query->orWhere(['user_id' => Yii::$app->user->id]);
            }
            // TODO можно также выбирать только среди удачных заказов
            $models = $query->all();
            if ($models && count($models)) {
                foreach ($models as $m) {
                    foreach (['name', 'phone', 'address'] as $key) {
                        if (!empty($m->$key)) {
                            $value = (($key == 'phone') ? '+380' : '') . $m->$key;
                            if (!in_array($value, static::$old[$key])) {
                                static::$old[$key][$value] = $value;
                            }
                        }
                    }
                }
            }
        }
        return static::$old;
    }

    /** @return ActiveQuery */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /** @return ActiveQuery */
    public function getOrderProducts() {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'id']);
    }

    public static function namesForSelect() {

    }

    protected static $customers = [];

    public static function usersForSelect() {
        if (empty(static::$customers)) {
            $users = User::find()
                ->innerJoin('orders', 'orders.user_id = users.id')
                ->distinct(['id', 'username', 'email'])->all();
            if ($users && count($users)) {
                $result = [];
                foreach ($users as $user) {
                    $result[$user->id] = $user->username
                        ? (Html::encode($user->username) . ' (' . $user->email . ')')
                        : $user->email;
                }
                static::$customers = $result;
            }
        }
        return static::$customers;
    }

    protected static $selectProducts = [];

    public static function productsForSelect() {
        if (empty(static::$selectProducts)) {
            $products = Product::find()
                ->innerJoin('order_product', 'products.id = order_product.product_id')
                ->all();
            if ($products && count($products)) {
                $result = [];
                foreach ($products as $product) {
                    $result[$product->id] = $product->v('name');
                }
                static::$selectProducts = $result;
            }
        }
        return static::$selectProducts;
    }

    // сколько заказал юзер с этим ключом + с этого аккаунта
    public function orderCounter() {
        $query = static::find()
            ->where([
                'key' => $this->key,
                //'status' => static::$successfulStatuses
            ]);
        if (!Yii::$app->user->isGuest && $this->user_id) {
            $query->orWhere([
                'user_id' => $this->user_id,
                //'status' => static::$successfulStatuses
            ]);
        }
        echo $query->count();
    }

    // на какую сумму заказал юзер с этим ключом + с этого аккаунта
    public function ordersSum() {
        $query = static::find()
            ->where([
                'key' => $this->key,
                //'status' => static::$successfulStatuses
            ]);
        if (!Yii::$app->user->isGuest && $this->user_id) {
            $query->orWhere([
                'user_id' => $this->user_id,
                //'status' => static::$successfulStatuses
            ]);
        }
        echo $query->sum('sum') * 1;
    }
}
