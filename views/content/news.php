<?php

use app\models\Picture;
use app\models\Content;
use yii\helpers\Html;
use app\helpers\Common as CH;
use yii\data\ActiveDataProvider;

/**
 * @var $content Content
 * @var $page integer
 * @var $sort string
 * @var $dataProvider ActiveDataProvider
 */

$news = $dataProvider->models;

?>

<?php if (count($news)) { ?>
    <div class="row">
        <?php foreach ($news as $one) { ?>
            <?php $url = CH::$pLng . '/' . $content->slug . '/' . $one->slug; ?>
            <div class="col-md-12 col-lg-6 one-news">
                <a href="<?= $url ?>" class="news-img">
                    <img src="<?= Picture::getThumb($one->img) ?>"
                         alt="<?= Html::encode($one->v('name')) ?>"
                         title="<?= Html::encode($one->v('name')) ?>" />
                </a>
                <h4>
                    <a href="<?= $url ?>">
                        <?= $one->v('name') ?>
                    </a>
                </h4>
                <p><?= $one->v('description') ?></p>
                <a href="<?= $url ?>" class="news-more">
                    <?= Yii::t('common', 'More') ?>
                </a>
                <div style="clear:both;"></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
