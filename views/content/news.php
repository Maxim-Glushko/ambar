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

?>

<?php if (count($dataProvider->models)) { ?>
    <div class="news-list">
        <div class="row">
            <?php foreach ($dataProvider->models as $model) { ?>
                <?php $url = CH::$pLng . '/' . $content->slug . '/' . $model->slug; ?>
                <div class="col-md-12 col-lg-6">
                    <div class="one-news">
                        <a href="<?= $url ?>" class="news-img">
                            <img src="<?= Picture::getThumb($model->img) ?>"
                                 alt="<?= Html::encode($model->v('name')) ?>"
                                 title="<?= Html::encode($model->v('name')) ?>" />
                        </a>
                        <div class="news-texts">
                            <h4>
                                <a href="<?= $url ?>">
                                    <?= $model->v('name') ?>
                                </a>
                            </h4>
                            <span class="news-date">
                                <?= CH::d1($model->published_at ?: $model->created_at) ?>
                            </span>
                            <p><?= $model->v('description') ?></p>
                        </div>
                        <a href="<?= $url ?>" class="news-more">
                            <?= Yii::t('common', 'More') ?>...
                        </a>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
