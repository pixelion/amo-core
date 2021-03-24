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
class Note extends ActiveRecord
{

    public $action;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%amo_account_note}}";
    }

    public function setAttributes($values, $safeOnly = true)
    {
        if (isset($values['id'])) {
            $values['note_id'] = $values['id'];
            unset($values['id']);


            if (isset($values['params'])) {
                if (is_array($values['params'])) {
                    $values['params'] = json_encode($values['params'], JSON_UNESCAPED_UNICODE);
                }
            }
            if (!isset($values['params']) && $this->isJson($values['text'])) {
                $values['params'] = $values['text'];
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

        $user = AccountUser::findOne(['user_id' => $this->modified_by]);

        $model = new Timeline();
        $model->action = $this->action;


        if ($user) {
            $params['owner']['name'] = $user->name;
            $params['owner']['id'] = $user->user_id;

        }


        if (!in_array($this->note_type, [AmoCore::NOTE_DEAL_CREATED])) {
            $model->element_id = $this->element_id;
            $model->account_id = $this->account_id;
            $model->responsible_user_id = $this->main_user_id;
            $model->created_user_id = $this->created_by;
            $model->modified_user_id = $this->modified_by;
            $model->note_type = $this->note_type;
            if (isset($this->params))
                $params['params'] = Json::decode($this->params);

            if ($this->element_type == AmoCore::TYPE_LEAD) {
                // $result .= 'Сделка' . $this->element_id;
            } elseif ($this->element_type == AmoCore::TYPE_CONTACT) {
                //$user2 = AccountUser::findOne(['user_id'=>$this->element_id]);
                //  $result .= 'добавил(а) запись в контакт ' . $this->element_id . '! ';
            } elseif ($this->element_type == AmoCore::TYPE_COMPANY) {
                //  $result .= 'добавил(а) запись в компании' . $this->element_id . '! ';
            }

            if (in_array($this->note_type, [AmoCore::NOTE_CALL_OUT, AmoCore::NOTE_CALL_IN])) {
                // $params = Json::decode($model->params);
                //$test = Json::decode($this->metadata);
                if (isset($params['DURATION']))
                    $result .= 'Исходящий звонок ' . $this->secToArray($params['DURATION']) . ' Разговор состоялся';

                $model->action = 'contacts_note_call';
            } elseif (in_array($this->note_type, [AmoCore::NOTE_SMS_IN, AmoCore::NOTE_SMS_OUT])) {
                $model->action = 'contacts_note_sms';

                if ($this->isJson($this->text)) {
                    //  $params = Json::decode($model->params);
                    //$params = Json::decode($model->text);
                    //$model->params = $this->text;
                    //$result .= $this->main_user_id . ' Отправил SMS на ' . $params['PHONE'] . ' ' . $params['TEXT'];

                }

            }elseif($this->note_type == AmoCore::NOTE_SYSTEM){
                $model->action = 'note_system';
               // $params['params']=$this->params;
            }elseif($this->note_type == AmoCore::NOTE_MAIL){
                $model->action = 'note_mail';
                if ($this->isJson($this->text)) {
                    $params['params'] = Json::decode($this->text);
                    if(isset($this->message_uuid))
                        $params['message_uuid'] = $this->message_uuid;
                }
            }

            $result .= $this->text;
            $model->result = $result;
            $model->element_type = $this->element_type;
            $model->params = Json::encode($params);
            $model->created_at = $this->created_at;
            $model->updated_at = $this->updated_at;
            $model->save(false);
        }


        parent::afterSave($insert, $changedAttributes);
    }

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function secToString($secs)
    {
        $res = '';

        $days = floor($secs / 86400);
        if ($days) {
            $res .= $days . ':';
        }
        $secs = $secs % 86400;
        $hours = floor($secs / 3600);
        if ($hours) {
            $res .= $hours . ':';
        }
        $secs = $secs % 3600;
        $minutes = floor($secs / 60);
        if ($minutes) {
            $res .= $minutes . ':';
        }
        $sec = $secs % 60;
        if ($sec) {
            $res .= $sec;
        }
        return $res;
    }


}
