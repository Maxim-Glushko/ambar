<?php

namespace app\controllers\admin;

use Yii;
use app\models\Unit;
use app\models\admin\UnitSearch;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends AdminController
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
     * {@inheritdoc}
     */
    /*public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

    /**
     * Lists all Unit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Unit model.
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
     * Creates a new Unit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Unit();

        if ($model->load(Yii::$app->request->post())) {
            $model->sequence = Unit::find()->max('sequence') + 1;
            if ($model->save()) {
                //return $this->redirect(['view', 'id' => $model->id]);
                Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit created'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Unit model.
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
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit updated'));
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
            $model2 = Unit::findOne(['sequence' => $model->sequence - 1]);
            if ($model2) {
                $model2->sequence++;
                $model2->save(false);
            }
            $model->sequence--;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit was increased'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit was already the first'));
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
        $maxSequence = Unit::find()->max('sequence');
        if ($model->sequence < $maxSequence) {
            $model2 = Unit::findOne(['sequence' => $model->sequence + 1]);
            if ($model2) {
                $model2->sequence--;
                $model2->save(false);
            }
            $model->sequence++;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit was decreased'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The unit was already the last'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Unit model.
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
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }
}
