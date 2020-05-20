<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\NewsSearch;
use yii\data\ActiveDataProvider;
use kartik\grid\SerialColumn;
use app\models\Picture;
use kartik\grid\ActionColumn;
use yii\helpers\Url;
use app\helpers\Common as CH;
use yii\helpers\ArrayHelper as AH;
use app\models\Content;

/**
 * @var $this View
 * @var $searchModel NewsSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'News');
$this->params['breadcrumbs'][] = $this->title;

$newsSlug = AH::getValue(Content::findOne(Content::NEWS_ID), 'slug');
?>
<div class="news-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => SerialColumn::class],

            //'id',
            [
                'attribute' => 'img',
                'content' => function($model) {
                    $img = $model->img;
                    return ($img && file_exists(substr($img, 1)))
                        ? ('<img src="' . Picture::getThumb($img) . '" style="width: 100px;" />')
                        : '';
                }
            ],
            'slug',
            'name_ua',
            'name_ru',
            [
                'attribute' => 'show',
                'content' => function($model) {
                    return $model->show
                        ? '<span class="fa fa-desktop"></span>'
                        : '<span class="fa fa-minus"></span>';
                }
            ],
            [
                'attribute' => 'published_at',
                'content' => function($model) {
                    return CH::d2($model->published_at ?: $model->created_at);
                }
            ],
            [
                'class' => ActionColumn::class,
                'headerOptions' => [
                    'style' => 'min-width: 120px;',
                ],
                'template' => '<div class="actions">{update} {showHide} {view}</div>',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return '<a href="' . Url::to(['update', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Update') . '">'
                            . '<span class="glyphicon glyphicon-pencil"></span></a>';
                    },
                    'showHide' => function($url, $model) {
                        $span = '<span class="glyphicon glyphicon-eye-' . ($model->show ? 'close' : 'open') . '"></span>';
                        $title = Yii::t('admin', ($model->show ? 'ToHide' : 'ToShow'));
                        $url = Url::to(['show-hide', 'id' => $model->id, 'type' => (int)!$model->show]);
                        return '<a href="' . $url . '" title="' . $title . '">' . $span . '</a>';
                    },
                    'view' => function($url, $model) use ($newsSlug) {
                        $title = Yii::t('admin', 'How is it showing');
                        return '<a href="' . CH::$pLng . '/' . $newsSlug . '/' . $model->slug . '" target=_blank title="' . $title . '">'
                            . '<span class="glyphicon glyphicon-new-window"></span></a>';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
