<?php

namespace app\controllers;

use app\models\EmailConfirmForm;
use app\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ContactForm;
use yii\base\Exception;
use app\models\PasswordResetForm;
use app\models\ChangePasswordForm;
use yii\widgets\ActiveForm;
use app\helpers\Common as CH;

class SiteController extends PapaController {
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'register'],
                'rules' => [
                    [
                        'actions' => ['register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }


    /**
     * Login action.
     * @return string|Response
     * @throws \yii\base\ExitException
     */
    public function actionLogin() {
        /*try {*/
            if (!Yii::$app->user->isGuest) {
                //return $this->goHome();
                $where = in_array(Yii::$app->user->identity->role, ['admin', 'manager']) ? '/admin' : false;
                return $this->goBack($where);
            }

            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                $where = in_array(Yii::$app->user->identity->role, ['admin', 'manager']) ? '/admin' : false;
                return $this->goBack($where);
            }
        /*} catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('success', Yii::t('login', 'You need to change password'));
            //$this->redirect(Url::to(['/password-reset'])); // так флеш не сохраняется
            Yii::$app->response->redirect('/password-reset')->send();
            Yii::$app->end();
        }*/

        $model->password = '';
        $this->layout = 'clear';
        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return array|string|Response
     * @throws Exception
     */
    public function actionRegister() {
        $model = new RegisterForm();
        $modelLoad = $model->load(Yii::$app->request->post());
        // здесь можно вставить логирование попыток
        if (Yii::$app->request->isAjax) {
            if ($modelLoad) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            return '';
        } else {
            if ($modelLoad) {
                if ($user = $model->signup()) {
                    if (Yii::$app->getUser()->login($user, 3600*24*30)) {
                        Yii::$app->getSession()->setFlash('success', Yii::t('login', 'Please check your email'));
                        return $this->goHome();
                    }
                }
            }

            $this->layout = 'clear';
            return $this->render('register', ['model' => $model]);
        }
    }

    /**
     * @param int $id
     * @param string $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionEmailConfirm($id, $token) {
        try {
            $model = new EmailConfirmForm($id, $token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('success',
                Yii::t('login', 'Thank you! Your email has ben successfully checked.'));
        } else {
            Yii::$app->getSession()->setFlash('error',
                Yii::t('login', 'There is email confirm error'));
        }

        return $this->goHome();
    }

    /**
     * Requests password reset.
     * @return string|Response
     * @throws Exception
     */
    public function actionPasswordReset() {
        $model = new PasswordResetForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success',
                    Yii::t('login', 'Check your email for further instructions.'));
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error',
                    Yii::t('login', 'Sorry, we are unable to reset password for email provided.'));
            }
        }
        $this->layout = 'clear';
        return $this->render('password-reset', ['model' => $model]);
    }

    /**
     * Resets password.
     * @param string $token
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionChangePassword($token) {
        try {
            $model = new ChangePasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->user->login(User::findOne(['id' => $model->_user->id]));
            Yii::$app->session->setFlash('success', Yii::t('login', 'New password was saved.'));
            return $this->goHome();
        }

        $this->layout = 'clear';
        return $this->render('change-password', ['model' => $model]);
    }
}
