<?php

namespace Pixelion\AmoCrm\models;

use Pixelion\AmoCrm\components\AmoCore;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;


/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 */
class Company extends ActiveRecord
{

    public $action;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_company}}";
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['id'])) {
            $values['company_id'] = $values['id'];
            unset($values['id']);



                if (is_array($values['custom_fields'])) {
                    $values['custom_fields'] = json_encode($values['custom_fields'], JSON_UNESCAPED_UNICODE);
                }


                if (is_array($values['linked_leads_id'])) {
                    $values['linked_leads_id'] = json_encode($values['linked_leads_id'], JSON_UNESCAPED_UNICODE);
                }



                if (is_array($values['tags'])) {
                    $values['tags'] = json_encode($values['tags'], JSON_UNESCAPED_UNICODE);
                }


        }

        parent::setAttributes($values, $safeOnly);

    }

    public static function find()
    {
        return new TaskQuery(get_called_class());
    }



    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }




}
