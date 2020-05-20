<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\View;
use app\models\Product;
use app\models\Content;
use app\models\Picture;
use app\models\Cart;
use app\helpers\Common as CH;
use app\models\Setting;

/**
 * @var $this View
 * @var $product Product
 */

$this->title = $product->v('title') ?: $product->v('name');

foreach (['keywords', 'description'] as $key) {
    if ($product->v($key)) {
        $this->registerMetaTag(['name' => $key, 'content' => $product->v($key)]);
    }
}

$this->params['breadcrumbs'][] = [
    'label' => $product->content->v('name'),
    'url' => [CH::$pLng . '/' . $product->content->slug]
];
$this->params['breadcrumbs'][] = $product->v('name');

$productQuantity = Cart::hasProduct($product->id);
$isDiscount =  (($product->discount *1) && ($product->discount < $product->price));
$withMeasure = $product->unit_id && $product->measure;

?>
<div class="product-view one-product" data-product_id="<?= $product->id ?>" data-url="<?= CH::$pLng ?>/cart/add">

    <div class="row">
        <div class="col-sm-6 col-md-7 col-lg-4 pictures">
            <div class="big-picture">
                <img src="<?= Picture::getThumb(!empty($product->pictures) ? $product->pictures[0]->src : $product->content->img) ?>"
                    alt="<?= !empty($product->pictures) ? Html::encode($product->pictures[0]->v('description')) : '' ?>" />
            </div>
            <?php if ($product->pictures) {
                $i = 0; $j = 0;
                ?>
                <div class="small-pictures">
                    <?php foreach ($product->pictures as $pi) { ?>
                        <img src="<?= Picture::getThumb($pi->src) ?>" <?= !$j++ ? 'class="active"' : '' ?>
                                alt="<?= Html::encode($pi->v('description')) ?>" />
                        <?php if (++$i == 3) { $i = 0; ?>
                            <div style="clear: both;"></div>
                        <?php } ?>
                    <?php } ?>
                    <div style="clear: both; height: 15px;"></div>
                </div>
            <?php } ?>
        </div>
        <div class="col-sm-6 col-md-5 col-lg-8" style="background: #fff;">
            <div class="product-text">
                <?php if ($product->v('text')) { ?>
                    <?= nl2br($product->v('text')) ?>
                <?php } else { ?>
                    <h1><?= Html::encode($product->v('name')) ?></h1>
                    <?php /* <p><?= Yii::t('common', 'category is filling now') ?></p> */ ?>
                <?php } ?>
            </div>

            <div style="position:relative; width: 49%; float:left;">
            <div class="prices">
                <?php if ($withMeasure) { ?>
                    <span class="measure"><?= (float) $product->measure ?> <?= $product->unit->v('name') ?></span>
                <?php } ?>
                <span class="status"><?= $product->lngStatus() ?></span>
                <?php if ($isDiscount) { ?>
                    <span class="discount"><?= intval($product->discount) ?> ₴</span>
                    <span class="price cross-out"><?= intval($product->price) ?></span>
                <?php } else { ?>
                    <span class="price"><?= intval($product->price) ?> ₴</span>
                <?php } ?>
            </div>
            </div>

            <div style="position:relative; width: 49%; float: right;">
            <div class="product-buttons" <?= $productQuantity ? 'style="display:none;"' : '' ?>>
                <div class="input-group input-group-lg">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default minus">
                            <span class="fa fa-minus"></span>
                        </button>
                    </div>
                    <input type="text" class="form-control product-input" value="1" />
                    <div class="input-group-btn two">
                        <button type="button" class="btn btn-default plus">
                            <span class="fa fa-plus"></span>
                        </button>
                        <button type="button" class="btn btn-default add">
                            <span class="fa fa-shopping-basket"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="product-already" <?= $productQuantity ? '' : 'style="display:none;"' ?>>
                <span class="already">
                    <?= Yii::t('app', 'вже у кошику') ?>
                </span>
            </div>
            </div>

            <div style="clear:both;"></div>

            <div class="product-infa">
                <?= Setting::v('product-infa') ?>
            </div>

        </div>
    </div>

</div>
