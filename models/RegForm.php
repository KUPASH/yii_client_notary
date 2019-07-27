<?php
namespace app\models;

use yii\base\Model;
use Yii;

class RegForm extends Model
{
    public $login;
    public $password;
    public $typeUser;
    public function rules()
    {
        return [
            [['login', 'password', 'typeUser'], 'required'],
            ['login', 'email'],
            ['login', 'unique', 'targetClass' => Users::class, 'message' => 'This username has already been taken.'],
            ['password', 'string', 'min' => 6, 'max' => 15],
        ];
    }
    public function setPassword($password)
    {
        return $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

}