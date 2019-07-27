<?php
namespace app\models;

use yii\base\Model;

class LoginForm extends Model
{
    public $login;
    public $password;
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['login', 'email'],
            ['password', 'string', 'min' => 6, 'max' => 15],
        ];
    }
}