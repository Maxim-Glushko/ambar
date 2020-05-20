<?php

namespace app\widgets;

use Yii;
use yii\bootstrap\Widget;
use app\helpers\Common as CH;

class LinkPager extends Widget {

    public $url = '';
    public $anchor = '';

    public $page = 0;
    public $pagePrefix = 'page:';
    public $defaultPage = 1; // в новостях целсообразнее делать последнюю страницу умолчанием, в товарах - первую
    public $maxPage = 0;

    public $sort = '';
    public $sortPrefix = 'sort:';
    public $defaultSort = 'default-asc';

    public $cssClass = '';

    /** @return string */
    public function run() {
        try {
            if ($this->maxPage < 2) {
                return '';
            }
            $this->page = $this->page ?: $this->defaultPage;
            return $this->render('linkPager', [
                'url'           => CH::$pLng . $this->url,
                'anchor'        => $this->anchor,           // то, что идёт после # в адресе
                'page'          => $this->page,
                'defaultPage'   => $this->defaultPage,
                'pagePrefix'    => $this->pagePrefix,
                'maxPage'       => $this->maxPage,          // максимальный номер страницы
                'pages'         => $this->getPages(),
                'sortPrefix'    => $this->sortPrefix,
                'sort'          => (!$this->sort || ($this->sort == $this->defaultSort)) ? '' : $this->sort,
                'class'         => $this->cssClass,
            ]);
        } catch (\Exception $e) {
            Yii::error('LinkPager: ' . $e->getMessage());
            return '';
        }
    }

    /** @return array */
    protected function getPages() {
        $page = $this->page;
        $maxPage = $this->maxPage;
        $results = [];

        $points = [1, $page, $maxPage];
        $points[] = ceil($page / 2);
        $points[] = $page + ceil(($maxPage - $page) / 2);

        for ($i = -2; $i < 3; $i++) {
            foreach ($points as $p) {
                $new = $i + $p;
                if (($new > 0) && ($new < ($maxPage + 1)))
                    $results[] = $new;
            }
        }
        // чтобы не было картины: 3 ... 5, а вместо этого было 3 4 5
        $add = [];
        foreach ($results as $r) {
            if (in_array($r + 2, $results) && !in_array($r + 1, $results)) {
                $add[] = $r + 1;
            }
        }
        $results = array_unique(array_merge($results, $add));
        sort($results);

        return $results;
    }
}
