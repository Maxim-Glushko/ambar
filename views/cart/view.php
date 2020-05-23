<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Cart;
use app\models\Product;
use app\models\Setting;
use app\models\Picture;
use app\helpers\Common as CH;

/**
 * @var $this View
 * @var $form null|string
 * @var $unProductIds null|array
 */

$form = $form ?? '';

/** @var $cart Cart | null */
$cart = Cart::extract($renew ?? false);
$isEmptyCart = !$cart || !$cart->cartProducts || !count($cart->cartProducts);
$unProductIds = Cart::extractUnProductIds();

?>

<div class="cart-view">

    <h3>
        <span class="fa fa-shopping-basket"></span>
        <?= Yii::t('common', 'Cart') ?>
    </h3>

    <?php if ($isEmptyCart) { ?>
    <p class="cart-text">
        <?= Setting::v('empty-cart-text') ?>
    </p>
    <?php } ?>

    <?php if (!$isEmptyCart) {
        $sum = 0;
        foreach ($cart->cartProducts as $cp) {
            $product = $cp->product;
            $productPrice = $product->curPrice() * $cp->quantity;
            $sum += $productPrice;
            $src = (!empty($product->picture) && !empty($product->picture->src) && file_exists(substr($product->picture->src, 1)))
                ? $product->picture->src
                : $product->content->img;
            ?>
            <div class="cart-product" id="cart-product-<?= $cp->product_id ?>" data-product_id="<?= $cp->product_id ?>">
                <a href="<?= CH::$pLng . '/' . $product->content->slug . '/' . $product->slug ?>" class="cart-product-img" target="_blank">
                    <img src="<?= Picture::getThumb($src) ?>" alt="<?= Html::encode($product->v('description')) ?>"
                         title="<?= Html::encode($product->v('description')) ?>" />
                </a>
                <button class=" btn btn-sm btn-danger cart-product-del" data-url="<?= CH::$pLng ?>/cart/del">
                    <span class="fa fa-times"></span>
                </button>
                <div class="cart-product-name">
                    <?= $product->v('name') ?>
                </div>
                <?php if ($product->unit_id && $product->measure) { ?>
                    <span class="measure"><?= (float) $product->measure ?> <?= $product->unit->v('name') ?></span>
                <?php } ?>
                <?php if (in_array($product->id, $unProductIds)) { ?>
                    <span class="last-product"><?= Yii::t('common', 'last') ?></span>
                <?php } ?>

                <div class="cart-product-buttons">
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon cart-product-price"><?= $productPrice ?> ₴</span>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default minus" data-url="<?= CH::$pLng ?>/cart/plus-minus">
                                <span class="fa fa-minus"></span>
                            </button>
                        </div>
                        <input type="text" class="form-control product-input" value="<?= $cp->quantity ?>"
                               max="<?= $product->availability ?>"
                               data-url="<?= CH::$pLng ?>/cart/change" />
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default plus" data-url="<?= CH::$pLng ?>/cart/plus-minus">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div style="clear: both; height: 3px;"></div>
            </div>
        <?php } ?>

        <?php if (Setting::v('min-order-sum') > $sum) { ?>
            <p class="min-order-sum-text" <?= $form ? 'style="display: none;"' : '' ?>>
                <?= Setting::v('min-sum-order-text') ?>
            </p>
        <?php } else { ?>
            <div class="input-group for-to-order-btn" <?= $form ? 'style="display: none;"' : '' ?>>
                <span class="input-group-addon sum"><?= $sum ?>  ₴</span>
                <div class="input-group-btn">
                    <button id="to-order" type="button" class="btn btn-warning">
                        <span class="fa fa-shopping-bag"></span>
                        <?= Yii::t('common', 'To Order') ?>
                    </button>
                </div>
            </div>
        <?php } ?>

        <div class="for-order" data-url="<?= CH::$pLng ?>/cart/order-form">
            <?= $form ?>
        </div>

    <?php } ?>

</div>
