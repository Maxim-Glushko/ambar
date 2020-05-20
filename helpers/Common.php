<?php

namespace app\helpers;

use app\helpers\Common as CH;
use app\models\Content;
use app\models\Article;
use app\models\News;
use app\models\Product;
use app\models\Setting;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\ArrayHelper as AH;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

/**
 * Class Common
 */
class Common {

    const DEFAULT_AVA = '/img/users/ava/default.jpg';
    const DEFAULT_IMG = '/img/200x200/nedostizhimyy-ostrov.jpg';
    const FOREVER = 4294967295;


    /**
     * @param $str
     * @return bool
     * является ли строка простым числом
     */
    public static function isInt($str) {
        $str = (string) $str;
        $str_int = (int) $str;
        return (($str_int > 0) && ((string) $str_int === $str));
    }

    public static function dateFormat($time) {
        if (!$time || ($time < 1000)) {
            return '';
        }
        $date = date('G:i, j M Y', $time);
        $months = (static::$lng == 'ru') ? [
            'Jan' => 'января', 'Feb' => 'февраля', 'Mar' => 'марта', 'Apr' => 'апреля',
            'May' => 'мая', 'Jun' => 'июня', 'Jul' => 'июля', 'Aug' => 'августа',
            'Sep' => 'сентября', 'Oct' => 'октября', 'Nov' => 'ноября', 'Dec' =>'декабря'
        ] : [
            'Jan' => 'січня', 'Feb' => 'лютого', 'Mar' => 'березня', 'Apr' => 'квітня',
            'May' => 'травня', 'Jun' => 'червня', 'Jul' => 'липня', 'Aug' => 'серпня',
            'Sep' => 'вересня', 'Oct' => 'жовтня', 'Nov' => 'листопада', 'Dec' =>'грудня'
        ];
        foreach ($months as $en => $ours) {
            if (strpos($date, $en)) {
                $date = str_replace($en, $ours, $date);
                break;
            }
        }
        return $date;
    }

    public static function misyac($num) {
        $months = (static::$lng == 'ru') ? [
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня',
            7 => 'июля', 8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        ] : [
            1 => 'січня', 2 => 'лютого', 3 => 'березня', 4 => 'квітня',
            5 => 'травня', 6 => 'червня', 7 => 'липня', 8 => 'серпня',
            9 => 'вересня', 10 => 'жовтня', 11 => 'листопада', 12 =>'грудня'
        ];
        return $months[$num] ?? '';
    }

    public static function mon($num) {
        $months = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun',
            7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'
        ];
        return $months[$num] ?? '';
    }

    public static function reMon($mon) {
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
            'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
        ];
        return $months[$mon] ?? 0;
    }

    public static function daysInMonth($monthNum) {
        $days = [
            1 => 31,  2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30,
            7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31
        ];
        return $days[$monthNum] ?? 0;
    }

    // создаёт неупорядоченный массив из номеров страниц, которые можно показывать в перечне страниц
    /*public static function pager($page, $maxPage){
        // вводные данные: текущий номер страницы и общее количество страниц

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
    }*/

    private static $userAvas = [];

    /**
     * @param $user array|User|false
     * @return bool|string
     */
    public static function avaSrc($user = false) {
        $id = $user ? $user['id'] : 0;
        if (!isset(static::$userAvas[$id])) {
            $src = ($id && $user['ava']) ? ('/img/users/ava/' . $id . '/' . $user['ava']) : false;
            static::$userAvas[$id] = ($src && file_exists($_SERVER['DOCUMENT_ROOT'] . $src))
                ? $src
                : static::DEFAULT_AVA;
        }
        return static::$userAvas[$id];
    }

    public static function img($src) {
        if (!$src || !file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
            $src = static::DEFAULT_IMG;
        }
        return $src;
    }

    public static function getCreatedAt($target, $prev) {
        // должно влезть в выходные с утра до ночи либо с 19 до 02 будней с датой позднее предыдущей
        // если не влезает, пересчитывать, пока не получится
        $plus = mt_rand(-1*24*60*60, 3*24*60*60);
        $created_at = $target + $plus;
        if ($created_at <= $prev) {
            $created_at = $prev + rand(7*60, 2*60*60);
        }

        $f = false;

        $date_format_n = date('N', $created_at);
        $date_format_g = date('G', $created_at);
        unset($date);

        if ($date_format_n == 1) { // пн
            if (($date_format_g >= 0) && ($date_format_g <= 2))
                $f = true;
        } elseif ($date_format_n > 5) { // сб, вс
            if (($date_format_g >= 6) && ($date_format_g <= 23))
                $f = true;
            if (($date_format_g >= 0) && ($date_format_g <= 2))
                $f = true;
        } else {
            if (($date_format_g >= 19) && ($date_format_g <= 23))
                $f = true;
            if (($date_format_g >= 0) && ($date_format_g <= 2))
                $f = true;
        }

        return ($f && ($created_at > $prev)) ? $created_at : static::getCreatedAt($target, $prev);
    }

    /** @throws ForbiddenHttpException */
    public static function throwIfNotAjaxAndPost() {
        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            throw new ForbiddenHttpException('Только ajax & post. Только так.');
        }
    }

    /**
     * @param int $number
     * @param array $words
     * @return string
     */
    public static function plural($number, $words) {
        return ($number % 10 == 1 && $number % 100 != 11)
            ? $words[0]
            : (
                ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20))
                    ? $words[1]
                    : $words[2]
            );
    }

    public static function rewriteSitemap($itemType = false) {

        $folder = Yii::getAlias('@app') . '/data/sitemap/';
        $siteName = Url::home(true);
        // если указан $itemType - удаляем этот файл
        if ($itemType) {
            if (file_exists($folder . $itemType . '.txt')) {
                @unlink($folder . $itemType . '.txt');
            }
        }

        $types = [Morph::CONTENT, Morph::PRODUCT, Morph::NEWS, Morph::ARTICLE];

        // перебираем кусочки карты и создаём, если какого-то не хватает
        foreach ($types as $type) {
            $filePath = $folder . $type . '.txt';
            if (!file_exists($filePath)) {
                switch ($type) {
                    case Morph::CONTENT:
                        $models = Content::find()->select('slug')->where(['show' => 1])
                            ->orderBy(['created_at' => SORT_DESC])->all();
                        $slugs = ArrayHelper::getColumn($models, 'slug');
                        $text = $siteName . implode("\n" . $siteName, $slugs) . "\n";
                        $text .= $siteName . 'ru/' . implode("\n" . $siteName . 'ru/', $slugs) . "\n";
                        file_put_contents($filePath, $text);
                    break;

                    case Morph::NEWS:
                    case Morph::ARTICLE:
                        $prefix = AH::getValue(
                            Content::findOne(($type == Morph::NEWS) ? Content::NEWS_ID : Content::ARTICLES_ID),
                            'slug'
                        );
                        if ($type == Morph::NEWS) {
                            $query = News::find();
                        } else {
                            $query = Article::find();
                        }
                        $models = $query->select('slug')->where(['show' => 1])->orderBy(['created_at' => SORT_DESC])->all();
                        $slugs = ArrayHelper::getColumn($models, 'slug');
                        $text = $siteName . $prefix . '/' . implode("\n" . $siteName . $prefix . '/', $slugs) . "\n";
                        $text .= $siteName . 'ru/' . $prefix . '/' . implode("\n" . $siteName . 'ru/' . $prefix . '/', $slugs) . "\n";
                        file_put_contents($filePath, $text);
                    break;

                    case Morph::PRODUCT:
                        $models = Product::find()->with('content')->where('availability > 0')
                            ->orderBy(['created_at' => SORT_DESC])->all();
                        $text = '';
                        foreach ($models as $model) {
                            $text .= $siteName . $model->content->slug . '/' . $model->slug . "\n";
                            $text .= $siteName . 'ru/' . $model->content->slug . '/' . $model->slug . "\n";
                        }
                        unset($models);
                        file_put_contents($filePath, $text);
                        break;
                }
            }
        }

        // собираем кусочки и пишем sitemap.txt
        $text = '';
        foreach ($types as $type) {
            $filePath = $folder . $type . '.txt';
            if (file_exists($filePath)) {
                $text .= file_get_contents($filePath);
            }
        }
        file_put_contents(Yii::getAlias('@app') . '/web/sitemap.txt', $text);
    }

    public static $forbiddenSlugs = [
        'login', 'logout', 'register', 'password-reset', 'email-confirm', 'change-password',
        'contents', 'content', 'news', 'new', 'blogs', 'blog', 'hotlinking', 'hotlinkings', 'comment', 'comments',
        'user', 'users', 'message', 'messages', 'onthisdays', 'onthisday', 'stories', 'story', 'knows', 'know',
        'tags', 'tag', 'pictures', 'picture',
        'page', 'pages', 'create', 'store', 'edit', 'update', 'delete', 'index', 'view', 'validate', 'submenu',
        'p-change', 're-view'
    ];

    public static function isAdmin() {
        return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin();
    }

    public static function isManager() {
        return !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
    }

    public static function isAdminOrManager() {
        return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdminOrManager();
    }

    public static function v($array, $field) {
        $lang = (Yii::$app->language == 'ru-RU') ? 'ru' : 'ua';
        $lang2 = ($lang == 'ua') ? 'ru' : 'ua';
        return $array[$field . '_' . $lang] ?? $array[$field . '_' . $lang2] ?? '';
    }

    public static function toSlug(string $value) {
        $value = mb_strtolower($value);
        $value = strtr($value, [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'є' => 'e', 'ё' => 'e',
            'ж' => 'zh', 'з' => 'z', 'і' => 'i', 'и' => 'i', 'й' => 'y', 'ї' => 'yi', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => 'y', 'ы' => 'y',
            'ъ' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '\'' => 'y',
        ]);
        $value = preg_replace("!([^a-z0-9-]+)!si","-", $value);
        $value = preg_replace('!-+!','-', $value);
        $value = trim($value, '-');
        return $value;
    }

    public static $pLng = '';
    public static $lng = 'ru';

    public static function d1($time) {
        return $time
            ? (date('j', $time) . ' ' . static::misyac(date('n', $time)) . ' ' . date('’y H:i', $time))
            : '';
    }

    public static function d2($time)
    {
        return $time
            ? (date('j', $time) . ' ' . static::misyac(date('n', $time)) . ' ' . date('’y') . '<br />' . date('H:i', $time))
            : '';
    }

    public static function setLngs() {
        $defLng = array_keys(Yii::$app->params['pLngs'])[0];
        CH::$pLng = AH::getValue(
            Yii::$app->params['pLngs'], Yii::$app->language, Yii::$app->params['pLngs'][$defLng]
        );
        CH::$lng = AH::getValue(
            Yii::$app->params['lngs'], Yii::$app->language, Yii::$app->params['lngs'][$defLng]
        );
    }

    public static function getField($model, $field)
    {
        $curLng = Yii::$app->params['lngs'][Yii::$app->language];
        $f = $field . '_' . $curLng;
        if (!empty($model->$f)) {
            return $model->$f;
        }
        // если не найдено на нужном языке, отдаёт на первом попавшемся не пустом
        foreach (Yii::$app->params['lngs'] as $k => $v) {
            $f = $field . '_' . $v;
            if (!empty($model->$f)) {
                return $model->$f;
            }
        }
        return '';
    }

}
