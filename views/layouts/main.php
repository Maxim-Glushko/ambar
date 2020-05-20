<?php

use app\models\Content;
use yii\web\View;
use app\widgets\Status;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\helpers\Common as CH;
use yii\helpers\Url;
use app\models\Picture;
use app\models\Setting;

/**
 * @var $this View
 * @var $content string
 */

AppAsset::register($this);
$topContents = Content::getTopContents();

$this->beginPage() ?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-68648896-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-68648896-3');
    </script>

</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        //'brandLabel' => Yii::$app->name, // или 'My Company'
        'brandLabel' => '<img src="/static/servimg/logo-ua-white.png" alt="" />',
        'brandUrl' => CH::$pLng ?: '/' /*. '/' .Yii::$app->homeUrl*/,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    foreach ($topContents as $k => $v) {
        $menuItems[] = [
            'label' => $v,
            'url' => CH::$pLng . '/' . $k
        ];
    }
    ?>

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => $menuItems,
    ]) ?>

    <?php
    $lngsItems = [];
    foreach (Yii::$app->params['pLngs'] as $k => $v) {
        $nLng = Yii::$app->params['nLngs'][$k];
        $lngsItems[] = ($k == Yii::$app->language)
            ? ('<span class="active">' . $nLng . '</span>')
            : ('<a href="' . $v . '/' . Yii::$app->request->pathInfo . '">' . $nLng . '</a>');
    }
    $lngsItem = implode('<span>|</span>', $lngsItems);
    ?>

    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right top-data'],
        'items' => [
            '<li class="address-n-phone">' . Setting::v('top-contacts') . '</li>',
            '<li class="lng">'
                . implode('<span>|</span>', $lngsItems)
            . '</li>',
            '<li class="login-logout">'
                . (Yii::$app->user->isGuest
                    ? ('<a href="' . CH::$pLng . '/login"><span>' . Yii::t('common', 'Login') . '</span></a>')
                    : (Html::beginForm(['/logout'], 'post')
                        . Html::submitButton('<span>' . Yii::t('common', 'Logout') . '</span>', [
                            'class' => 'btn btn-link logout'
                        ])
                        . Html::endForm()))
            . '</li>'
        ],
    ]) ?>

    <?php NavBar::end(); ?>

    <div class="container">

        <ul class="products-nav">
            <?php
            $categories = Content::extractCategories();
            $catCount = count($categories);
            foreach ($categories as $cat) {
                $thisAct = (stripos(Yii::$app->request->pathInfo, $cat->slug) === 0);
                $url = CH::$pLng . '/' . $cat->slug;
                ?>
                <li <?= $thisAct ? 'class="active"' : '' ?> style="width:<?= 100 / $catCount ?>%;">
                    <a href="<?= $url ?>">
                        <img src="<?= Picture::getThumb($cat->img) ?>" alt="<?= Html::encode($cat->v('name')) ?>" />
                        <h4><?= $cat->v('name') ?></h4>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <div style="clear:both;"></div>

        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Setting::v('main-in-breadcrumbs'),
                        'url' => CH::$pLng ?: '/'
                    ],
                    'activeItemTemplate' => '<li class="active"><span>{link}</span></li>' . "\n",
                    'links' => $this->params['breadcrumbs'] ?? [],
                ]) ?>

                <div style="clear: both;"></div>
                <?= Status::widget() ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2" id="for-cart">
                <?= $this->render('@app/views/cart/view') ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">

        <?php /*
        <ul class="bottom-links">
            <li class="bottom-logo">
                <a href="<?= CH::$pLng ?: '/' ?>">
                    <img src="/static/servimg/logo-ua.png" alt="" />
                </a>
            </li>

            <?php foreach ($topContents as $k => $v) { ?>
            <li>
                <a href="<?= CH::$pLng . '/' . $k ?>"><?= $v ?></a>
            </li>
            <?php } ?>

        </ul>
 */ ?>

        <p style="text-align: center;">
            &copy; Ambar.od.ua 2020<?= (date('Y') == 2020) ? '' : ('-' . date('Y')) ?>
        </p>
    </div>
</footer>

<div id="popup-message">
</div>

<?php $this->endBody() ?>
</body>
</html><?php $this->endPage(); ?>