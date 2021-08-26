<?php
/**
 * Используется для просмотра базы знаний
 */
namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

class Base_kn extends \yii\db\ActiveRecord
{
    public $file;
    public static function tableName()
    {
        return 'vw_base_kn'; //Это вид
    }

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
        ];
    }

    public function rules()
    {
        return [
            [['id','theme_1','theme_2','theme_3','theme_4','type_r','date','src_type','src',
                'page_src','author_src','author_id','content_link','link','content','tag','file',
                'resurs'],'safe'],
            [['file'],'file', 'extensions'=>'txt,xls,xlsx,doc,docx,csv'],
            ];
    }

    public function search($params, $sql)
    {
        $query = Base_kn::findBySql($sql);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $tel = trim($this->tel_mob);
        $query->andFilterWhere(['=', 'tab_nom', $this->tab_nom]);
        $query->andFilterWhere(['like', 'fio', $this->fio]);
        $query->andFilterWhere(['like', 'post', $this->post]);
        if(substr($tel,0,1)=='0' &&  strlen($tel)>1){
            $fnd = '%'.substr($tel,1).'%';
            $query->andFilterWhere(['like', 'tel_mob', $fnd, false]);}
        else
            $query->andFilterWhere(['like', 'tel_mob', only_digit($this->tel_mob)]);

        $query->andFilterWhere(['like', 'tel', $this->tel]);
        $query->andFilterWhere(['like', 'tel_town', only_digit($this->tel_town)]);
        $query->andFilterWhere(['like', 'main_unit', $this->main_unit]);
        $query->andFilterWhere(['like', 'unit_1', $this->unit_1]);
        $query->andFilterWhere(['like', 'unit_2', $this->unit_2]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'email_group', $this->email_group]);

        return $dataProvider;
    }
// Поиск внутри файлов в двух кодировках
    public function search_into($s, $id_user)
    {
        $pos_1 = stripos($s, '|');
        $pos_2 = stripos($s, '&');
        $kind_search=0;  // Обычный поиск подстрок
        $mas_f[0]=$s;
        $flag_search=0; // Признак что хоть какой-то файл найден
        $res1=[]; // Stack - initialisation else
        if ($pos_1 !== false) {
            $mas_f = explode('|', $s);
            $kind_search=1; // Поиск любой из заданных подстрок
        }
        if ($pos_2 !== false) {
            $mas_f = explode('&', $s);
            $kind_search=2;  // Одновременный поиск заданных подстрок
        }
//        $mas_f = explode('|', $s);
        $all_s=count($mas_f);
        $sql = "select file_path from vw_base_kn where author_id=$id_user and file_path is not null";
        $list = Base_kn::findBySql($sql)->asarray()->all();
        $i=0;
        $result='';
//        debug($s);
//        $res=[]; // Stack - initialisation
        $pp=0;
        $q_all = count($list);
        foreach ($list as $v) {
            $pp++;
            $fname = $v['file_path'];
//            $fname = '1.doc';
            // Определяем расширение файла
            $getMime = explode('.', $fname);
            $k1 = count($getMime);
            $type_file = strtolower($getMime[$k1 - 1]);
            switch ($type_file) {
                // doc файлы
                case 'doc':
                        if (file_exists($fname)) {
                            if (($fh = fopen($fname, 'r')) !== false) {
                                $headers = fread($fh, 0xA00);

                                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                                $n1 = ( ord($headers[0x21C]) - 1 );

                                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                                $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

                                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                                $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

                                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                                $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

                                // Total length of text in the document
                                $textLength = ($n1 + $n2 + $n3 + $n4);

                                $extracted_plaintext = fread($fh, $textLength);
                                $extracted_plaintext = mb_convert_encoding( $extracted_plaintext, 'UTF-8', 'UTF-16LE' );
                               $text =  nl2br($extracted_plaintext);

                            } else {
                                $text = '';
                            }
                        } else {
                            $text = '';
                        }

//                        debug($text);
//                        return;

                    $e=0;
                    $res=[]; // Stack - initialisation
                    for ($q = 0; $q < $all_s; $q++) {
                        $s1 = $mas_f[$q];
                        $s = mb_convert_encoding($mas_f[$q], 'CP1251', mb_detect_encoding($mas_f[$q]));  // В Windows кодировке
                        $pos1 = stripos($text, $s);
                        $pos2 = stripos($text, $s1);
                        if (($pos1 !== false) || ($pos2 !== false)) {
                            if(empty($result))
                                $result = "'" . $fname . "'";
                            else
                                $result = $result . ',' . "'" . $fname . "'";
                            $res[$q]='doc'; // Add to stack
                           $res1[$q]='doc'; // Add to stack else
                            $e++;
                            $flag_search=1;
                        }
                    }
                    if(count($res)==$all_s ||  count($res1)==$all_s) $flag_search = 1;
                    else
//                        if($flag_search <> 1)
                            $flag_search = 0;

                    //  Если последняя запись - последний файл в обрабатываемом цикле
                    if($pp==$q_all) {
                        $flag_search = 0;
                        if (count($res1)==$all_s) {
                            if(count($res)==$all_s) {
                                $flag_search = 1;
                            }
                            $flag_like = like_elements($res1);
                            // Если найдено в одном файле
                            if($flag_like==1) $flag_search = 1;
                        }
                    }

                     // Если в стеке не все слова поиска при одновременном поиске - тогда ничего не найдено
                    if((count($res)<>$all_s && $flag_search==0)  && $kind_search==2) $result='';
//                    debug($result);
//                    debug('doc');
//                    debug($res);
//                    debug('res1');
//                    debug($res1);
//                    debug('flag_search= '.$flag_search);
//                    debug('------------------------------------------');
                    break;

                case 'docx':
                    $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
                     $phpWord = $objReader->load($fname);
               // ---------------------------------------------------------------------------
                $text='';
                $sections = $phpWord->getSections();

                foreach ($sections as $s) {
                    $els = $s->getElements();
                    /** @var ElementTest $e */
                    foreach ($els as $e) {
                        $class = get_class($e);
                        if (method_exists($class, 'getElements')) {
                            if ($e instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                $secondSectionElement = $e->getElements();
                                foreach ($secondSectionElement as $secondSectionElementKey => $secondSectionElementValue) {
                                    if ($secondSectionElementValue instanceof \PhpOffice\PhpWord\Element\Text) {
                                        $text .= $secondSectionElementValue->getText();
                                    } else {
                                        $text .= "\n";
                                    }
                                    if ($secondSectionElementValue instanceof \PhpOffice\PhpWord\Element\TextBreak)
                                        $text .= "\n";
                                    // table
                                    if ($secondSectionElementValue instanceof \PhpOffice\PhpWord\Element\Table) {
                                        $rows = $e->getRows();

                                        foreach ($rows as $row) {
                                            $cells = $row->getCells();
                                            foreach ($cells as $cell) {
                                                $celements = $cell->getElements();
                                                foreach ($celements as $celem) {

                                                    if ($celements instanceof \PhpOffice\PhpWord\Element\Text) {
                                                        $text .= $celem->getText();
                                                    } else if ($celem instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                                        foreach ($celem->getElements() as $text1) {
                                                            $text .= $text1->getText();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $text .= $e->getText();
                                    }
                                }
                            }
                        }
                    }
                }
                            $e=0;
                            $res=[]; // Stack - initialisation
                            for ($q = 0; $q < $all_s; $q++) {
                                $s1 = $mas_f[$q];
                                $s = mb_convert_encoding($mas_f[$q], 'CP1251', mb_detect_encoding($mas_f[$q]));  // В Windows кодировке
                                $pos1 = stripos($text, $s);
                                $pos2 = stripos($text, $s1);
                                if (($pos1 !== false) || ($pos2 !== false)) {
                                    if(empty($result))
                                        $result = "'" . $fname . "'";
                                    else
                                        $result = $result . ',' . "'" . $fname . "'";
                                    $res[$q]='docx'; // Add to stack
                                    $res1[$q]='docx'; // Add to stack else
                                    $e++;
                                    $flag_search=1;

                            }
                        }
                    // Если в стеке не все слова поиска при одновременном поиске - тогда ничего не найдено
                    if(count($res)==$all_s ||  count($res1)==$all_s) $flag_search = 1;
                    else
//                        if($flag_search <> 1)
                            $flag_search = 0;

                    //  Если последняя запись - последний файл в обрабатываемом цикле
                    if($pp==$q_all) {
                        $flag_search = 0;
                        if (count($res1)==$all_s) {
                            if(count($res)==$all_s) {
                                $flag_search = 1;
                            }
                            $flag_like = like_elements($res1);
                            // Если найдено в одном файле
                            if($flag_like==1) $flag_search = 1;
                        }
                    }

                    if((count($res)<>$all_s && $flag_search==0)  && $kind_search==2) $result='';
//                    debug($result);
//                    debug('docx');
//                    debug($res);
//                    debug('res1');
//                    debug($res1);
//                    debug('flag_search= '.$flag_search);
//                    debug('----------------------------------------');
                    break;

                // Excel файлы
                case 'xls':
                case 'xlsx':
                    $objPHPExcel = \PHPExcel_IOFactory::load($fname);
                    $sheet = $objPHPExcel->getSheet(0);
                    $highestRow = $sheet->getHighestRow();
                    if($highestRow>100000) break;
                    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

//                    debug($sheetData);
                    $ky=count($sheetData);
                    $e=0;
                    $res=[]; // Stack - initialisation
                    for($iy=1;$iy<=$ky;$iy++){
                           foreach ($sheetData[$iy] as $v) {
                               $str = trim($v);
//                            debug($str);
                               if (empty($str)) continue;
                               for ($q = 0; $q < $all_s; $q++) {
                                   $s1 = $mas_f[$q];
                                   $s = mb_convert_encoding($mas_f[$q], 'CP1251', mb_detect_encoding($mas_f[$q]));  // В Windows кодировке
                                   $pos1 = stripos($str, $s);
                                   $pos2 = stripos($str, $s1);
                                   if (($pos1 !== false) || ($pos2 !== false)) {
                                       if ($i == 0)
                                           if(empty($result))
                                               $result = "'" . $fname . "'";
                                           else
                                               $result = $result . ',' . "'" . $fname . "'";
                                       else
                                           $result = $result . ',' . "'" . $fname . "'";
                                       $i++;
                                       $e++;
                                       $res[$q]='xls'; // Add to stack
                                       $res1[$q]='xls'; // Add to stack else
                                       $flag_search=1;
                                   }
                               }
                           }
                    }

                // Если в стеке не все слова поиска при одновременном поиске - тогда ничего не найдено
                if(count($res)==$all_s ||  count($res1)==$all_s) $flag_search = 1;
                else
//                    if($flag_search <> 1)
                         $flag_search = 0;

               //  Если последняя запись - последний файл в обрабатываемом цикле
                if($pp==$q_all) {
                    $flag_search = 0;
                    if (count($res1)==$all_s) {
                        if(count($res)==$all_s) {
                            $flag_search = 1;
                        }
                        $flag_like = like_elements($res1);
                        // Если найдено в одном файле
                        if($flag_like==1) $flag_search = 1;
                    }
                }

                if((count($res)<>$all_s && $flag_search==0)  && $kind_search==2) $result='';
//                debug($result);
//                debug('xls');
//                debug($res);
//                debug('res1');
//                debug($res1);
//                debug('flag_search= '.$flag_search);
//                debug('----------------------------------------');
                    break;
                // другие файлы (рассматриваем как текстовые)
                case 'txt':
                    $f = fopen($fname, 'r');
                    $j = 0;
                    $e=0;
                    $res=[]; // Stack - initialisation
                    $i_s=0;
                    while (!feof($f)) {
                        $str = trim(fgets($f));
                        $j++;
                        for ($q = 0; $q < $all_s; $q++) {
                            $s1 = $mas_f[$q];
                            $s = mb_convert_encoding($mas_f[$q], 'CP1251', mb_detect_encoding($mas_f[$q]));  // В Windows кодировке
                        $pos1 = stripos($str, $s);
                        $pos2 = stripos($str, $s1);
                        if (($pos1 !== false) || ($pos2 !== false)) {
                           // Что-то найдено
                            if ($i == 0)
                                if(empty($result))
                                      $result = "'" . $fname . "'";
                                else
                                      $result = $result . ',' . "'" . $fname . "'";
                            else
                                $result = $result . ',' . "'" . $fname . "'";
                            $i++;
                            $e++;
                            $res[$q]='txt'; // Add to stack
                            $res1[$q]='txt'; // Add to stack else
                            $flag_search=1;
                        }

                     }
                    }
                    fclose($f);
                    if(count($res)==$all_s ||  count($res1)==$all_s) $flag_search = 1;
                    else
//                        if($flag_search <> 1)
                            $flag_search = 0;

                    //  Если последняя запись - последний файл в обрабатываемом цикле
                    if($pp==$q_all) {
                        $flag_search = 0;
                        if (count($res1)==$all_s) {
                            if(count($res)==$all_s) {
                                $flag_search = 1;
                            }
                            $flag_like = like_elements($res1);
                            // Если найдено в одном файле
                            if($flag_like==1) $flag_search = 1;
                        }
                    }
                    // Если в стеке не все слова поиска  при одновременном поиске - тогда ничего не найдено
                    if((count($res)<>$all_s && $flag_search==0)  && $kind_search==2) $result='';
//                    debug('aaaaaaaaaaaaaaaaaaaaa');
//                    debug($result);
//                    debug('txt');
//                    debug($res);
//                    debug('res1');
//                    debug($res1);
//                    debug('flag_search= '.$flag_search);
//                    debug('----------------------------------------');
                    break;
            }
        }
//       debug('result '.$result);
        return $result;
        }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getDb()
    {
            return Yii::$app->get('db');
    }

    public function getImageurl()
    {
        return \Yii::$app->request->BaseUrl.'/'.'linkprogramingsymbolofinterface_105005.png';
    }
    public function getImageurl_t()
    {
        return \Yii::$app->request->BaseUrl.'/'.'text-document-outlined-symbol_icon-icons.com_57756.png';
    }

}


