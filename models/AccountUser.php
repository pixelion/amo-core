<?php

namespace Pixelion\AmoCrm\models;


use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 */
class AccountUser extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_users}}";
    }


    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

}
