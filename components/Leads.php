<?php

namespace Pixelion\AmoCrm\components;

use Pixelion\AmoCrm\models\AccountUser;
use Pixelion\AmoCrm\models\Timeline;
use Yii;

class Leads extends AmoCore
{
    protected $data = [];
    private $action;

    public function __construct($post)
    {

        if (isset($post)) {
            if (isset($post['note'])) {
                $this->data = $post['note'];
                $this->note();
                // file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_none.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            } else {
                if (isset($post['update'])) {
                    $this->data = $post['update'];
                    $this->update();
                } elseif ($post['add']) {
                    $this->data = $post['add'];
                    $this->add();
                } elseif ($post['delete']) {
                    $this->data = $post['delete'];
                    $this->delete();
                } else {


                    file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead' . time() . '.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                }

                // file_put_contents(Yii::getAlias('@runtime/crm/') . 'test1.json', array_keys($post));

            }
            //  file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead'.time().'.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        }

    }

    private function update()
    {
        foreach ($this->data as $data) {
            $model = new Timeline();
            $model->element_id = $data['id'];
            $model->account_id = $data['account_id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->created_user_id = $data['created_user_id'];
            $model->modified_user_id = $data['modified_user_id'];
             $model->pipeline_id = $data['pipeline_id'];



            $user = AccountUser::findOne(['user_id'=>$data['modified_user_id']]);
            if($user){
                $model->owner_name = $user->name;
                $model->owner_photo = $user->photo_url;
                $model->result = $model->owner_name. ': Изменил сделку '.$data['id'].' Бюджен '.$data['price'];

            }else {
                $model->result = $data['modified_user_id'] . ': Изменил сделку ' . $data['id'] . ' Бюджен ' . $data['price'];
            }


                $model->created_at = $data['created_at'];
            $model->updated_at = $data['updated_at'];
            $model->action = 'lead_update';
            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_update.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function add()
    {
        foreach ($this->data as $data) {
            $model = new Timeline();
            $model->element_id = $data['id'];
            $model->account_id = $data['account_id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->created_user_id = $data['created_user_id'];
            $model->pipeline_id = $data['pipeline_id'];

            $user = AccountUser::findOne(['user_id'=>$data['created_user_id']]);
if($user){
    $model->owner_name = $user->name;
    $model->owner_photo = $user->photo_url;
    $model->result = $user->name. ': Создал сделку '.$data['id'].' Бюджен '.$data['price'];

}else{
    $model->result = $data['created_user_id']. ': Создал сделку '.$data['id'].' Бюджен '.$data['price'];

}

            $model->created_at = $data['created_at'];
            $model->updated_at = $data['updated_at'];
            $model->action = 'lead_add';
            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_add.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function note()
    {
        foreach ($this->data as $data) {
            $model = new Timeline();
            if (!in_array($data['note']['note_type'],[self::NOTE_DEAL_CREATED])) {
                $model->element_id = $data['note']['element_id'];
                $model->account_id = $data['note']['account_id'];
                $model->responsible_user_id = $data['note']['responsible_user_id'];
                $model->created_user_id = $data['note']['created_user_id'];
                $model->result = $data['note']['text'];
                $model->created_at = $data['note']['created_at'];
                $model->updated_at = $data['note']['updated_at'];
                $model->action = 'lead_note';
                $model->save(false);
          }
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_note.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function delete()
    {
        foreach ($this->data as $data) {
            $model = new Timeline();
            $model->element_id = $data['pipeline_id'];
            $model->action = 'lead_deleted';
            // $model->pipeline_id =$data['pipeline_id'];
            $model->result = 'Удалил сделку: ' . $data['pipeline_id'];
            $model->created_at = time();
            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'lead_delete.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }
}