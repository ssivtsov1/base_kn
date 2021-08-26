<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class A_diary_search extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'vw_diary';
    }

    public function attributeLabels()
    {
        return [
            'date' => 'Дата записи',
            'txt' => 'Текст записи',
            'projects' => 'Название проекта',
            'status' => 'Название статуса проекта',
        ];
    }

    public function search($params, $sql)
    {
        $query = A_diary_search::findBySql($sql);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // $query->andFilterWhere([
        //    'work' => $this->work,
        // ]);

//        $query->andFilterWhere(['like', 'work', $this->work]);
//        $query->andFilterWhere(['like', 'usluga', $this->usluga]);
//        $query->andFilterWhere(['=', 'stavka_grn', $this->stavka_grn]);
//        $query->andFilterWhere(['=', 'time_transp', $this->time_transp]);
//        $query->andFilterWhere(['=', 'type_transp', $this->type_transp]);
//        $query->andFilterWhere(['like', 'brig', $this->brig]);
//        $query->andFilterWhere(['=', 'cast_1', $this->cast_1]);
//        $query->andFilterWhere(['=', 'cast_2', $this->cast_2]);
//        $query->andFilterWhere(['=', 'cast_3', $this->cast_3]);
//        $query->andFilterWhere(['=', 'cast_4', $this->cast_4]);
//        $query->andFilterWhere(['=', 'cast_5', $this->cast_5]);
//        $query->andFilterWhere(['=', 'cast_6', $this->cast_6]);

        return $dataProvider;
    }

    public static function getDb()
    {
        return Yii::$app->get('db_mysql');
    }
}