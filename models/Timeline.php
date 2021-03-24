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
class Timeline extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%widget_timeline}}";
    }

    public function getRowStyle(){
        $view = '_default';
        if($this->action=='lead_deleted'){
            $view = '_deleted';
        }elseif(in_array($this->action,['leads_note','contacts_note'])){
            $view = '_note';
        }elseif(in_array($this->action,['task_add','task_update','task_complete'])){
            $view = '_task';
        }elseif($this->action=='contacts_note_call'){
            $view = '_call';
        }elseif($this->action=='lead_add' || $this->action=='lead_update' || $this->action=='lead_tags_add' || $this->action=='lead_tags_deleted' || $this->action=='lead_update_diff'){
            $view = '_lead_add';
        }elseif($this->action=='contacts_note_sms'){
            $view = '_sms';
        }elseif($this->action=='note_mail'){
            $view = '_mail';
        }
        return $view;
    }

    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

}
