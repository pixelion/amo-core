<?php

namespace Pixelion\AmoCrm\components;

use Pixelion\AmoCrm\models\Timeline;
use Yii;

class Contacts extends AmoCore
{
    protected $data = [];
    private $action;

    public function __construct($post)
    {
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

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


                    file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                }
            }


            // file_put_contents(Yii::getAlias('@runtime/crm/') . 'test1.json', array_keys($post));


        }else{

        }

    }
    private function note()
    {
        foreach ($this->data as $data) {
            $model = new Timeline();
            if($data['note']['note_type'] == 11){ //call
                $model->action = 'contact_call';
                $model->result = $data['text'];
            }else{
                $model->action = 'contact_note';
                $model->responsible_user_id = $data['note']['responsible_user_id'];
                $model->created_user_id = $data['note']['created_user_id'];
            }
            $model->element_id = $data['note']['element_id'];
            $model->account_id = $data['note']['account_id'];
            $model->created_at = $data['note']['created_at'];
            $model->updated_at = $data['note']['updated_at'];

            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact_note.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }
    private function update()
    {
        foreach ($this->data as $data){
            $model = new Timeline();
            $model->element_id = $data['id'];
            $model->account_id = $data['account_id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->created_user_id = $data['created_user_id'];
            $model->modified_user_id = $data['modified_user_id'];
            $model->created_at = $data['created_at'];
            $model->updated_at = $data['updated_at'];
            $model->action = 'contact_update';
            $model->result = 'Изменен контакт';
            $model->save(false);
        }

        file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact_update.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function add()
    {
        foreach ($this->data as $data){
            $model = new Timeline();
            $model->element_id = $data['id'];
            $model->account_id = $data['account_id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->created_user_id = $data['created_user_id'];
            $model->result = 'Добавлен контакт: '.$data['name'];
            $model->created_at = $data['created_at'];
            $model->updated_at = $data['updated_at'];
            $model->action = 'contact_add';
            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact_add.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function delete()
    {
        /*foreach ($this->data as $data){
            $model = Timeline::findOne(1);
            $model->account_id = $data['id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->result = $data['text'];
            $model->created_at = $data['created_at'];
            $model->updated_at = $data['updated_at'];
            $model->action = 'task_delete';
            $model->save(false);
        }*/
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'contact_delete.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }
}