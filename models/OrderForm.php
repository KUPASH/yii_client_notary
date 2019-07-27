<?php
namespace app\models;

use yii\base\Model;

class OrderForm extends Model
{
    public $name;
    public $city;
    public $documentTitle;
    public $newFile;
    public function rules()
    {
        return [
            [['name','city','documentTitle','newFile'], 'required'],
            [['newFile'], 'file', 'extensions' => 'pdf'],
        ];
    }


}