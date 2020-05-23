<?php

use app\models\Cart;
use app\models\Picture;
use app\models\Content;
use yii\helpers\Html;
use app\helpers\Common as CH;
use yii\helpers\ArrayHelper as AH;
use yii\data\ActiveDataProvider;
use app\models\ProductSearch;

/**
 * @var $content Content
 * @var $page integer
 * @var $sort string
 * @var $dataProvider ActiveDataProvider
 */

$products = $dataProvider->models;

?>

<?php if (count($products)) { ?>

    <?php if ($content->id != Content::MAIN_ID) { ?>

        <div class="product-sort">
            <ul>
                <li><?= Yii::t('common', 'Sort by') ?>:</li>
            <?php
            foreach (ProductSearch::sortNames() as $k => $v) {
                $sort2 = ($sort && ($sort != ProductSearch::$sorts[0])) ? $sort : ProductSearch::$sorts[0];
                $url = CH::$pLng . '/' . $content->slug;
                if ($sort2 == $k) {
                    $url .= '/sort:-' . $k;
                    $arrow = 'fa fa-long-arrow-down';
                } else {
                    $arrow = 'fa fa-long-arrow-up';
                    $url .= '/sort:' . $k;
                }
                ?>
                <li>
                    <a href="<?= $url ?>">
                        <i class="<?= $arrow ?>"></i>
                        <span><?= $v ?></span>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </div>

    <?php } ?>

    <div class="row product-list">
        <?php foreach ($products as $product) {
            $productQuantity = Cart::hasProduct($product->id);
            $src = (!empty($product->picture) && !empty($product->picture->src) && file_exists(substr($product->picture->src, 1)))
                ? $product->picture->src
                : $product->content->img;
            $isDiscount =  (!empty($product->discount * 1) && ($product->discount < $product->price));
            $withMeasure = $product->unit_id && $product->measure;
            $url =  CH::$pLng . '/' . ($content->slug ?: $product->content->slug) . '/' . $product->slug
            ?>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <div class="product-item one-product" data-product_id="<?= $product->id ?>" data-url="<?= CH::$pLng ?>/cart/add">
                    <a href="<?= $url ?>" class="product-img">
                        <img src="<?= Picture::getThumb($src) ?>"
                             alt="<?= Html::encode($product->v('description')) ?>"
                             title="<?= Html::encode($product->v('description')) ?>" />
                    </a>
                    <h4>
                        <a href="<?= $url ?>">
                            <?= $product->v('name') ?>
                        </a>
                    </h4>

                    <p><?= $product->v('description') ?></p>

                    <div class="prices">
                        <?php if ($withMeasure) { ?>
                            <span class="measure"><?= $product->measure * 1 ?> <?= $product->unit->v('name') ?></span>
                        <?php } ?>
                        <span class="status"><?= $product->lngStatus() ?></span>

                        <?php if ($isDiscount) { ?>
                            <span class="discount"><?= intval($product->discount) ?> ₴</span>
                            <span class="price cross-out"><?= intval($product->price) ?></span>
                        <?php } else { ?>
                            <span class="price"><?= intval($product->price) ?> ₴</span>
                        <?php } ?>

                    </div>

                    <div class="product-buttons" <?= ($productQuantity || ($product->availability < 1)) ? 'style="display:none;"' : '' ?>>
                        <div class="input-group input-group-lg">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default minus">
                                    <span class="fa fa-minus"></span>
                                </button>
                            </div>
                            <input type="text" class="form-control product-input" value="1" max="<?= $product->availability ?>" />
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
                            <?= Yii::t('common', 'cart already has it') ?>
                        </span>
                    </div>

                    <div class="product-unavailable" <?= ($product->availability < 1) ? '' : 'style="display:none;"' ?>>
                    </div>

                    <div style="clear:both;"></div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p style="padding: 10px; background: #fff;">
        <?= Yii::t('common', 'category is filling now') ?>
    </p>
<?php } ?>
