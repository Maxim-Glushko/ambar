<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Order;
use app\models\Product;
use yii\helpers\ArrayHelper as AH;
use app\helpers\Common as CH;
use yii\helpers\Url;

/**
 * @var $this View
 * @var $model Order
 */

$this->title = Yii::t('admin', 'Update Order: {name}', [
    'name' => '#' . $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');


$username = $model->name ?: ($model->user->username ?? 'Anonim');
$email = $model->user->email ?? '';
$address = $model->address ?: ($model->user->address ?? '');
?>

<div class="order-update">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <div class="row">
        <div class="col-md-5 col-lg-4">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="<?= Order::customerImg() ?>" alt="">

                    <h3 class="profile-username text-center">
                        <?= $username ?>
                    </h3>

                    <?php if ($model->phone) { ?>
                        <p class="text-muted text-center">
                            <a href="tel:+38<?= $model->phone ?>">+38<?= $model->phone ?></a>
                        </p>
                    <?php } ?>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b><?= Yii::t('admin', 'Key') ?></b>
                            <a class="pull-right" href="<?= Url::to(['index?OrderSearch[key]=' . $model->key]) ?>" target="_blank">
                                <?= $model->key ?>
                            </a>
                        </li>

                        <?php if ($email) { ?>
                        <li class="list-group-item">
                            <b>Email</b>
                            <a class="pull-right" href="mailto:<?= $email ?>"><?= $email ?></a>
                        </li>
                        <?php } ?>

                        <li class="list-group-item">
                            <b><?= Yii::t('admin', 'OrderCount') ?></b>
                            <a class="pull-right"><?= $model->orderCounter() ?></a>
                        </li>

                        <li class="list-group-item">
                            <b><?= Yii::t('admin', 'OrdersSum') ?></b>
                            <a class="pull-right"><?= $model->ordersSum() ?> ₴</a>
                        </li>
                    </ul>

                    <?php if ($model->address) { ?>
                        <strong>
                            <i class="fa fa-map-marker margin-r-5"></i>
                            <?= Yii::t('admin', 'Address') ?>
                        </strong>
                        <p><?= Html::encode($address) ?></p>
                    <?php } ?>

                    <?php if ($model->note) { ?>
                        <strong>
                            <i class="fa fa-file-text-o margin-r-5"></i>
                            <?= Yii::t('admin', 'Note') ?>
                        </strong>
                        <p><?= Html::encode($model->note) ?></p>
                    <?php } ?>

                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <div class="col-md-7 col-lg-8">
            <div class="box box-primary">
                <div class="box-body box-profile">

                    <div class="admin-order-products" data-id="<?= $model->id ?>">
                        <?= $this->render('order-products', [
                            'model' => $model,
                        ]) ?>
                    </div>

                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>

                </div>
            </div>
        </div>
    </div>

</div>
