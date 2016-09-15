<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation for table `user`.
 */
class m160915_075754_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $query = new Query;
        /**
        * USER TABLE MIGRATION
        */
        $tableSchema = null;
        $rows = false;
        $tableSchema = Yii::$app->db->schema->getTableSchema('user');
        if ($tableSchema !== null) {
            $query->select('*')->from('user');
            $rows = $query->all();
            $this->dropTable('user');
        }
        
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'hash' => $this->string(),
            'created' => $this->integer()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'ban' => $this->string(),
            'role' => $this->string()->notNull(),
        ]);
        if($rows){
            Yii::$app->db->createCommand()->batchInsert(
                'user',
                array(
                    'id',
                    'username',
                    'password',
                    'hash',
                    'created',
                    'email',
                    'ban',
                    'role'
                ),
                $rows
            )->execute();
        }
        
        
        /**
        * SETTINGS TABLE MIGRATION
        */
        $tableSchema = null;
        $rows = false;
        $tableSchema = Yii::$app->db->schema->getTableSchema('settings');
        if ($tableSchema !== null) {
            $query->select('*')->from('settings');
            $rows = $query->all();
            $this->dropTable('settings');
        }
        
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'defaultStatusComment' => $this->smallInteger(4)->notNull(),
            'defaultStatusUser' => $this->smallInteger(4)->notNull()
        ]);
        if($rows){
            Yii::$app->db->createCommand()->batchInsert(
                'settings',
                array(
                    'id',
                    'defaultStatusComment',
                    'defaultStatusUser'
                ),
                $rows
            )->execute();
        } else {
            Yii::$app->db->createCommand()->batchInsert(
                'settings',
                array(
                    'id',
                    'defaultStatusComment',
                    'defaultStatusUser'
                ),
                array(
                    array('id' => 1, 'defaultStatusComment' => 0, 'defaultStatusUser' => 1)
                )
            )->execute();
        }
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('user');
        $this->dropTable('config');
    }
}
