<?php
/**
 * Main config file
 * @package Easyland
 * @version 0.3
 */
$MY_EMAIL = array("test@kompolom.ru");//admin
define('FROM', "test@kompolom.ru");
define('SERVER', "ssl://smtp.yandex.ru");
define('LOGIN',"test@kompolom.ru");
define('PW',"");
define('USE_SMTP',0);
define('SMTP_PORT',465);
define('SITENAME','Easyland');
define('SITE_URL','');
define('GEODB','lib/SxGeoCity.dat');
define('MSG_SUCCESS','Мы свяжемся с вами в ближайшее время');

$formsrc = json_decode(file_get_contents('forms.json'), TRUE);
$fieldsrc = json_decode(file_get_contents('fields.json'), TRUE);
$content = json_decode(file_get_contents('content.json'), TRUE);
//ini_set('display_errors',true);
/*
 * метки рекламных кампаний
 */
$utmmarks = array(
    'utm_source'=>'Источник перехода по метке (utm_source)',
    'utm_medium'=>'Тип трафика(utm_medium)',
    'utm_campaign'=>'Кампания(utm_campaign)',
    'utm_term'=>'Ключевые слова(utm_term)',
    'utm_city'=> 'Регион по метке(utm_city)'
);
$statMarks = array(
    'ip'=>'IP Клиента',
    'city'=>'Регион по IP',
    'referer'=>'Источник перехода'
);

/**
 * ниже поля форм на сайте
 * В этой версии сделана привязка полей к конкретной форме.
 * @use forms.json
 * @use fields.json
 */
$fields = array(
    'name',
    'phone',
    'email',
    'formid'=>'Заполненая форма');
//Поля для отправки в смс
$smsfields = array(
   'name'=>'Имя',
   'phone'=>'Телефон', 
   'email', 
);

$fields = array_merge($fields, $utmmarks);
$fields = array_merge($fields, $statMarks);

 $formnames = array(
    'не определено',   
 );
 
  
 $rules = array(
     0=>array(
         'name'=>array('maxLenght'=>20)
     )
 );
 /* SMSWEB */
$sms = array(
  'login' => '',
  'pass'  => '',
  'from'  => 'INFORM',
  'to'    => '');
?>
