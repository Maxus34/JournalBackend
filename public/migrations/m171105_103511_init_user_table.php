<?php

use app\models\User;
use yii\db\Expression;
use yii\db\Schema;
use yii\db\Migration;


class m171105_103511_init_user_table extends Migration
{
    public static $tableName = 'user';

    public function safeUp()
    {
        $this->createTable(static::$tableName, [
            'id'       => $this->primaryKey()->unsigned(),
            'email'    => $this->string(255),

            'name'       => $this->string(100)->notNull(),
            'surname'    => $this->string(100)->notNull(),
            'patronymic' => $this->string(100)->notNull(),

            'role'   => $this->string(64)->defaultValue(User::ROLE_STUDENT),
            'status' => $this->integer(3)->defaultValue(User::STATUS_UNCONFIRMED),

            'passwordHash'       => $this->string(255)->notNull(),
            'passwordResetToken' => $this->string(255),
            'emailConfirmToken'  => $this->string(255),

            'createdAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'updatedAt'   => $this->dateTime()->defaultExpression('NOW()'),
            'createdBy'   => $this->integer(11)->unsigned(),
            'updatedBy'   => $this->integer(11)->unsigned(),

            'lastLoginIp' => $this->string(20),
            'lastLoginAt' => $this->dateTime(),
        ]);

        $this->createIndex('idx_user', static::$tableName, ['email', 'authKey', 'passwordHash', 'status', 'role']);
        $this->createIndex('idx_user-name', static::$tableName, ['name', 'surname', 'patronymic']);

        $this->createBasicUserRecords();
    }


    public function safeDown()
    {
        $this->dropIndex('idx_user', static::$tableName);
        $this->dropIndex('idx_user-name', static::$tableName);

        $this->dropTable(static::$tableName);
    }


    protected function createBasicUserRecords () {
        $this->batchInsert(static::$tableName,
            ['id', 'email', 'role', 'status', 'name', 'surname', 'patronymic', 'passwordHash', 'createdAt', 'createdBy'],
            [
                ['1', 'admin@a.b',   User::ROLE_ADMIN,     User::STATUS_ACTIVE, 'Admin', 'Admin', 'Admin',       Yii::$app->security->generatePasswordHash('admin'),   new Expression('NOW()'), '1'],
                ['2', 'moder@a.b',   User::ROLE_MODER,     User::STATUS_ACTIVE, 'Moder', 'Moder', 'Moder',       Yii::$app->security->generatePasswordHash('moder'),   new Expression('NOW()'), '1'],
                ['3', 'student@a.b', User::ROLE_STUDENT,   User::STATUS_ACTIVE, 'Student', 'Student', 'Student', Yii::$app->security->generatePasswordHash('student'), new Expression('NOW()'), '1'],
                ['4', 'parent@a.b',  User::ROLE_PARENT,    User::STATUS_ACTIVE, 'Parent', 'Parent', 'Parent',    Yii::$app->security->generatePasswordHash('parent'),  new Expression('NOW()'), '1']
            ]
        );
    }
}
