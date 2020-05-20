<?php

namespace app\controllers;

use app\models\Content;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;
use app\helpers\Common as CH;
use Yii;
use app\models\News;

/**
 * NewsController implements the CRUD actions for Content model.
 */
class NewsController extends PapaController
{

    /**
     * Displays a single Content model.
     * @param string $slug
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $slug)
    {
        $model = $this->findModelBySlug($slug);
        $content = Content::findOne(Content::NEWS_ID);
        return $this->render('view', compact('model', 'content'));
    }

    /**
     * @param $slug
     * @return array|ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModelBySlug($slug)
    {
        $query = News::find()->where(['slug' => $slug]);
        if (!CH::isAdmin()) {
            $query->andWhere(['show' => 1])->andWhere('publish_at < ' . time());
        }
        $model = $query->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
