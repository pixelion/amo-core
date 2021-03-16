<?php

namespace Pixelion\AmoCrm\components;

use Yii;

class Leads extends AmoCore
{
    protected $data = [];
    private $action;

    public function __construct($post)
    {

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


                file_put_contents(Yii::getAlias('@runtime/crm/') . 'none.json', json_encode($post, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            }

            // file_put_contents(Yii::getAlias('@runtime/crm/') . 'test1.json', array_keys($post));


        }

    }

    private function update()
    {
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'update.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function add()
    {
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'add.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }

    private function delete()
    {
        file_put_contents(Yii::getAlias('@runtime/crm/') . 'delete.json', json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    }
}