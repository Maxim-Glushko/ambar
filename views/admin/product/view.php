<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'slug',
            'name_ua',
            'name_ru',
            'title_ua',
            'title_ru',
            'keywords_ua',
            'keywords_ru',
            'description_ua:ntext',
            'description_ru:ntext',
            'text_ua:ntext',
            'text_ru:ntext',
            'price',
            'discount',
            'measure',
            'unit_id',
            'vendorcode',
            'availability',
            'sequence',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
