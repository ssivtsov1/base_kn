<?php


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

//$this->title = 'Відключення у електромережах';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-spr1">

    <h3><?= Html::encode($this->title) ?></h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'projects',
            'plan_status',
            'year',
            'month',
            'txt',
            'speed',
        ],
    ]); ?>



</div>



