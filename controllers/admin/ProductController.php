<?php

namespace app\controllers\admin;

use app\helpers\Common as CH;
use app\helpers\Morph;
use app\models\Content;
use app\models\Picture;
use Yii;
use app\models\Product;
use app\models\admin\ProductSearch;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper as AH;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends AdminController
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
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            $pictureData = AH::getValue($post, 'Product.picture_data', []);
            Picture::sync(Morph::PRODUCT, $model->id, $pictureData);
            Product::contentSync(
                $model->id,
                AH::getValue($post, 'Product.cat_id', 0),
                AH::getValue($post, 'Product.cat_ids', [])
            );
            CH::rewriteSitemap();
            //return $this->redirect(['view', 'id' => $model->id]);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The product created'));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        // TODO: проверить, не менялась ли главная категория, записать sequence, записать в product_content

        if ($model->load($post)) {
            if ($model->save()) {
                $pictureData = AH::getValue($post, 'Product.picture_data', []);
                Picture::sync(Morph::PRODUCT, $model->id, $pictureData);
                Product::contentSync(
                    $model->id,
                    AH::getValue($post, 'Product.cat_id', 0),
                    AH::getValue($post, 'Product.cat_ids', [])
                );
                //return $this->redirect(['view', 'id' => $model->id]);
                Yii::$app->session->setFlash('success', Yii::t('admin', 'The product updated'));
                return $this->redirect(['update', 'id' => $model->id]);
            }
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
        $pc = $model->productContentMain;
        if ($model->sequence > 1) {
            $model2 = Product::find()
                //->innerJoinWith('productContentMain')
                ->innerJoin('product_content', 'product_content.product_id = products.id')
                ->where(['products.sequence' => $model->sequence - 1, 'product_content.main' => 1, 'product_content.content_id' => $pc->content_id])
                ->one();
            if ($model2) {
                $model2->sequence++;
                $model2->save(false);
            }
            $model->sequence--;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The product was increase'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The product was already the first'));
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
        $pc = $model->productContentMain;
        $maxSequence = Product::find()
            //->innerJoinWith('productContentMain')
            ->innerJoin('product_content', 'product_content.product_id = products.id')
            ->where(['product_content.main' => 1, 'product_content.content_id' => $pc->content_id])
            ->max('products.sequence');
        if ($model->sequence < $maxSequence) {
            $model2 = Product::find()
                //->innerJoinWith('productContentMain')
                ->innerJoin('product_content', 'product_content.product_id = products.id')
                ->where(['products.sequence' => $model->sequence + 1, 'product_content.main' => 1, 'product_content.content_id' => $pc->content_id])
                ->one();
            if ($model2) {
                $model2->sequence--;
                $model2->save(false);
            }
            $model->sequence++;
            $model->save(false);
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The product was decrease'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The product was already the last'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('common', 'The requested page does not exist.'));
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionUpload() {
        CH::throwIfNotAjaxAndPost();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = Picture::upload();
        if (empty($result['error'])) {
            $form = new ActiveForm;
            $picture = ['description_ua' => '', 'description_ru' => ''];
            $model = new Product;
            $i = Yii::$app->request->post('index', 1);
            $src = $result['src'];
            return [
                'html' => $this->renderAjax('one-picture', compact('form', 'picture', 'model', 'i', 'src'))
            ];
        } else {
            return $result;
        }
    }
}
