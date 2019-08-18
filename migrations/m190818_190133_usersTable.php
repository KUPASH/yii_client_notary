<?php

use yii\db\Migration;

/**
 * Class m190818_190133_usersTable
 */
class m190818_190133_usersTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'users',
            [
                'id' => $this->primaryKey(),
                'login' => $this->string(30)->notNull(),
                'pass' => $this->string(80)->notNull(),
                'type_user' => $this->integer()->notNull(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }
}
