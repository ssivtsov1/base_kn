<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;
$arr = ['Вибір компанії','Виконавець','ВОЕ','СОЕ','ЦЕК','ЧОЕ','ЧОЕ (викл.?)'];
?>
<div class = 'test col-xs-3' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'fio')->label('П.І.Б.:') -> textInput() ?>

    <?= $form->field($model, 'company')->label('Компанія')-> textInput()
        -> dropDownList ($arr)
    ?>

    <?= Html::submitButton('Надіслати',['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
</div>
