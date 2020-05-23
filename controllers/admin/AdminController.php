<?php

namespace app\controllers\admin;

use app\helpers\Common as CH;
use yii\base\Action;
use yii\helpers\ArrayHelper as AH;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\web\Response;
use yii\base\ExitException;
use yii;

class AdminController extends Controller
{
    public $layout = '@app/views/admin/layouts/main';

    /**
     * @param $roles
     * @return string|array
     * @throws ForbiddenHttpException
     */
    public function haveYouGotRoles($roles) {
        $result = false;
        if (!Yii::$app->user->isGuest && !empty($roles)) {
            if (!is_array($roles)) {
                $roles = [$roles];
            }
            foreach ($roles as $role) {
                if ($role == Yii::$app->user->identity->role) {
                    $result = true;
                }
            }
        }
        if (!$result) {
            if (Yii::$app->user->isGuest) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['error' => Yii::t('common', 'You must login')];
                } else {
                    Yii::$app->session->setFlash('warning', Yii::t('common', 'You are not allowed'));
                    try {
                        //return $this->redirect('/login'); // так флеш не отправляется
                        Yii::$app->response->redirect('/login')->send();
                        Yii::$app->end();
                    } catch (\Exception $e) {
                        return $this->redirect('/login');
                    }
                }
            } else {
                $message = Yii::t('yii', 'You are not allowed to perform this action.');
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['error' => $message];
                } else {
                    throw new ForbiddenHttpException($message);
                }
            }
        }
        return true;
    }

    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action) {
        // если не указано иное в контроллере-потомке, разрешаем все действия для админа и менеджера
        // иначе в потомке переопределяем и сужаем только для админа
        $this->haveYouGotRoles(['admin', 'manager']);
        CH::setLngs();
        return parent::beforeAction($action);
    }

    /*public function actionEditable() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['output' => '11', 'message'=>'222', 'value' => 333];

        $model = new Demo;

        if (isset($_POST['hasEditable'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $_POST['editableAttribute'];
            $_POST['User.1.username'];


            if ($model->load($_POST)) {
                // read or convert your posted information
                $value = $model->name;

                // return JSON encoded output in the below format
                return ['output'=>$value, 'message'=>''];

                // alternatively you can return a validation error
                // return ['output'=>'', 'message'=>'Validation error'];

            } else { // else if nothing to do always return an empty JSON encoded output
                return ['output'=>'', 'message'=>''];
            }
        }

        // Else return to rendering a normal view
        //return $this->render('view', ['model' => $model]);
        return ['output'=>'', 'message'=>''];
    }*/
}