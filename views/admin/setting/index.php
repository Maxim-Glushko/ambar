<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\View;
use app\models\admin\SettingSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use kartik\grid\ActionColumn;

/**
 * @var $this View
 * @var $searchModel SettingSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php /*
    <p>
        <?= Html::a(Yii::t('admin', 'Create Setting'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    */ ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'slug',
            'description:ntext',
            'value_ua:ntext',
            'value_ru:ntext',
            [
                'class' => ActionColumn::class,
                'template' => '<div class="actions">{update}</div>',
            ],
        ],
    ]); ?>
</div>
