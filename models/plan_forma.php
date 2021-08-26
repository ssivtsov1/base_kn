<?php

namespace app\models;

use yii\base\Model;
//use yii\web\Plan_forma;



class plan_forma extends Model
{
    public $projects;
    public $plan_status;
    public $year;
    public $month;
    public $txt;
    public $speed;

    public function attributeLabels()
    {
        return [
            'projects' => 'Проект',
            'plan_status' => 'Статус плана',
            'year' => 'Год',
            'month' => 'Месяц',
            'txt' => 'План',
            'speed' => 'Степень срочности',
        ];
    }
    public function rules()
    {
        return [

            [['projects','plan_status', 'year', 'month','txt','speed',
            ], 'safe'],

        ];
    }

}