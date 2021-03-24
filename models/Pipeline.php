<?php

namespace Pixelion\AmoCrm\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;


/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 */
class Pipeline extends ActiveRecord
{

    public $action;
    public $tineline_date;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_pipeline}}";
    }

    /**
     * @inheritdoc
     */

    public static function find()
    {
        return new TaskQuery(get_called_class());
    }



}
