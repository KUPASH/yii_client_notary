<?php

use yii\db\Migration;

/**
 * Class m190818_194858_filesTable
 */
class m190818_194858_filesTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'files',
            [
                'id' => $this->primaryKey(),
                'real_name_client_file' => $this->string(250)->notNull(),
                'hash_name_client_file' => $this->string(250)->notNull(),
                'short_client_key' => $this->string(30)->notNull(),
                'real_name_notary_file' => $this->string(250)->null(),
                'hash_name_notary_file' => $this->string(250)->null(),
                'short_notary_key' => $this->string(30)->null(),
                'user_id' => $this->integer()->notNull(),
                'order_id' => $this->integer()->notNull(),
            ]
        );
        $this->addForeignKey(
            'files_user_FK',
            'files',
            'user_id',
            'users',
            'id'
        );
        $this->addForeignKey(
            'files_order_FK',
            'files',
            'order_id',
            'orders',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'files_user_FK',
            'files'
        );
        $this->dropForeignKey(
            'files_order_FK',
            'files'
        );
        $this->dropTable('files');
    }
}
