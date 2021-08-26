<?php

namespace app\models;

use yii\base\Model;




class A_diary extends Model
{
    public $date;
    public $txt;
    public $projects;
    public $status;


    public function attributeLabels()
    {
        return [
            'date' => 'Дата записи',
            'txt' => 'Текст записи',
            'projects' => 'Название проекта',
            'status' => 'Название статуса проекта',
        ];
    }
    public function rules()
    {
        return [

            [['date','txt', 'projects', 'status','id_status',
            ], 'safe'],

        ];
    }

}