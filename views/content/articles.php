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

$articles = $dataProvider->models;

?>

<?php if (count($articles)) { ?>
    <div class="row">
        <?php foreach ($articles as $article) { ?>
            <?php $url = CH::$pLng . '/' . $content->slug . '/' . $article->slug; ?>
            <div class="col-md-12 col-lg-6 one-article">
                <a href="<?= $url ?>" class="article-img">
                    <img src="<?= Picture::getThumb($article->img) ?>"
                         alt="<?= Html::encode($article->v('name')) ?>"
                         title="<?= Html::encode($article->v('name')) ?>" />
                </a>
                <h4>
                    <a href="<?= $url ?>">
                        <?= $article->v('name') ?>
                    </a>
                </h4>
                <p><?= $article->v('description') ?></p>
                <a href="<?= $url ?>" class="article-more">
                    <?= Yii::t('common', 'More') ?>
                </a>
                <div style="clear:both;"></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
