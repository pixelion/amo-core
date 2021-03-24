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
class Task extends ActiveRecord
{

    public $action;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_task}}";
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['id'])) {
            $values['task_id'] = $values['id'];
            unset($values['id']);

            if (isset($values['result'])) {
                $values['result'] = Json::encode($values['result']);
            }
        }

        parent::setAttributes($values, $safeOnly);

    }

    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {

        $params = [];
        $model = new Timeline();
        if ($this->action != 'task_delete') {

            $model->element_id = $this->task_id;
            $model->note_type = $this->task_type;
            $model->account_id = $this->account_id;
            $model->responsible_user_id = $this->responsible_user_id;
            $model->created_user_id = $this->created_user_id;
            $model->element_type = $this->element_type;
            $model->created_at = $this->created_at;
            $model->updated_at = $this->updated_at;
            $params['text'] = $this->text;
            $model->action = 'task_add';
            if ($this->action_close) {
                $model->action = 'task_complete';

            }
            $params['result'] = Json::decode($this->result);
            $model->params = Json::encode($params);

            $model->save(false);
        }

        parent::afterSave($insert, $changedAttributes);
    }


    public static function numberToString($value)
    {
        $value = explode('.', number_format($value, 2, '.', ''));

        $f = new \NumberFormatter('uk', \NumberFormatter::SPELLOUT);
        $str = $f->format($value[0]);

        // Первую букву в верхний регистр.
        $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1, mb_strlen($str));

        // Склонение слова "рубль".
        $num = $value[0] % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1:
                $currency = 'гривня';
                break;
            case 2:
            case 3:
            case 4:
                $currency = 'гривні';
                break;
            default:
                $currency = 'гривень';
        }


        /*switch ($num) {
            case 1: $currency = 'рубль'; break;
            case 2:
            case 3:
            case 4: $currency = 'рубля'; break;
            default: $currency = 'рублей';
        }*/

        return $str . ' ' . $currency . ' ' . $value[1] . ' копеек.';
    }
}
