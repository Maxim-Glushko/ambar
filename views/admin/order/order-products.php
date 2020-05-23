<?php

use app\models\Order;
use kartik\select2\Select2;
use app\models\Content;
use app\helpers\Common as CH;

/**
 * @var $model Order
 */

?>

<?php if (!$model->orderProducts || !count($model->orderProducts)) { ?>
    <p style="color: red;"><?= Yii::t('admin', 'There is no one Products in the Order') ?></p>
<?php } ?>

<div class="row">
    <?php
    $sum = 0;
    if ($model->orderProducts && count($model->orderProducts)) {
        foreach ($model->orderProducts as $op) {
            $sum += $op->product->curPrice() * $op->quantity;
            ?>

            <?= $this->render('one-product-item', [
                'product' => $op->product,
                'quantity' => $op->quantity,
                'orderStatus' => $model->status,
            ]) ?>

    <?php
        }
    }
    ?>

    <?php if ($model->status == 1) { /* иначе товары добавлять нельзя, заказ ушёл */ ?>
        <div class="col-lg-4 admin-order-product-add" data-url="<?= $op->product->id ?>">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::t('admin', 'AddProduct') ?></h3>
                </div>
                <div class="box-body gpapa-select">
                    <div class="form-group papa-select">
                        <?php /* <label class="control-label" for="content_id">
                        <?= Yii::t('admin', 'Category') ?>
                    </label>
                    */ ?>
                        <?= Select2::widget([
                            'name' => 'content_id',
                            'data' => Content::forSelect(),
                            'options' => [
                                'prompt' => '',
                                'data-url' => CH::$pLng . '/admin/order/add-product-select',
                                'id' => 'content-select',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div style="clear: both; font-size: 20px; padding-bottom: 15px;">
    <?= Yii::t('admin', 'OrderSum') ?>:
    <span class="label label-default" style="font-size:20px;"><?= $sum * 1 ?> ₴</span>
</div>