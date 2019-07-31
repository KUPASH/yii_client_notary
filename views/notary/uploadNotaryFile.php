<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form = ActiveForm::begin() ?>

<?= $form->field($model, 'pdfFile')->fileInput(['options' => ['enctype' => 'multipart/form-data']])?>

<div class="form-group" xmlns="http://www.w3.org/1999/html">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end();?>