<?php
$_fn=realpath(__DIR__."/../data")."/data.db";
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=info;port=3306',
    'username' => 'root',
    'password' => "123",
    'charset' => 'utf8',
];
