<?php

namespace app\controllers;

use app\models\Content;
use Yii;
use app\models\Product;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveRecord;
use app\helpers\Common as CH;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends PapaController
{
    /**
     * @param $slug
     * @param $pslug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug, $pslug)
    {
        $product = $this->findModelBySlug($pslug);

        if ($slug != $product->content->slug) { // если не в своей основной категории
            return $this->redirect(CH::$pLng . '/' . $product->content->slug . '/' . $pslug);
        }

        return $this->render('view', compact('product'));
    }

    /**
     * @param $slug
     * @return array|ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModelBySlug($slug)
    {
        $model = Product::find()->with('content', 'pictures')
            ->where(['slug' => $slug])
            //->andWhere('availability > 0 AND status > 0')
            ->andWhere('status > 0')
            ->one();
        if ($model && $model->content) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
