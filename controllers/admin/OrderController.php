<?php

namespace app\controllers\admin;

use app\helpers\Common as CH;
use app\models\OrderProduct;
use app\models\Product;
use Yii;
use app\models\Order;
use app\models\admin\OrderSearch;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper as AH;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends AdminController
{
    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action) {
        $this->haveYouGotRoles(['admin', 'manager']);
        return parent::beforeAction($action);
    }
    /**
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

    /**
     * Lists all Order models.
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findFullModel($id, true);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->status == 1) {
                $model->addError('status', Yii::t('admin', 'You must select other status'));
            } elseif (!$model->orderProducts || !count($model->orderProducts)) {
                $model->addError('somebody', Yii::t('admin', 'There is no one Products in the Order'));
            } else {
                $chStatus = $model->isAttributeChanged('status');
                $chComment = $model->isAttributeChanged('comment');
                if ($chStatus || $chComment) {
                    if ($chStatus) {
                        $changes['status'] = $model->status;
                    }
                    if ($chComment) {
                        $changes['comment'] = $model->comment;
                    }
                    $changes['user_id'] = Yii::$app->user->id;
                    $changes['time'] = time();
                    $data = $model->data ? Json::decode($model->data, true) : [];
                    if (!isset($data['changes'])) {
                        $data['changes'] = [];
                    }
                    $data['changes'][] = $changes;
                    $model->data = Json::encode($data);
                }
                if ($model->save()) {
                    if ($model->orderProducts && count($model->orderProducts)) {
                        foreach ($model->orderProducts as $op) {
                            $product = $op->product;
                            $av = $product->availability - $op->quantity;
                            $av = ($av >= 0) ? $av : 0;
                            $product->availability = $av;
                            $product->save(false);
                        }
                    }
                    Yii::$app->session->setFlash('success', Yii::t('admin', 'The Order was processed'));
                    return $this->redirect(['index', 'OrderSearch[status]' => 1]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('admin', 'The requested page does not exist.'));
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionAddProductSelect() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $id = intval(AH::getValue($post, 'id', 0));
        if (!$id) {
            return ['error' => Yii::t('admin', 'You must select the category')];
        }
        $products = Product::find()
            ->innerJoin('product_content', 'product_content.product_id = products.id')
            ->where(['product_content.content_id' => $id])
            //->andWhere('products.availability > 0')
            ->all();
        if (!$products) {
            return ['error' => 'There is nothing'];
        }
        $rows = [];
        foreach ($products as $product) {
            $rows[$product->id] = $product->v('name') . ($product->availability ? '' : ' (0)');
        }
        return ['html' => $this->renderAjax('product-select', ['products' => $rows])];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionPlusProduct() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        return $this->changeProduct($post, 'increase', 1);
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionMinusProduct() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        return $this->changeProduct($post, 'increase', -1);
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionSetProduct() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $num = AH::getValue($post, 'num', 1);
        return $this->changeProduct($post, 'set', $num);
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionDelProduct() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        return $this->changeProduct($post, 'delete');
    }

    /**
     * @param $post
     * @param $type
     * @param int $num
     * @return array
     */
    protected function changeProduct($post, $type, $num = 1) {
        try {
            $num = intval($num);
            $rules = [
                [['product_id', 'order_id'], 'required'],
                [['product_id', 'order_id'], 'integer'],
                ['order_id', 'exist', 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id'],
                    'filter' => 'status = 1'],
                ['product_id', 'exist', 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id'],
                    /*'filter' => 'availability > 0'*/], // менеджер знает что делать и уже уведомлен о наличии
                // с другой стороны если забыли вставить наличие - он голосом это уточнит и не будет ждать, пока админ вставит наличие в базу
            ];
            $v = DynamicModel::validateData($post, $rules);
            $order = $this->findFullModel($post['order_id']);
            if ($v->hasErrors()) {
                return [
                    'html' => $this->renderAjax('order-products', ['model' => $order]),
                    'error' => $v->firstErrors[0]
                ];
            }
            if (!$order) {
                return [
                    'html' => $this->renderAjax('order-products', ['model' => $order]),
                    'error' => Yii::t('admin', 'This Order does not exist')
                ];
            }

            $message = '';
            $error = '';
            $changes = '';

            switch ($type) {
                case 'delete':
                    OrderProduct::deleteAll(['order_id' => $post['order_id'], 'product_id' => $post['product_id']]);
                    $message = Yii::t('admin', 'The Product was deleted from the Order');
                    $changes = [
                        'user_id' => Yii::$app->user->id,
                        'time' => time(),
                        'type' => 'delete',
                        'product_id' => $post['product_id']
                    ];
                    break;

                case 'increase':
                    $increase = false;
                    if ($order->orderProducts && count($order->orderProducts)) {
                        foreach ($order->orderProducts as $op) {
                            if ($op->product_id == $post['product_id']) {
                                if (($op->quantity + $num) < 1) {
                                    $error = Yii::t('admin', 'Delete Product if you need');
                                } else {
                                    $op->quantity += $num;
                                    $op->save(false);
                                    $increase = true;
                                }
                                break;
                            }
                        }
                    }
                    if (!$error && !$increase) {
                        if ($num < 1) {
                            $num = 1;
                        }
                        $product = Product::findOne($post['product_id']);
                        $op = new OrderProduct;
                        $op->order_id = $post['order_id'];
                        $op->product_id = $post['product_id'];
                        $op->quantity = $num;
                        $op->product_name = $product->v('name');
                        $op->price = $product->curPrice();
                        $op->save(false);
                        $increase = true;
                    }
                    if ($increase) {
                        $changes = [
                            'user_id' => Yii::$app->user->id,
                            'time' => time(),
                            'type' => 'increase',
                            'product_id' => $post['product_id'],
                            'quantity' => $num
                        ];
                    }
                    break;

                case 'set':
                    $set = false;
                    if ($order->orderProducts && count($order->orderProducts)) {
                        foreach ($order->orderProducts as $op) {
                            if ($op->product_id == $post['product_id']) {
                                if ($num < 1) {
                                    $error = Yii::t('admin', 'Delete Product if you need');
                                } else {
                                    $op->quantity = $num;
                                    $op->save(false);
                                    $set = true;
                                }
                                break;
                            }
                        }
                    }
                    if (!$error && !$set) {
                        $product = Product::findOne($post['product_id']);
                        $op = new OrderProduct;
                        $op->order_id = $post['order_id'];
                        $op->product_id = $post['product_id'];
                        $op->quantity = $num;
                        $op->product_name = $product->v('name');
                        $op->price = $product->curPrice();
                        $op->save(false);
                        $set = true;
                    }
                    if ($set) {
                        $changes = [
                            'user_id' => Yii::$app->user->id,
                            'time' => time(),
                            'type' => 'set',
                            'product_id' => $post['product_id'],
                            'quantity' => $num
                        ];
                    }
                    break;
            }

            if ($changes) {
                $data = $order->data ? Json::decode($order->data, true) : [];
                if (!isset($data['changes'])) {
                    $data['changes'] = [];
                }
                $data['changes'][] = $changes;

                $sum = 0;
                $order = $this->findFullModel($post['order_id']);
                if ($order->orderProducts && count($order->orderProducts)) {
                    foreach ($order->orderProducts as $op) {
                        $sum += $op->price * $op->quantity;
                    }
                }
                $order->sum = $sum;
                $order->data = Json::encode($data);
                $order->save(false);
            }

            $order = $this->findFullModel($post['order_id']);
            $result = ['html' => $this->renderAjax('order-products', ['model' => $order])];
            if ($message) {
                $result['message'] = $message;
            } elseif ($error) {
                $result['error'] = $error;
            }

            return $result;
        } catch (\Exception $e) {
            Yii::error('admin/OrderController: ' . $e->getMessage());
            $order = $this->findFullModel($post['order_id']);
            return [
                'error' => Yii::t('common', 'unidentified error'),
                'html' => $this->renderAjax('order-products', ['model' => $order]),
            ];
        }
    }

    /**
     * @param $id
     * @param bool $exception
     * @return ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findFullModel($id, $exception = false) {
        $order =  Order::find()
            ->with(['orderProducts.product.unit', 'orderProducts.product.picture'])
            ->where(['id' => $id])
            ->one();
        if (!$order && $exception) {
            throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
        } else {
            return $order;
        }
    }

    public function actionCounter() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'counter' => Order::find()->where(['status' => 1])->count()
        ];
    }
}
