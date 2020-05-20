<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\Common as CH;
use yii\web\View;
use app\models\User;

/**
 * @var $this View
 * @var $model User
 */

$this->title = $model->username ?: $model->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <?php if (CH::isAdmin()) { ?>
        <p>
            <?= Html::a(Yii::t('admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php /*= Html::a(Yii::t('admin', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])*/ ?>
        </p>
    <?php } ?>

    <div class="box box-primary">
        <div class="box-body">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'email',
                    [
                        'attribute' => 'phone',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->phone ? ('+380' . $model->phone) : '';
                        }
                    ],
                    'address:ntext',
                    'role',
                    [
                        'label' => Yii::t('admin', 'OrderCount'),
                        'format' => 'raw',
                        'value' => function($model) {
                            $orders = $model->orders;
                            return ($orders && count($orders))
                                ? ('<a href="' . CH::$pLng . '/admin/order?OrderSearch[user_id]=' . $model->id . '">' . count($orders) . '</a>')
                                : '';
                        }
                    ],
                    'comment:ntext',
                    [
                        'attribute' => 'registered_at',
                        'format' => 'raw',
                        'value' => function($model) {
                            return CH::d1($model->registered_at);
                        },
                    ],
                    [
                        'attribute' => 'confirmed_at',
                        'format' => 'raw',
                        'value' => function($model) {
                            return CH::d1($model->confirmed_at);
                        },
                    ],
                    [
                        'attribute' => 'last_visit',
                        'format' => 'raw',
                        'value' => function($model) {
                            return CH::d1($model->last_visit);
                        },
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
