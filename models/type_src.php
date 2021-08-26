<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Type_src extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'type_src';
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Назва',
        ];
    }

    public function search($params)
    {
        $query = type_src::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

//    public static function getDb()
//    {
//        return Yii::$app->get('db_mysql');
//    }
}