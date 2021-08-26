<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii \ helpers \ ArrayHelper;
$arr = ['Выбор года','2018','2019'];
?>
<div class = 'test col-xs-3' >
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'date')->label('Дата записи')  -> widget(\yii\jui\DatePicker::classname(), ['language' => 'uk']) ?>

    <?= $form->field($model, 'txt')->label('Текст записи')  -> textInput()  ?>

    <?= $form->field($model, 'projects')->label('Название проекта') -> textInput() -> dropDownList (ArrayHelper::map(
        app\models\A_diary_search::findbysql('select id, txt from project')
            ->all(), 'id', 'txt'),
        ['prompt' => 'Выбор проекта',]) ?>

    <?= $form->field($model, 'status')->label('Название статуса проекта')-> textInput() -> dropDownList (ArrayHelper::map(
        app\models\A_diary_search::findbysql('select id, txt from status_project')
            ->all(), 'id', 'txt'),
        ['prompt' => 'Выбор статуса',]) ?>

    <?= Html::submitButton('Надіслати',['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
</div>
