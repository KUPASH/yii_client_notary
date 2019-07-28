<?php

namespace app\models;

use yii\base\Model;

class NotaryForm extends Model
{
    public $pdfFile;

    public function rules()
    {
        return [
            [['pdfFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'pdfFile' => 'Download signed file'
        ];
    }
}