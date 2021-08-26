<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;

$this->params['breadcrumbs'][] = 'База знань';
?>
<div class="site-spr1">

    <?php echo Html::a('Експорт в Excel', ['site/norms2excel'
    ],
        ['class' => 'btn btn-info excel_btn',
            'data' => [
                'method' => 'post',
                'params' => [
                    'data' => $sql
                ],
            ]]); ?>

    <h3><?= Html::encode($this->title) ?></h3>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'emptyText' => 'Нічого не знайдено',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                /**
                 * Указываем класс колонки
                 */
                'class' => \yii\grid\ActionColumn::class,
                'buttons'=>[

                    'update'=>function ($url, $model) use ($sql) {
                        $customurl=Yii::$app->getUrlManager()->
                        createUrl(['/site/update_kn','id'=>$model['id'],'mod'=>'base_kn','sql'=>$sql]);
                        return \yii\helpers\Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $customurl,
                            ['title' => Yii::t('yii', 'Редагувати'), 'data-pjax' => '0']);
                    }
                ],
                'template' => '{update}',
            ],
            'theme_1',
            'theme_2',
            'theme_3',
            'theme_4',
            'resurs',
            'tag',
            'content',
            'srcname',
            'date',
            'username',
            'author_src',
            'file_path',
            [
                'attribute' => 'Тип',
                'format' => 'image',
                'value'=>function($data) {
                    if($data->type_doc=='L')
                         return $data->imageurl;
                    if($data->type_doc=='T')
                        return $data->imageurl_t; },
            ],
        ],
    ]); ?>

</div>





