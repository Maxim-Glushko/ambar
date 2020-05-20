<?php

use yii\helpers\Html;
use app\helpers\Common as CH;

$contrId = Yii::$app->controller->id;

?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Html::encode(Yii::$app->user->identity->username ?: Yii::$app->user->identity->email) ?></p>
                <span><?= Yii::$app->user->identity->role ?></span>
                <?php /* <a href="#"><i class="fa fa-circle text-success"></i> Online</a> */ ?>
            </div>
        </div>

        <?php /*
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        */ ?>

        <?= dmstr\widgets\Menu::widget([
            'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
            'items' => [
                ['label' => 'Admin Panel', 'options' => ['class' => 'header']],
                //['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                //['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                //['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],

                [
                    'label' => Yii::t('admin', 'Orders'),
                    'icon'  => 'shopping-bag',
                    'url'   => CH::$pLng . '/admin/order',
                    'visible' => CH::isAdminOrManager(),
                    'options' => ['class' => ($contrId == 'admin/order') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Categories'),
                    'icon'  => 'newspaper-o',
                    'url'   => CH::$pLng . '/admin/content',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/content') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Products'),
                    'icon'  => 'cutlery',
                    'url'   => CH::$pLng . '/admin/product',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/product') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Articles'),
                    'icon'  => 'bookmark',
                    'url'   => CH::$pLng . '/admin/article',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/article') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'News'),
                    'icon'  => 'bookmark-o',
                    'url'   => CH::$pLng . '/admin/news',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/news') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Settings'),
                    'icon'  => 'language',
                    'url'   => CH::$pLng . '/admin/setting',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/setting') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Units'),
                    'icon'  => 'tags',
                    'url'   => CH::$pLng . '/admin/unit',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/unit') ? 'active' : ''],
                ],
                [
                    'label' => Yii::t('admin', 'Users'),
                    'icon'  => 'users',
                    'url'   => CH::$pLng . '/admin/user',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/user') ? 'active' : ''],
                ],
                /*[
                    'label' => Yii::t('admin', 'Carts'),
                    'icon'  => 'shopping-cart',
                    'url'   => CH::$pLng . '/admin/cart',
                    'visible' => CH::isAdmin(),
                    'options' => ['class' => ($contrId == 'admin/cart') ? 'active' : ''],
                ],*/


                /*[
                    'label' => 'Some tools',
                    'icon' => 'share',
                    'url' => '#',
                    'items' => [
                        ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                        ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                        [
                            'label' => 'Level One',
                            'icon' => 'circle-o',
                            'url' => '#',
                            'items' => [
                                ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                [
                                    'label' => 'Level Two',
                                    'icon' => 'circle-o',
                                    'url' => '#',
                                    'items' => [
                                        ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],*/
            ],
        ]) ?>

    </section>

</aside>
