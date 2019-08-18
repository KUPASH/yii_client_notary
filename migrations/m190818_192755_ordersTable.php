<?php

use yii\db\Migration;

/**
 * Class m190818_192755_ordersTable
 */
class m190818_192755_ordersTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'orders',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(30)->notNull(),
                'city' => $this->string(30)->notNull(),
                'document_title' => $this->string(80)->notNull(),
                'status' => $this->string(30)->notNull(),
                'notary_id' => $this->integer()->null(),
                'user_id' => $this->integer()->notNull(),
            ]
        );
        $this->addForeignKey(
            'orders_user_FK',
            'orders',
            'user_id',
            'users',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'orders_user_FK',
            'orders'
        );
        $this->dropTable('orders');
    }
}
