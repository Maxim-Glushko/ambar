<?php

/**
 * @var int $page
 * @var string $pagePrefix
 * @var int $maxPage
 * @var string $url
 * @var string $anchor
 * @var array $pages
 * @var integer $defaultPage
 * @var string $sort
 * @var string $sortDefault
 * @var string $sortPrefix
 * @var string $class
 */

$isProfilePage = $isProfilePage ?? false;
$anchor = $anchor ? ('#' . $anchor) : '';
$pSort = ($sort ? ('/' . $sortPrefix . $sort) : '');
?>

<div class="clearfix" style="text-align: center;">
<ul class="pagination <?= $class ?>">

    <?php
    if ($page <> 1) {
        $prevPage = (($defaultPage == 1) && ($page == 2)) ? '' : ('/' . $pagePrefix . ($page - 1));
        $prevUrl = $url . $prevPage . $pSort . $anchor;

        // у статьи может быть предыдущая статья, и это истинный prev,
        // а здесь могли бы в качестве prev всунуться предыдущие старые комменты к этой статье
        if ($page && ($page < $maxPage)) {
            Yii::$app->view->registerLinkTag(['rel' => 'prev', 'href' => $prevUrl]);
            Yii::$app->view->linkTags = array_unique(Yii::$app->view->linkTags);
        }
        ?>
        <li class="prev">
            <a href="<?=  $prevUrl ?>">&laquo;</a>
        </li>
        <?php
    }

    $use = true;
    for ($i = 1; $i < $maxPage + 1; $i++) {
        if (!in_array($i, $pages)) {
            if ($use) {
                ?>
                <li class="nohover"><span>...</span></li>
                <?php
                $use = false;
            }
            continue;
        } else {
            $use = true;
        }
        if ($page == $i) {
            ?>
            <li class="active" ><span><?= $i ?></span></li>
        <?php } else {
            $fullSuffix = ($i == $defaultPage) ? '' : ('/' . $pagePrefix . $i);
            $fullSuffix .= $pSort;
            ?>
            <li>
                <a href="<?= $url . $fullSuffix . $anchor ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php
        }
    }
    if ($page < $maxPage) {
        $nextPage = (($defaultPage == $maxPage) && (($page + 1) == $maxPage)) ? '' : ('/' . $pagePrefix . ($page + 1));
        $nextUrl = $url . $nextPage . $pSort . $anchor;

        Yii::$app->view->registerLinkTag(['rel' => 'next', 'href' => $nextUrl]);
        Yii::$app->view->linkTags = array_unique(Yii::$app->view->linkTags);
        ?>

        <li class="next">
            <a href="<?= $nextUrl ?>">&raquo;</a>
        </li>
    <?php } ?>
</ul>
</div>