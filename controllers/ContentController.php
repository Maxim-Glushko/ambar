<?php

namespace app\controllers;

use app\models\ProductSearch;
use app\models\ArticleSearch;
use app\models\NewsSearch;
use app\models\Product;
use app\models\Content;
use app\models\Cart;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveRecord;
use app\helpers\Common as CH;
use Yii;

/**
 * ContentController implements the CRUD actions for Content model.
 */
class ContentController extends PapaController
{

    /**
     * Displays a single Content model.
     * @param string $slug
     * @param integer|null $page
     * @param string|null $sort
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($slug = '', $page = null, $sort = null)
    {
        $content = $this->findModelBySlug($slug);
        $params = [];
        $poly = false;

        if ($content->id == Content::ARTICLES_ID) {
            $poly =  true;
            $searchClass = ArticleSearch::class;
            $defaultSort = ArticleSearch::$sorts[0];
        } elseif ($content->id == Content::NEWS_ID) {
            $poly =  true;
            $searchClass = NewsSearch::class;
            $defaultSort = NewsSearch::$sorts[0];
        } elseif ($content->showmenu || ($content->id == Content::MAIN_ID)) {
            $poly =  true;
            $searchClass = ProductSearch::class;
            $defaultSort = ProductSearch::$sorts[0];
        }

        if ($poly) {
            if ($sort) {
                // если сортировка по умолчанию, она нам в адресной строке не нужна
                if ($sort == $defaultSort) {
                    return $this->redirect(CH::$pLng . '/' . $slug
                        . ((!empty($page) && ($page > 1)) ? ('/page:' . $page) : ''));
                }
            }
            // если указана первая страница, она не нужна, ибо и так умолчание
            if ($page == 1) {
                return $this->redirect(CH::$pLng . '/' . $slug . ($sort ? ('/sort:' . $sort) : ''));
            } elseif ($page > 1) {
                $params['page'] = $page;
                // sort в params не суём, ибо бесполезно - он ловится из get
            }
            $searchModel = new $searchClass;
            if ($content->showmenu || ($content->id == Content::MAIN_ID)) {
                $params['ProductSearch'] = ['content_id' => $content->id];
            }
            $dataProvider = $searchModel->search($params);
        } else {
            $searchModel = false;
            $dataProvider = false;
            $defaultSort = false;
        }

        return $this->render('view',
            compact('content', 'searchModel', 'dataProvider', 'page', 'sort', 'defaultSort'));
    }

    /**
     * Finds the Content model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Content the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Content::findOne($id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

    /**
     * @param $slug
     * @return array|ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModelBySlug($slug)
    {
        $model = Content::find()->where(['slug' => $slug, 'show' => 1])->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
