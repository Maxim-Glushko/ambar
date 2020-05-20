<?php

namespace app\controllers\admin;

use Yii;
use app\models\Content;
use app\models\admin\ContentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\helpers\Common as CH;

/**
 * ContentController implements the CRUD actions for Content model.
 */
class ContentController extends AdminController
{
    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action) {
        $this->haveYouGotRoles('admin'); // тут все действия только для админа
        return parent::beforeAction($action);
    }

    /**
     * Lists all Content models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Content model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Content model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Content();
        if ($model->load(Yii::$app->request->post())) {
            $model->sequence = Content::find()->max('sequence') + 1;
            if ($model->save()) {
                //return $this->redirect(['view', 'id' => $model->id]);
                Yii::$app->session->setFlash('success', Yii::t('admin', 'The category created'));
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', compact('model'));
    }

    /**
     * Updates an existing Content model.
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
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The category edited'));
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUp($id) {
        $model = $this->findModel($id);
        if ($model->sequence > 1) {
            $model2 = Content::findOne(['sequence' => $model->sequence - 1, 'parent_id' => (int)$model->parent_id]);
            if ($model2) {
                $model2->sequence++;
                $model2->save(false);
            }
            $model->sequence--;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The category was increase'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The category was already the first'));
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
        $maxSequence = Content::find()->where(['parent_id' => $model->parent_id])->max('sequence');
        if ($model->sequence < $maxSequence) {
            $model2 = Content::findOne(['sequence' => $model->sequence + 1, 'parent_id' => (int) $model->parent_id]);
            if ($model2) {
                $model2->sequence--;
                $model2->save(false);
            }
            $model->sequence++;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The category was decrease'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The category was already the last'));
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
        if ($id == Content::MAIN_ID) {
            Yii::$app->session->setFlash('success', 'Главная должна быть открытой');
        } else {
            $model = $this->findModel($id);
            if ($model->show != $type) {
                $model->show = $type;
                $model->save(true);
            }
            Yii::$app->session->setFlash('success', Yii::t('admin', $type
                ? 'The category became visible' : 'The category became invisible'));
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param $type
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionShowHideMenu($id, $type) {
        $model = $this->findModel($id);
        if ($model->showmenu != $type) {
            $model->showmenu = $type;
            $model->save(true);
        }
        Yii::$app->session->setFlash('success', Yii::t('admin', $type
            ? 'The category will show in product menu' : 'The category will not show in product menu'));
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Content model.
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
     * Finds the Content model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Content the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Content::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
