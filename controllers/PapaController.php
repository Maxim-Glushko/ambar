<?php

namespace app\controllers;

use app\helpers\Common as CH;
use yii\helpers\ArrayHelper as AH;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\web\Response;
use yii\base\ExitException;
use yii;

class PapaController extends Controller
{
    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action) {
        CH::$pLng = AH::getValue(
            Yii::$app->params['pLngs'], Yii::$app->language, Yii::$app->params['pLngs'][Yii::$app->params['defLng']]
        );
        return parent::beforeAction($action);
    }
}