<?php

namespace Pixelion\AmoCrm\controllers;

use AmoCRM\Exception;
use Yii;
use Pixelion\AmoCrm\models\Task;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class DefaultController extends \yii\rest\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;


        try {
            $post = Yii::$app->request->post();


            if (empty($post)) {
                throw new Exception('Post is empty!');
            }


            if (isset($post['task'])) {
                $response = new \Pixelion\AmoCrm\components\Task($post['task']);
            } elseif (isset($post['leads'])) {
                $response = new \Pixelion\AmoCrm\components\Leads($post['leads']);
            } else {
                $data = json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents(Yii::getAlias('@runtime/crm/') . 'resposonse.json', $data);
            }

            //

            return [];
        } catch (Exception $e) {
            print_r($e);
        }


    }
}