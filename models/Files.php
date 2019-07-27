<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $real_name_client_file
 * @property string $hash_name_client_file
 * @property string $short_client_key
 * @property string $real_name_notary_file
 * @property string $hash_name_notary_file
 * @property string $short_notary_key
 * @property int $user_id
 * @property int $order_id
 *
 * @property Orders $order
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['real_name_client_file', 'hash_name_client_file', 'short_client_key', 'user_id', 'order_id'], 'required'],
            [['user_id', 'order_id'], 'integer'],
            [['real_name_client_file', 'hash_name_client_file', 'real_name_notary_file', 'hash_name_notary_file'], 'string', 'max' => 250],
            [['short_client_key', 'short_notary_key'], 'string', 'max' => 30],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'real_name_client_file' => 'Real Name Client File',
            'hash_name_client_file' => 'Hash Name Client File',
            'short_client_key' => 'Short Client Key',
            'real_name_notary_file' => 'Real Name Notary File',
            'hash_name_notary_file' => 'Hash Name Notary File',
            'short_notary_key' => 'Short Notary Key',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
