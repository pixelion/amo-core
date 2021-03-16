<?php

use yii\db\Migration;

/**
 * Class m210213_092335_amo_leads
 */
class m210213_092335_amo_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('amo_leads', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->unsigned(),
            'name' => $this->string(255)->null(),
            'pipeline_id' => $this->integer()->unsigned(),
            'status_id' => $this->integer()->unsigned(),
            'old_status_id' => $this->integer()->unsigned(),
            'responsible_user_id' => $this->integer()->unsigned(),
            'created_user_id' => $this->integer()->unsigned(),
            'modified_user_id' => $this->integer()->unsigned(),


            'price' => $this->integer()->unsigned(),


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
