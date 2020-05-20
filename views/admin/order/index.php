<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\OrderSearch;
use yii\data\ActiveDataProvider;
use kartik\grid\ActionColumn;
use kartik\grid\SerialColumn;
use app\helpers\Common as CH;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper as AH;
use app\models\Order;
use app\models\Picture;

/**
 * @var $this View
 * @var $searchModel OrderSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Orders');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="order-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => SerialColumn::class],

            [
                'attribute' => 'id',
                'headerOptions' => [
                    'style' => 'width: 50px;',
                ],
            ],
            [
                'attribute' => 'created_at',
                'content' => function($model) {
                    return CH::d2($model->created_at);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'data' => Order::statuses(),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'content' => function($model) {
                    return AH::getValue(Order::statuses(), $model->status, '');
                },
                'headerOptions' => [
                    'style' => 'min-width: 130px;',
                ],
                'contentOptions' => [
                    'style' => 'text-align: center;',
                ],
            ],
            [
                'attribute' => 'key',
                'headerOptions' => [
                    'style' => 'width: 70px;',
                ],
            ],
            [
                'attribute' => 'user_id',
                'content' => function($model) {
                    return ($model->user_id && $model->user)
                        ? ('<a href="' . Url::to(['/admin/user/view', 'id' => $model->user_id]) . '" target="_blank">'
                            . AH::getValue(Order::usersForSelect(), $model->user_id, '-') . '</a>')
                        : '-';
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'user_id',
                    'data' => Order::usersForSelect(),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('admin', 'Coords'),
                'content' => function($model) {
                    $html = '';
                    if ($model->name) {
                        $html .= Yii::t('admin', 'Username') . ': ' . Html::encode($model->name) . '<br />';
                    }
                    $html .= '<a href="tel:+380' . $model->phone . '">+380' . $model->phone . '</a><br />';
                    if ($model->address) {
                        $html .= Yii::t('admin', 'Address') . ': ' . Html::encode($model->address) . '<br />';
                    }
                    return $html;
                }
            ],
            [
                'attribute' => 'product_id',
                'content' => function($model) {
                    $htmls = [];
                    if ($model->orderProducts && count($model->orderProducts)) {
                        foreach ($model->orderProducts as $op) {
                            if ($op->product) {
                                $html = '<div style="clear:both;">';
                                $p = $op->product;
                                $src = Picture::getThumb($p->picture->src ?: $p->content->img);
                                if ($src) {
                                    $html .= '<img src="' . $src .'" style="width: 50px;float: left;" />';
                                }
                                $html .= '<div style="margin-left: 55px;">';
                                $html .= '<a href="' . CH::$pLng . '/' . $p->content->slug . '/' . $p->slug . '"'
                                    . ' target="_blank">' . $p->v('name') . '</a>';
                                if ($p->measure && $p->unit) {
                                    $html .= ' (' . $p->measure * 1 . ' ' . $p->unit->v('name') . ')';
                                }
                                if (!$p->availability) {
                                    $html .= '<br /><span style="color: red;">[' . Yii::t('admin', 'Availability') . ': 0]</span>';
                                }
                                $html .= '<br />' . $p->curPrice() . ' ₴ x ' . $op->quantity . ' = ' . ($p->curPrice() * $op->quantity) . ' ₴';
                                $html .= "</div></div>";
                                $htmls[] = $html;
                            }
                        }
                    }
                    return implode('<div style="margin-bottom:7px; clear: both;"></div>', $htmls);
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'product_id',
                    'data' => Order::productsForSelect(),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'headerOptions' => [
                    'style' => 'min-width: 70px;',
                ],
            ],
            [
                'attribute' => 'sum',
                'content' => function($model) {
                    return $model->sum ? (($model->sum * 1) . ' ₴') : '0';
                },
                'headerOptions' => [
                    'style' => 'width: 80px;',
                ],
                'contentOptions' => [
                    'style' => 'text-align: center;',
                ],
            ],
            [
                'attribute' => 'note',
                'content' => function($model) {
                    return $model->note ? Html::encode($model->note) : '';
                },

            ],
            [
                'attribute' => 'comment',
                'content' => function($model) {
                    return $model->comment ? Html::encode($model->comment) : '';
                },

            ],
            //'data:ntext',
            [
                'class' => ActionColumn::class,
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return '<a href="' . Url::to(['update', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Processing') . '">'
                            . '<span class="fa fa-edit" style="font-size: 26px;"></span></a>';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
