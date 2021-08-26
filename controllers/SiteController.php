<?php

namespace app\controllers;
//namespace app\models;

use app\models\A_diary;
use app\models\A_diary_search;
use app\models\phones_sap;
use app\models\phones_sap_search;
use app\models\Plan;
use app\models\plan_forma;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\ContactForm;
use app\models\InputData;
use app\models\cdata;
use app\models\employees;
use app\models\shtrafbat;
use app\models\viewphone;
use app\models\list_workers;
use app\models\kyivstar;
use app\models\hipatch;
use app\models\tel_vi;
use app\models\requestsearch;
use app\models\tofile;
use app\models\base_kn;
use app\models\base_kn1;
use app\models\docs;
use app\models\forExcel;
use app\models\info;
use app\models\User;
use app\models\loginform;
use kartik\mpdf\Pdf;
//use mpdf\mpdf;
use yii\web\UploadedFile;

class SiteController extends Controller
{  /**
 * 
 * @return type
 *
 */

    public $curpage;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }



    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'image' => [
                'class' => 'circulon\images\actions\ImageAction',

                // all the model classes to be searched by this action.
                // Can be fully qualified namespace or alias
                'models' => ['base_kn1']
            ]
        ];
    }

    //  Происходит при запуске сайта
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['site/more']);
        }
        if(strpos(Yii::$app->request->url,'/cek')==0)
            return $this->redirect(['site/more']);
        $model = new loginform();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['site/more']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    //  Происходит после ввода пароля
    public function actionMore($sql='0')
    {
        $this->curpage=1;
        if($sql=='0') {
            $model = new InputData();
            if ($model->load(Yii::$app->request->post())) {
                // Создание поискового sql выражения
                $theme_1=mb_strtolower($model->theme_1,'UTF-8');
                $theme_2=mb_strtolower($model->theme_2,'UTF-8');
                $theme_3=mb_strtolower($model->theme_3,'UTF-8');
                $theme_4=mb_strtolower($model->theme_4,'UTF-8');
                $tag=mb_strtolower($model->tag,'UTF-8');
                $content=mb_strtolower($model->content,'UTF-8');
                $where = ' where 1=1 ';
                if (!empty($model->theme_1)) {
                        $where .= ' and LOWER(theme_1)=' . "'" . $theme_1 . "'";
                    }
                if (!empty($model->theme_2)) {
                    $where .= ' and LOWER(theme_2)=' . "'" . $theme_2 . "'";
                }
                if (!empty($model->theme_3)) {
                    $where .= ' and LOWER(theme_3)=' . "'" . $theme_3 . "'";
                }
                if (!empty($model->theme_4)) {
                    $where .= ' and LOWER(theme_4)=' . "'" . $theme_4 . "'";
                }
                if (!empty($model->tag)) {
                    $where .= ' and LOWER(tag) like ' . "'%" . $tag . "%'";
                }
                if (!empty($model->content)) {
                    $where .= ' and LOWER(content) like ' . "'%" . $content . "%'";
                }
                if (!empty($model->link)) {
                    $where .= ' and link like ' . "'%" . $model->link . "%'";
                }
                if (!empty($model->link)) {
                    $where .= ' and author_src like ' . "'%" . $model->author_src . "%'";
                }


                $where = trim($where);
                if (empty($where)) $where = '';

                $sql = "select * from vw_base_kn  " . $where . ' order by date desc';

//            debug($sql);
//            return;

                $f=fopen('aaa','w+');
                fputs($f,$sql);
                $base_kn = new base_kn();

                $find_into='';
                if (!empty($model->txt_in)) {
                    // Поиск внутри файлов - возвращает список названий файлов где встречается строка
                    $find_into = $base_kn->search_into($model->txt_in,1);
                }

//                debug('find_into '.$find_into);
                if(!empty($find_into)) {
                    $sql = "select * from vw_base_kn " . $where .
                        ' and file_path in(' . $find_into . ')' . ' order by date desc';

                }
                else{
                   if (!empty($model->txt_in )){
                       $sql = "select * from vw_base_kn where 1=2";
                   }
                }

//                debug($sql);
//                return;


                $searchModel = new base_kn();
                $data = base_kn::findBySql($sql)->all();
                $kol = count($data);
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);

                return $this->render('base_kn', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel, 'kol' => $kol,'sql' => $sql]);
            } else {

                return $this->render('inputdata', [
                    'model' => $model
                ]);
            }
            }
                
            
        else{
             // Если передается параметр $sql
            $data = base_kn::findBySql($sql)->all();
            $searchModel = new base_kn();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
            $kol = count($data);

            $session = Yii::$app->session;
            $session->open();
            $session->set('view', 1);

            return $this->render('base_kn', ['data' => $data,
                'dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'kol' => $kol, 'sql' => $sql]);
        }
    }

//    ~ Обновление записи
    public function actionUpdate_kn($id,$mod,$sql,$res='')
    {
        // $id  id записи
        // $mod - название модели
//        if($mod=='norm_facts')
//            $model = vneeds_fact::find()
//                ->where('id=:id', [':id' => $id])->one();
        $sql1='select * from ('.$sql.') src '. ' where id='.$id;
        $model = base_kn::findBySql($sql1)->one();

        $session = Yii::$app->session;
        $session->open();
        if($session->has('user'))
            $user = $session->get('user');
        else
            $user = '';

        if ($model->load(Yii::$app->request->post()))
        {
            $model1 = base_kn1::find()->where('id=:id',[':id'=>$id])->one();

            $model1->theme_1 = $model->theme_1;
            $model1->theme_2 = $model->theme_2;
            $model1->theme_3 = $model->theme_3;
            $model1->theme_4 = $model->theme_4;
            if(empty($model1->date))
                 $model1->date = date("Y-m-d");
            $model1->tag = $model->tag;
            $model1->content = $model->content;
            $model1->link = $model->link;
            $model1->author_src = $model->author_src;
            $model1->type_r = $model->type_r;
            $model1->src_type = $model->src_type;

           $model1->file = UploadedFile::getInstance($model, 'file');
           $model1->upload();

            $doc=new Docs();

                $doc->file_path = $model1->file->name;
                $doc->user_id = 1;
                $doc->id_unique = $id;
                $doc->save();

            if(!$model1->save(false))
            {  var_dump($model1);return;}

            if($mod=='base_kn')
                $this->redirect(['site/more','sql' => $sql]);

        } else {
            if($mod=='base_kn')
                return $this->render('update_kn', [
                    'model' => $model
                ]);
        }
    }

// Добавление новых пользователей
    public function actionAddAdmin() {
        $model = User::find()->where(['username' => 'buh1'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'buh1';
            $user->email = 'buh1@ukr.net';
            $user->setPassword('afynfpbz');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }

// Выход пользователя
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

//Щеденники

    public function actionA_diary_forma()
    {
        $model = new A_diary();
        $searchModel = new A_diary_search();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT date,txt,projects,status
FROM vw_diary 
where 1=1";
//            if (!empty($model->txt)) {
//                $sql2 = '(select txt from plan where id ='. $model->txt.')';
//                $model->txt = $sql2;
//                $sql = $sql . ' and txt =' . $model->txt  ;
//            }
//        debug($sql);
//        return;
            if (!empty($model->projects)) {
                $sql = $sql . ' and id_project =' . "'" . $model->projects . "'";
            }
//        debug($sql);
//        return;
            if (!empty($model->status)) {
                $sql = $sql . ' and id_status =' . "'" . $model->status . "'";
            }
//                debug($sql);
//        return;
//            if (!empty($model->year)) {
//                if ($model->year == '1')
//                    $model->year = '2018';
//                if ($model->year == '2')
//                    $model->year = '2019';
//                $sql = $sql . ' and year =' . "'" . $model->year . "'";
//            }
////                        debug($sql);
////        return;
            $sql = $sql . ' ORDER BY 3';
//            debug($sql);
//            return;
//            $data = Off_site::findbysql($sql)->asArray()
//                ->all();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('a_diary_forma_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('a_diary_forma', compact('model'));
        }
    }


    public function actionPlan_forma()
    {
        $model = new Plan_forma();
        $searchModel = new Plan();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT projects, plan_status, year, month, txt, speed
            FROM vw_plans 
            where 1=1";

            if (!empty($model->projects)) {
                $sql = $sql . ' and id_project =' . "'" . $model->projects . "'";
            }
//        debug($sql);
//        return;
            if (!empty($model->plan_status)) {
                $sql = $sql . ' and id_status =' . "'" . $model->plan_status . "'";
            }
//                debug($sql);
//        return;
            if (!empty($model->year)) {
                if ($model->year == '1')
                    $model->year = '2018';
                if ($model->year == '2')
                    $model->year = '2019';
                $sql = $sql . ' and year =' . "'" . $model->year . "'";
            }
//                        debug($sql);
//        return;
            if (!empty($model->month)) {
                $sql = $sql . ' and id_month =' . "'" . $model->month . "'";
            }
            if (!empty($model->txt)) {
                $sql2 = '(select txt from plan where id ='. $model->txt.')';
                $model->txt = $sql2;
                $sql = $sql . ' and txt =' . $model->txt  ;
            }
//        debug($sql);
//        return;
            if (!empty($model->speed)) {
                $sql2 = '(select speed from plan where id ='. $model->speed.')';
                $model->speed = $sql2;
                $sql = $sql . ' and speed =' . $model->speed;
            }
//        debug($sql);
//        return;
            $sql = $sql . ' ORDER BY 1';
//            debug($model);
//            return;
//            $data = Off_site::findbysql($sql)->asArray()
//                ->all();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('plan_forma_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('plan_forma', compact('model'));

        }
    }




    public function actionPhones_sap()
    {
        $model = new phones_sap();
        $searchModel = new phones_sap_search();
//    $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//        $model = $model::find()->all();
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $c = mb_substr($fio,0,1,"UTF-8");
//        $code = ord($c);
//        if($code<128) $fio=recode_c(strtolower($fio));
//
//        $name1 = trim(mb_strtolower($fio,"UTF-8"));
//        $name2 = trim(mb_strtoupper($fio,"UTF-8"));
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//        debug('1111111111111');
            $sql = "SELECT *
FROM contacty_sap
where 1=1";
            if (!empty($model-> fio)) {
                $sql = $sql . " and fio like '" .$model->fio ."%'";
            }
//                debug($sql);
//        return;
            if (!empty($model-> company)) {
                if ($model->company == '1')
                    $model->company = '"Виконавець"';
                if ($model->company == '2')
                    $model->company = '"ВОЕ"';
                if ($model->company == '3')
                    $model->company = '"СОЕ"';
                if ($model->company == '4')
                    $model->company = '"ЦЕК"';
                if ($model->company == '5')
                    $model->company = '"ЧОЕ"';
                if ($model->company == '6')
                    $model->company = '"ЧОЕ (викл.?)"';
                $sql = $sql . " and company = " .$model->company;
            }
//                debug($sql);
//        return;
            $sql = $sql . ' ORDER BY 1';
//            debug($model);
//            return;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sql);
//            debug($sql);
//            return;
            $dataProvider->pagination = false;
            return $this->render('phones_sap_2', [
                'model' => $searchModel,'dataProvider' => $dataProvider,'searchModel' => $searchModel,
            ]);
        } else {
            return $this->render('phones_sap', compact('model'));
        }
    }


}
