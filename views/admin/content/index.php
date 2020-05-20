<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\ContentSearch;
use yii\data\ActiveDataProvider;
use app\models\Picture;
use kartik\select2\Select2;
use app\models\Content;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use app\helpers\Common as CH;

/**
 * @var $this View
 * @var $searchModel ContentSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
$maxSequence = Content::find()->max('sequence');
?>

<div class="content-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create Category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $columns = [
        //['class' => 'yii\grid\SerialColumn'],

        'id',
        'sequence',
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
        /*[
            'attribute' => 'parent_id',
            'filter' => Select2::widget([
                'model' => $searchModel,
                'attribute' => 'parent_id',
                'data' => Content::fullParents(),
                'options' => ['prompt' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]),
        ],*/
        [
            'attribute' => 'show',
            'content' => function($model) {
                return $model->show
                    ? '<span class="fa fa-desktop"></span>'
                    : '<span class="fa fa-minus"></span>';
            }
        ],
        [
            'attribute' => 'showmenu',
            'content' => function($model) {
                return $model->showmenu
                    ? '<span class="fa fa-plus"></span>'
                    : '<span class="fa fa-minus"></span>';
            }
        ],
        [
            'class' => ActionColumn::class,
            'headerOptions' => [
                'style' => 'min-width: 100px;',
            ],
            'template' => '<div class="actions">{update} {up} {down} {showHide} {showHideMenu}' . /* {delete} */' {view}</div>',
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
                    return ($model->id == Content::MAIN_ID)
                        ? $span
                        : ('<a href="' . $url . '" title="' . $title . '">' . $span . '</a>');
                },
                'showHideMenu' => function($url, $model) {
                    $span = '<span class="glyphicon glyphicon-star' . ($model->showmenu ? '-empty' : '') . '"></span>';
                    $title = Yii::t('admin', ($model->showmenu ? 'ToHideMenu' : 'ToShowMenu'));
                    $url = Url::to(['show-hide-menu', 'id' => $model->id, 'type' => (int)!$model->showmenu]);
                    return ($model->id == Content::MAIN_ID)
                        ? $span
                        : ('<a href="' . $url . '" title="' . $title . '">' . $span . '</a>');
                },
                'view' => function($url, $model) {
                    $title = Yii::t('admin', 'How is it showing');
                    return '<a href="' . CH::$pLng . '/' . $model->slug . '" target=_blank title="' . $title . '">'
                        . '<span class="glyphicon glyphicon-new-window"></span></a>';
                },
            ],
        ],
    ]; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>
</div>
