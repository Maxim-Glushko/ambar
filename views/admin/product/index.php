<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\ProductSearch;
use app\models\Picture;
use yii\data\ActiveDataProvider;
use kartik\grid\ActionColumn;
use kartik\select2\Select2;
use app\models\Product;
use yii\helpers\ArrayHelper as AH;
use app\models\Content;
use app\models\Unit;
use yii\helpers\Url;
use app\helpers\Common as CH;

/**
 * @var $this View
 * @var $searchModel ProductSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Products');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="product-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create Product'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'headerOptions' => [
                    'style' => 'width: 30px;',
                ],
            ],
            [
                'attribute' => 'vendorcode',
                'headerOptions' => [
                    'style' => 'width: 80px;',
                ],
                'content' => function($model) {
                    return $model->vendorcode ?: '';
                }
            ],
            [
                'label' => 'Picture',
                'content' => function($model) {
                    $src = $model->picture ? Picture::getThumb($model->picture->src) : '';
                    return $src ? ('<img src="' . $src . '" style="height:50px; margin: 0 10px 0 0;" />') : '';
                }
            ],
            'slug',
            'name_ua',
            'name_ru',
            [
                'attribute' => 'content_id',
                'label' => Yii::t('admin','Categories'),
                'content' => function($model) {
                    $parts = [];
                    if ($model->contents && count($model->contents)) {
                        foreach ($model->contents as $content) {
                            $parts[] = ($model->content->id == $content->id)
                                ? ('<u title="' . Yii::t('admin' ,'Main category') . '">' . $content->v('name') . '</u>')
                                : $content->v('name');
                        }
                    }
                    return implode(', ', $parts);
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'content_id',
                    'data' => AH::map(Content::extractCategories(), 'id', 'name_' . (Yii::$app->language == 'ru-RU' ? 'ru' : 'ua')),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'headerOptions' => [
                    'style' => 'width: 120px;',
                ],
            ],
            [
                'attribute' => 'price',
                'headerOptions' => [
                    'style' => 'width: 80px;',
                ],
                'content' => function($model) {
                    return $model->price ?: '';
                }
            ],
            [
                'attribute' => 'discount',
                'headerOptions' => [
                    'style' => 'width: 80px;',
                ],
                'content' => function($model) {
                    return $model->discount ?: '';
                }
            ],
            [
                'attribute' => 'measure',
                'headerOptions' => [
                    'style' => 'width: 80px;',
                ],
                'content' => function($model) {
                    return $model->measure ?: '';
                }
            ],
            [
                'attribute' => 'unit_id',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'unit_id',
                    'data' => Unit::forSelect(),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'content' => function($model) {
                    return AH::getValue(Unit::forSelect(), $model->unit_id, '');
                },
                'headerOptions' => [
                    'style' => 'width: 120px;',
                ],
            ],
            'availability',
            [
                'label' => Yii::t('admin', 'Rec'),
                'attribute' => 'recommended',
                'content' => function($model) {
                    return $model->recommended ? '<span class="fa fa-trophy"></span>' : '';
                },
            ],
            [
                'attribute' => 'status',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'data' => Product::statuses(),
                    'options' => ['prompt' => ''],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'content' => function($model) {
                    return AH::getValue(Product::statuses(), $model->status, '');
                }
            ],
            //'created_at',
            //'updated_at',
            [
                'attribute' => 'sequence',
                'headerOptions' => [
                    'style' => 'width: 50px;',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '<div class="actions" style="width:50px;">{update} {view}<br /> {up} {down}</div>',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return '<a href="' . Url::to(['update', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Update') . '">'
                            . '<span class="glyphicon glyphicon-pencil"></span></a>';
                    },
                    'up' => function ($url, $model) {
                        $span = '<span class="glyphicon glyphicon-arrow-up"></span>';
                        return ($model->sequence > 1)
                            ? ('<a href="' . Url::to(['up', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Up') . '">'
                                . $span . '</a>')
                            : $span;
                    },
                    'down' => function ($url, $model) {
                        return '<a href="' . Url::to(['down', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Down') . '">'
                            . '<span class="glyphicon glyphicon-arrow-down"></span></a>';
                    },
                    'view' => function($url, $model) {
                        $title = Yii::t('admin', 'How is it showing');
                        return '<a href="' . CH::$pLng . '/' . $model->content->slug . '/' . $model->slug . '" target=_blank title="' . $title . '">'
                            . '<span class="glyphicon glyphicon-new-window"></span></a>';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
