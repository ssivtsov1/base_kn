<?php
/**
 * Используется для просмотра базы знаний
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Base_kn1 extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return 'base_kn'; //Это таблица
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme_1' => 'Розділ',
            'theme_2' => 'Тема',
            'theme_3' => 'Підтема',
            'theme_4' => 'Доб. тема',
            'type_r' => '',
            'tag' => 'Опис',
            'content' => 'Зміст',
            'link' => 'Посилання',
            'date' => 'Дата',
            'src_type' => '',
            'page_src' => 'Сторінка',
            'author_src' => 'Автор',
            'author_id' => '',
            'content_link' => '',
        ];
    }

    public function rules()
    {
        return [
            [['id','theme_1','theme_2','theme_3','theme_4','type_r','date','src_type','src',
                'page_src','author_src','author_id','content_link','link','content','tag',
                'resurs'],'safe']
            ];
    }

    public function search($params, $sql)
    {
        $query = Base_kn1::findBySql($sql);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function upload()
    {
                $path = "store/".$this->file->baseName.'.'.$this->file->extension;
                $this->file->saveAs($path);
                return true;
    }


    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function primaryKey()
    {
        return ['id'];
    }

}


