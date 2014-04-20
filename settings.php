<?php
define(MY_EMAIL,"jenik@kompolom.ru");//
define(FROM, "jenik@kompolom.ru");
define(SERVER, "smtp.yandex.ru");
define(LOGIN,"jenik@kompolom.ru");
define(PW,"C5d30deee3");
define(USE_SMTP,1);
define(SITENAME,'My sitename');
define(SITE_URL,'');
define(GEODB,'lib/SxGeoCity.dat');
define(MSG_SUCCESS,'Мы свяжемся с вами в ближайшее время');

/*
 * метки рекламных кампаний
 */
$utmmarks = array(
    'utm_source'=>'Источник перехода по метке',
    'utm_medium'=>'тип трафика',
    'utm_campaign'=>'Кампания',
    'utm_term'=>'Ключевые слова',
    'utm_city'=> 'Регион по метке'
);
$statMarks = array(
    'ip'=>'IP Клиента',
    'city'=>'Регион по IP',
    'reffer'=>'Источник перехода'
);

/*
 * ниже поля форм на сайте
 */
$fields = array('name'=>'Имя',
                'phone'=>'Телефон',                
                'email',                
                'formid'=>'Заполненая форма');

$fields = array_merge($fields, $utmmarks);
$fields = array_merge($fields, $statMarks);

 $formnames = array(
     'не определено',
     'Акция (верх)',
     'ВЫЕЗД К ЗАКАЗЧИКУ И ОСМОТР',
     'БЕСПЛАТНАЯ КОНСУЛЬТАЦИЯ'  ,
     'обратный звонок',     
     
 );
?>
