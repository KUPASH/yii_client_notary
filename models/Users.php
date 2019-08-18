<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $login
 * @property string $pass
 * @property int $type_user
 *
 * @property Orders[] $orders
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ROLE_CLIENT = 1;
    const ROLE_NOTARY = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'pass', 'type_user'], 'required'],
            [['type_user'], 'integer'],
            [['login'], 'string', 'max' => 30],
            [['pass'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Pass',
            'type_user' => 'Type User',
        ];
    }

    public static function findIdentity($id)
    {
        return Users::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['user_id' => 'id']);
    }
    public function getFiles()
    {
        return $this->hasMany(Files::className(), ['user_id' => 'id']);
    }
}
