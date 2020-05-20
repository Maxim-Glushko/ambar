<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\User;

/**
 * @var $this View
 * @var $model User
 */

$this->title = Yii::t('admin', 'Update User: {name}', [
    'name' => $model->username ?: $model->email,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username ?: $model->email, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-update">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
