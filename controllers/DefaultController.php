<?php

namespace Pixelion\AmoCrm\controllers;

use Yii;
use Pixelion\AmoCrm\models\Task;
use yii\web\Controller;

class DefaultController extends Controller
{
    public function beforeAction($action)
    {

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $input = file_get_contents('php://input');
        $data = json_encode($_POST, true);
        if($data){
            if($data->task){
                $r = new Task();
            }
        }
        file_put_contents(Yii::getAlias('@runtime/crm').'file' . time() . '.json', json_encode($_POST, true));
    }
}