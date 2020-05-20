<?php

use yii\helpers\Html;
use yii\web\View;
use app\models\Content;

/**
 * @var $this View
 * @var $model Content
 */

$this->title = Yii::t('admin', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-create">
    <div class="box box-primary">
        <div class="box-body">
            <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
