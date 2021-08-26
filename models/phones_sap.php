<?php

namespace app\models;

use yii\base\Model;




class phones_sap extends Model
{
    public $fio;
    public $login;
    public $company;
    public $rolу;
    public $group;
    public $tell_mob;
    public $tell;
    public $e_mail;
    public $skype;


    public function attributeLabels()
    {
        return [
            'fio' => 'Прізвище',
            'login'=> 'Логин SAP',
            'company' => 'Компанія',
            'rolу' => 'Роль',
            'group' => 'Функціональная група',
            'tell_mob' => 'Мобільний телефон',
            'tell' => 'Робочий телефон',
            'e_mail' => 'E-mail',
            'skype' => 'Skype',
        ];
    }
    public function rules()
    {
        return [

            [['fio','login','company','rolу','group','tell_mob','tell','e_mail','skype',
            ], 'safe'],

        ];
    }

}