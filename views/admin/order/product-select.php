<?php

use kartik\select2\Select2;
use app\helpers\Common as CH;

/**
 * @var $products array
 */

?>

<div class="form-group papa-select">
    <?php /* <label class="control-label" for="product_id">
        <?= Yii::t('admin', 'Products') ?>
    </label>
    */ ?>
    <?= Select2::widget([
        'name' => 'product_id',
        'data' => $products,
        'options' => [
            'prompt' => '',
            'data-url' => CH::$pLng . '/admin/order/plus-product',
            'id' => 'product-select',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
</div>

<?php /*
<div class="form-group btn-papa">
    <div class="btn btn-primary" disabled="disabled" style="width: 100%;">
        <span class="fa fa-cart-arrow-down"></span>
        <?= Yii::t('admin', 'Add') ?>
    </div>
</div>
 */ ?>

