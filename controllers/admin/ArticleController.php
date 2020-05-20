<?php

namespace app\controllers\admin;

use app\models\Content;
use Yii;
use app\models\Article;
use app\models\admin\ArticleSearch;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends AdminController
{
    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action) {
        $this->haveYouGotRoles('admin');
        return parent::beforeAction($action);
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();

        if ($model->load(Yii::$app->request->post())) {
            $model->sequence = Article::find()->max('sequence') + 1;
            $model->show = 1;
            if ($model->save()) {
                //return $this->redirect(['view', 'id' => $model->id]);
                Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article created'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article updated'));
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUp($id) {
        $model = $this->findModel($id);
        if ($model->sequence > 1) {
            $model2 = Article::find()->where(['sequence' => $model->sequence - 1])->one();
            if ($model2) {
                $model2->sequence++;
                $model2->save(false);
            }
            $model->sequence--;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article was increase'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article was already the first'));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDown($id) {
        $model = $this->findModel($id);
        $maxSequence = Article::find()->max('sequence');
        if ($model->sequence < $maxSequence) {
            $model2 = Article::find()->where(['sequence' => $model->sequence + 1])->one();
            if ($model2) {
                $model2->sequence--;
                $model2->save(false);
            }
            $model->sequence++;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article was decrease'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The Article was already the last'));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param $type
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShowHide($id, $type) {
        $model = $this->findModel($id);
        if ($model->show != $type) {
            $model->show = $type;
            $model->save(true);
        }
        Yii::$app->session->setFlash('success', Yii::t('admin', $type
            ? 'The Article became visible' : 'The Article became invisible'));

        return $this->redirect(['index']);
    }
}
