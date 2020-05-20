<?php

use app\models\Picture;
use app\helpers\Common as CH;
use app\models\Product;
use yii\helpers\Url;

/**
 * @var $product Product
 * @var $quantity integer
 * @var $orderStatus integer
 */
?>

<div class="col-lg-4 admin-order-product" data-id="<?= $product->id ?>">
    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $product->v('name') ?></h3>

            <?php if ($orderStatus == 1) { ?>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool product-delete" <?php /* data-widget="remove" */ ?>
                    data-id="<?= $product->id ?>" data-url="<?= Url::to(['/admin/order/del-product']) ?>">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <?php } ?>

        </div>
        <div class="box-body">
            <img src="<?= Picture::getThumb($product->picture->src ?: $product->content->img) ?>" alt=""
                 style="width:100px; height: 100px; float: left;" />
            <div style="margin-left:115px; position: relative;">
                <p style="height: 43px;">
                    <?php if ($product->measure && $product->unit_id) { ?>
                        <?= $product->measure * 1 ?> <?= $product->unit->v('name') ?>
                    <?php } ?>
                </p>

                <?php if ($orderStatus == 1) { ?>
                <div class="admin-order-product-buttons">
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon cart-product-price">
                            <?= $product->curPrice() ?> ₴
                        </span>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default minus" data-id="<?= $product->id ?>"
                                    data-url="<?= Url::to(['/admin/order/minus-product']) ?>">
                                <span class="fa fa-minus"></span>
                            </button>
                        </div>
                        <input type="text" class="form-control product-input" value="<?= $quantity ?>" data-id="<?= $product->id ?>"
                            data-url="<?= Url::to(['/admin/order/set-product']) ?>" />
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default plus"  data-id="<?= $product->id ?>"
                                    data-url="<?= Url::to(['/admin/order/plus-product']) ?>">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php }  else { ?>
                    <?= $product->curPrice() ?> ₴ x <?= $quantity ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
