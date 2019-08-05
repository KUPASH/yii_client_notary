<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin() ?>
<?= $form->field($model, 'login')->textInput(['placeholder' => 'Example: admin@gmail.com']) ?>
<?= $form->field($model, 'password')->textInput(['placeholder' => 'Password should contain 6-15 characters'])?>
<?= $form->field($model, 'typeUser')->dropDownList(['1' => 'client', '2' => 'notary'],
    ['prompt' => 'Choose type of user'])?>
<?= $form->field($model, 'remember_me')->checkbox()?>

<div class="form-group" xmlns="http://www.w3.org/1999/html">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end();?>
