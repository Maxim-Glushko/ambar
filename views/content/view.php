<?php

use yii\web\View;
use app\models\Content;
use app\models\Product;
use yii\helpers\Url;
use app\helpers\Common as CH;
use app\widgets\LinkPager;
use yii\data\ActiveDataProvider;

/**
 * @var $this View
 * @var $content Content
 * @var $products Product[]
 * @var $page integer
 * @var $sort string
 * @var $dataProvider ActiveDataProvider
 */

$this->title = $content->v('title') ?: $content->v('name');

foreach (['keywords', 'description'] as $key) {
    if ($content->v($key)) {
        $this->registerMetaTag(['name' => $key, 'content' => $content->v($key)]);
    }
}
if ($content->slug) { // на главной крошки не нужны
    $this->params['breadcrumbs'][] = $content->v('name');
}

// TODO: на мелком экране можно сделать пятно-ссылку для перехода на корзину, которая будет не сбоку, а внизу
// на большом - наоборот - при уходе глубоко вниз, когда корзина ужен е видна, сделать пятно для перехода вверх к корзине

?>

<div style="background:#fff; border: solid 1px darkorange;clear: both; position: relative; margin-top: 15px;">
    <p style="color:darkorange; font-size: 20px;padding: 30px 20px;text-align: center;">
        <?= Yii::t('common', 'e-shop is on stage development') ?>
    </p>
</div>

<?php if ($content->id == Content::CONTACTS_ID) { ?>

    <div class="row">
        <div class="col-md-8" style="padding-top: 16px; padding-bottom: 16px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d343.67044639374143!2d30.75073347430484!3d46.44149272880905!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40c633ed0c37eccf%3A0xca1fbb91e333385c!2z0YPQuy4g0KfQtdGA0L3Rj9GF0L7QstGB0LrQvtCz0L4sIDI0LCDQntC00LXRgdGB0LAsINCe0LTQtdGB0YHQutCw0Y8g0L7QsdC70LDRgdGC0YwsIDY1MDAw!5e0!3m2!1sru!2sua!4v1589698664333!5m2!1sru!2sua"
                    width="100%" height="600" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
        </div>
        <div class="col-md-4">
            <div class="content-view contact-view">
                <div class="content-text">
                    <?= nl2br($content->v('text')) ?>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="content-view">

        <?php if ($content->v('text')) { ?>
        <div class="content-text">
            <?= nl2br($content->v('text')) ?>
        </div>
        <?php } ?>

        <?php $data = compact('content', 'page', 'sort', 'dataProvider'); ?>

        <?php if ($content->id == Content::MAIN_ID || $content->showmenu) { ?>
            <?= $this->render('products', $data) ?>
        <?php } elseif ($content->id == Content::ARTICLES_ID) { ?>
            <?= $this->render('articles', $data) ?>
        <?php } elseif ($content->id == Content::NEWS_ID) { ?>
            <?= $this->render('news', $data) ?>
        <?php } ?>

        <?php if ($dataProvider && ($content->id != Content::MAIN_ID)) { ?>
            <?= LinkPager::widget([
                'page'          => $page ?: 1,
                'maxPage'       => ceil($dataProvider->getTotalCount() / 24),
                'defaultPage'   => 1,
                'url'           => '/' . $content->slug,
                'sort'          => $sort,
                'cssClass'      => 'pagination-lg',
            ]) ?>
        <?php } ?>
    </div>

<?php } ?>