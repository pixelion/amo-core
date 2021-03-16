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
            'title' => $this->string(12)->notNull()->unique(),
            'body' => $this->text()
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
