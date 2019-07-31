<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin() ?>
<?= $form->field($model, 'name')->textInput(['placeholder' => 'Example: Your name']) ?>
<?= $form->field($model, 'city')->textInput(['placeholder' => 'Your city'])?>
<?= $form->field($model, 'documentTitle')?>
<?= $form->field($model, 'newFile')->fileInput(['options' => ['enctype' => 'multipart/form-data']])?>

<div class="form-group" xmlns="http://www.w3.org/1999/html">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end();?>

