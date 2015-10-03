<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_091437_flexform_widget extends Migration
{
    public function safeUp()
    {
        $this->createTable( 'flexform_template', [
                'id' => 'pk',
                'name' => 'string NOT NULL',
                'modelClass' => 'string NOT NULL',
                'type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
                'public' => 'boolean DEFAULT FALSE',
                'current' => 'boolean DEFAULT TRUE',
                'FK_user' => Schema::TYPE_INTEGER . ' NOT NULL',
            ], 'DEFAULT CHARSET = utf8' );

        $this->createTable( 'flexform_field', [
                'id' => 'pk',
                'FK_template' => Schema::TYPE_INTEGER . ' NOT NULL',
                'name' => 'string NOT NULL',
                'value' => 'string DEFAULT NULL',
                'order' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            ], 'DEFAULT CHARSET = utf8' );
    }

    public function safeDown()
    {
        $this->dropTable('flexform_field');
        $this->dropTable('flexform_template');
    }
    
}
