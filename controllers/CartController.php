<?php

namespace app\controllers;

use app\helpers\Common as CH;
use app\models\Cart;
use app\models\CartSearch;
use app\models\Order;
use app\models\OrderForm;
use app\models\OrderProduct;
use app\models\Product;
use app\models\Setting;
use app\models\User;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper as AH;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\db\StaleObjectException;
use Yii;

/**
 * CartController implements the CRUD actions for Cart model.
 */
class CartController extends PapaController
{

    // TODO
    // !!!!!
    // стоит, наверное, добавить рекапчу в каждое действие с корзиной

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionAdd() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = Cart::add(Yii::$app->request->post());
            if ($result === true) {
                Cart::extract(true);
                return $this->cartAnswer();
                /*$cart = Cart::extract(true);
                $productIds = Cart::extractProductIds();
                $unProductIds = Cart::extractUnProductIds();
                return [
                    'cart' => $this->renderAjax('view', ['cart' => $cart]),
                    'productIds' => $productIds,
                    'unProductIds' => $unProductIds,
                    'statusUnavailable' => Product::statuses()[Product::STATUS_UNAVAILABLE]
                ];*/
            } elseif (isset($result['error'])) {
                return $result;
            } else {
                return ['error' => Yii::t('common', 'unidentified error')];
            }
        } catch (\Exception $e) {
            Yii::error('Cart::add Exception: ' . $e->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        } catch (\Throwable $t) {
            Yii::error('Cart::add Throwable: ' . $t->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionChange() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            return $this->change(
                AH::getValue($post, 'product_id', 0),
                AH::getValue($post, 'quantity', 0)
            );
        } catch (\Exception $e) {
            Yii::error('Cart::change Exception: ' . $e->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        } catch (\Throwable $t) {
            Yii::error('Cart::add Throwable: ' . $t->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionDel() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $post = Yii::$app->request->post();
            return $this->change(
                AH::getValue($post, 'product_id', 0),
                0
            );
        } catch (\Exception $e) {
            Yii::error('Cart::del Exception: ' . $e->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        } catch (\Throwable $t) {
            Yii::error('Cart::del Throwable: ' . $t->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    /**
     * @param $product_id
     * @param $quantity
     * @return array
     * @throws StaleObjectException
     * @throws \Throwable
     */
    protected function change($product_id, $quantity) {
        $result = Cart::change($product_id, $quantity);
        if ($result === true) {
            Cart::extract(true);
            return $this->cartAnswer();
            /*$cart = Cart::extract(true);
            $productIds = Cart::extractProductIds();
            return [
                'cart' => $this->renderAjax('view', ['cart' => $cart]),
                'productIds' => $productIds
            ];*/
        } elseif (isset($result['error'])) {
            return $result;
        } else {
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionPlusMinus() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $result = Cart::plusMinus(Yii::$app->request->post());
            if ($result === true) {
                Cart::extract(true);
                return $this->cartAnswer();
                /*$cart = Cart::extract(true);
                $productIds = Cart::extractProductIds();
                return [
                    'cart' => $this->renderAjax('view', ['cart' => $cart]),
                    'productIds' => $productIds
                ];*/
            } elseif (isset($result['error'])) {
                return $result;
            } else {
                return ['error' => Yii::t('common', 'unidentified error')];
            }
        } catch (\Exception $e) {
            Yii::error('Cart::plusMinus Exception: ' . $e->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        } catch (\Throwable $t) {
            Yii::error('Cart::plusMinus Throwable: ' . $t->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionOrderForm() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            // извлечь корзину
            // если в ней товаров не хватает (значит, параллельно на другой странице их удалили),
            // возвращаем просто вид корзины

            // дальше
            // если телефон не корректен
            // возвращаем корзину и форму с ошибками

            // дальше
            // перекладываем из корзины в заказ
            // если что-то пошло не так (что?) - пишем ошибку и возвращаем извинения

            // возвращаем сообщение об успешном заказе

            $post = Yii::$app->request->post();
            $model = new OrderForm;
            if (empty($post)) {
                return [
                    'form' => $this->renderAjax('order-form', compact('model'))
                ];
            } else {
                /** @var Cart $cart */
                $cart = Cart::extract();
                $productIds = Cart::extractProductIds();

                if (Cart::isEmptyCart() || (Cart::cartSum() < intval(Setting::v('min-order-sum')))) {
                    return $this->cartAnswer(false, Yii::t('common', 'cart is empty'));
                    /*return [
                        'message' => Yii::t('common', 'cart is empty'),
                        'cart' => $this->renderAjax('view', compact('cart')),
                        'productIds' => $productIds,
                    ];*/
                }

                if ($model->load($post)) {
                    $data = [
                        'status' => 1,
                        'user_id' => Yii::$app->user->isGuest ? 0 : Yii::$app->user->id,
                        'key' => Cart::extractKey(),
                        'name' => $model->name,
                        'note' => $model->note,
                        'phone' => $model->phone,
                        'address' => $model->address,
                    ];
                    $order = new Order;
                    $sum = 0;
                    if ($order->load($data, '') && $order->save()) {
                        foreach ($cart->cartProducts as $cp) {
                            $op = new OrderProduct;
                            $op->order_id = $order->id;
                            $op->product_id = $cp->product_id;
                            $op->product_name = $cp->product->v('name');
                            $op->price = $cp->product->curPrice();
                            $op->quantity = $cp->quantity;
                            if ($op->save()) {
                                $cp->delete();
                                $sum += $op->price * $op->quantity;
                            } else {
                                Yii::error('Продукт "' . $cp->product->v('name') . '" (#' . $cp->product_id
                                    . ') не сохранился в заказ #' . $order->id);
                            }
                        }
                        $order->sum = $sum;
                        $order->save(false);
                        Cart::extract(true);
                        //$productIds = Cart::extractProductIds();
                        return $this->cartAnswer(false, Setting::v('after-order'));
                        /*return [
                            'cart' => $this->renderAjax('view', compact('cart')),
                            'productIds' => $productIds,
                            'message' => Setting::v('after-order'),
                        ];*/
                    } else {
                        Yii::error('Ошибка при заказе. Ключ: ' . $order->key);
                        return $this->cartAnswer(Yii::t('common', 'unidentified error'));
                        /*return [
                            'cart' => $this->renderAjax('view', compact('cart')),
                            'productIds' => $productIds,
                            'error' => Yii::t('common', 'unidentified error')
                        ];*/
                    }
                } else {
                    return $this->cartAnswer(
                        $model->firstErrors[0],
                        false,
                        $this->renderAjax('order-form', compact('model'))
                    );
                    /*return [
                        'cart' => $this->renderAjax('view', compact('cart')),
                        'productIds' => $productIds,
                        'error' => $model->firstErrors[0],
                        'form' => $this->renderAjax('order-form', compact('model'))
                    ];*/
                }
            }
        } catch (\Exception $e) {
            Yii::error('OrderForm Exception: ' . $e->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        } catch (\Throwable $t) {
            Yii::error('OrderForm Throwable: ' . $t->getMessage());
            return ['error' => Yii::t('common', 'unidentified error')];
        }
    }

    public function cartAnswer($error = false, $message = false, $form = false) {
        $cart = Cart::extract();
        $productIds = Cart::extractProductIds();
        $unProductIds = Cart::extractUnProductIds();
        $result = [
            'cart' => $this->renderAjax('view', compact('cart')),
            'productIds' => $productIds,
            'unProductIds' => $unProductIds,
            'statusUnavailable' => Product::statuses()[Product::STATUS_UNAVAILABLE]
        ];
        if ($error) {
            $result['error'] = $error;
        }
        if ($message) {
            $result['message'] = $message;
        }
        if ($form) {
            $result['form'] = $form;
        }
        return $result;
    }
}
