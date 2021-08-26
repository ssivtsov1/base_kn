<?php
/* Ввод основных данных для поиска телефонов */

namespace app\models;

use Yii;
use yii\base\Model;

class InputData extends Model
{
    public $id;
    public $theme_1;
    public $theme_2;
    public $theme_3;
    public $theme_4;
    public $type_r;
    public $resurs;
    public $tag;
    public $content;
    public $link;
    public $author_src;
    public $txt_in;

    private $_user;

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme_1' => 'Розділ',
            'theme_2' => 'Тема',
            'theme_3' => 'Підтема',
            'theme_4' => 'Доб. тема',
            'type_r' => 'Тип ресурсу',
            'resurs' => 'Тип ресурсу',
            'tag' => 'Опис',
            'content' => 'Зміст',
            'link' => 'Посилання',
            'date' => 'Дата',
            'src_type' => 'Джерело запису',
            'srcname' => 'Джерело запису',
            'page_src' => 'Сторінка',
            'author_src' => 'Автор',
            'author_id' => '',
            'content_link' => '',
            'file' => 'Файл',
            'file_path' => 'Файл: ',
            'txt_in' => 'Пошук в файлах',
        ];
    }

    public function rules()
    {
        return [
            ['theme_1', 'safe'],
            ['theme_2', 'safe'],
            ['theme_3', 'safe'],
            ['theme_4', 'safe'],
            ['content', 'safe'],
            ['txt_in', 'safe'],
            ['tag', 'safe'],
            ['link', 'safe'],
        ];
    }

}
