<?php
//namespace app\models;
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\type_r;
use app\models\type_src;
//$role = Yii::$app->user->identity->role;
//debug($model)

?>
<script>
   window.onload=function(){
    $(document).click(function(e){

	  if ($(e.target).closest("#recode-menu").length) return;

	   $("#rmenu").hide();

	  e.stopPropagation();

	  });
   }

 function showfields(p) {
     if (p == 3) {
         $('.field-base_kn-file').show();
     } else {
         $('.field-base_kn-file').hide();
     }
 }
</script>

<br>
<div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,]); ?>

    <?= $form->field($model, 'theme_1')->textInput() ?>
    <?= $form->field($model, 'theme_2')->textInput() ?>
    <?= $form->field($model, 'theme_3')->textInput() ?>
    <?= $form->field($model, 'theme_4')->textInput() ?>
    <?= $form->field($model, 'tag')->textInput() ?>
    <?= $form->field($model, 'link')->textInput() ?>

        <?= $form->field($model, 'type_r')->dropDownList(ArrayHelper::map(type_r::find()
            ->all(), 'id', 'name'),[ 'prompt' => 'Виберіть тип ресурсу.','onchange' => 'showfields($(this).val());',]); ?>
        <?= $form->field($model, 'file')->fileInput() ?>
        <? if($model->type_r==3): ?>
            <?= $form->field($model, 'file_path')->textInput(['enable'=>'false']) ?>
        <? endif; ?>
        <?= $form->field($model, 'src_type')->dropDownList(ArrayHelper::map(type_src::find()
            ->all(), 'id', 'name')); ?>
        <?= $form->field($model, 'author_src')->textInput(); ?>

        <br>
        <br>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'ОК' : 'OK', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
