<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\UnitSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use app\models\Unit;
use kartik\grid\ActionColumn;

/**
 * @var $this View
 * @var $searchModel UnitSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Units');
$this->params['breadcrumbs'][] = $this->title;
$maxSequence = Unit::find()->max('sequence');
?>

<div class="unit-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('admin', 'Create Unit'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'sequence',
            'name_ua',
            'name_ru',
            //'created_at',
            //'updated_at',

            [
                'class' => ActionColumn::class,
                'template' => '<div class="actions">{update} {up} {down}</div>',
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
                ],
            ],
        ],
    ]); ?>
</div>
