<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\select2\Select2;
use app\models\User;
use app\helpers\Common as CH;
use kartik\grid\EditableColumn;
use yii\web\View;
use app\models\admin\UserSearch;
use yii\data\ActiveDataProvider;

/**
 * @var $this View
 * @var $searchModel UserSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t('admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php /*
    <p>
        <?= Html::a(Yii::t('admin', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    */ ?>

    <?php $columns = [
        //['class' => 'kartik\grid\SerialColumn'],
        'id',
        'username',
        'email',
        'phone',
        'address:ntext',
        [
            'attribute' => 'role',
            'filter' => Select2::widget([
                'model' => $searchModel,
                'attribute' => 'role',
                'data' => User::rolesForSelect(),
                'options' => ['prompt' => ''],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]),
            'headerOptions' => [
                'style' => 'min-width: 100px;',
            ],
        ],
        [
            'attribute' => 'orderCount',
            //'header' => Yii::t('admin', 'OrderCount'),
            'content' => function($model) {
                return $model->orderCount
                    ? (' <a href="' . CH::$pLng . '/admin/order?OrderSearch[user_id]=' . $model->id . '">' . $model->orderCount . '</a>')
                    : '0';
            }
        ],
        [
            'attribute' => 'registered_at',
            'content' => function($model) {
                return $model->registered_at ? date('Y-m-d H:m', $model->registered_at) : '';
            }
        ],
        [
            'attribute' => 'confirmed_at',
            'content' => function($model) {
                return $model->confirmed_at ? date('Y-m-d H:m', $model->confirmed_at) : '';
            }
        ],
        [
            'attribute' => 'last_visit',
            'content' => function($model) {
                return $model->last_visit ? date('Y-m-d H:m', $model->last_visit) : '';
            }
        ],
        //'ban_expiration',
        //'auth_key',
        //'password_hash',
        //'email_confirm_token:email',
        //'password_reset_token',

        [
            'class' => 'kartik\grid\ActionColumn',
            'headerOptions' => [
                'style' => 'min-width: 80px;',
            ],
            'template' => '<div class="actions">{view} {update}</div>',
        ],
    ]; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>
</div>
