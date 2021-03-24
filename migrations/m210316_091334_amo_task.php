<?php

use yii\db\Migration;

/**
 * Class m210316_091334_amo_task
 */
class m210316_091334_amo_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('amo_task', [
            'id' => $this->primaryKey(),
            'element_id' => $this->integer()->unsigned(),
            'account_id' => $this->integer()->unsigned(),
            'created_user_id' => $this->integer()->unsigned(),
            'responsible_user_id' => $this->integer()->unsigned(),
            'result_id' => $this->integer()->unsigned(),
            'result_text' => $this->text(),
            'text' => $this->text(),
            'old_text' => $this->text(),
            'task_type' => $this->integer(),
            'status' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210316_091334_amo_task cannot be reverted.\n";

        return false;
    }

}
