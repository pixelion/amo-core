<?php

namespace Pixelion\AmoCrm\components;

use Pixelion\AmoCrm\models\Timeline;
use Yii;

class Message extends AmoCore
{
    protected $data = [];
    private $action;

    public function __construct($post)
    {
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'message.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if (isset($post)) {
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


                file_put_contents(Yii::getAlias('@runtime/crm/') . 'message.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            }

            // file_put_contents(Yii::getAlias('@runtime/crm/') . 'test1.json', array_keys($post));


        }else{

        }

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
            $model->action = 'message_update';
            $model->result = 'Изменен контакт';
            $model->save(false);
        }

        file_put_contents(Yii::getAlias('@runtime/crm/') . 'message_update.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function add()
    {
        foreach ($this->data as $data){
            $model = new Timeline();
            $model->element_id = $data['id'];
            $model->account_id = $data['account_id'];
            $model->responsible_user_id = $data['responsible_user_id'];
            $model->created_user_id = $data['created_user_id'];
            $model->result = $data['text'];
            $model->created_at = $data['created_at'];
           // $model->updated_at = $data['updated_at'];
            $model->action = 'message_add';
            $model->save(false);
        }
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'message_add.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

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
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'message_delete.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }
}