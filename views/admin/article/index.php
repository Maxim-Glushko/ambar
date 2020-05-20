<?php

use app\models\Article;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\web\View;
use app\models\admin\ArticleSearch;
use yii\data\ActiveDataProvider;
use kartik\grid\ActionColumn;
use app\models\Picture;
use yii\helpers\Url;
use app\helpers\Common as CH;
use app\models\Content;
use yii\helpers\ArrayHelper as AH;

/**
 * @var $this View
 * @var $searchModel ArticleSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Articles');
$this->params['breadcrumbs'][] = $this->title;

$articlesSlug = AH::getValue(Content::findOne(Content::ARTICLES_ID), 'slug');
$maxSequence = Article::find()->max('sequence');
?>
<div class="article-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create Article'), ['create'], ['class' => 'btn btn-success']) ?>
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
                },
                'headerOptions' => [
                    'style' => 'width: 50px;',
                ],
            ],
            [
                'attribute' => 'published_at',
                'content' => function($model) {
                    return CH::d2($model->published_at ?: $model->created_at);
                }
            ],
            'sequence',
            [
                'class' => ActionColumn::class,
                'headerOptions' => [
                    'style' => 'min-width: 120px;',
                ],
                'template' => '<div class="actions">{update} {view}<br /> {showHide} {up} {down}</div>',
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
                    'down' => function ($url, $model) use ($maxSequence) {
                        $span = '<span class="glyphicon glyphicon-arrow-down"></span>';
                        return ($model->sequence < $maxSequence)
                            ? ('<a href="' . Url::to(['down', 'id' => $model->id]) . '" title="' . Yii::t('admin', 'Down') . '">'
                                . $span . '</a>')
                            : $span;
                    },
                    'showHide' => function($url, $model) {
                        $span = '<span class="glyphicon glyphicon-eye-' . ($model->show ? 'close' : 'open') . '"></span>';
                        $title = Yii::t('admin', ($model->show ? 'ToHide' : 'ToShow'));
                        $url = Url::to(['show-hide', 'id' => $model->id, 'type' => (int)!$model->show]);
                        return '<a href="' . $url . '" title="' . $title . '">' . $span . '</a>';
                    },
                    'view' => function($url, $model) use ($articlesSlug) {
                        $title = Yii::t('admin', 'How is it showing');
                        return '<a href="' . CH::$pLng . '/' . $articlesSlug . '/' . $model->slug . '" target=_blank title="' . $title . '">'
                            . '<span class="glyphicon glyphicon-new-window"></span></a>';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
