<?php
/**
 * Отправка смс
 * Поддерживается только smsareo
 *
 * @package Easyland
 * @version 0.1
 */
class smsweb{
  private $url = 'http://gate.smsaero.ru/';
  private $login; // Ваш номер телефона указанный при регистрации (Например: 79056665533)
  private $pass; // - Ваш пароль
  private $to; //  - Номер получателя сообщения (Например: 79056665533)
  private $from;  //[FROM] - Смс имя отправителя
  private $text; // - Текст сообщения
  private $time; // - Время отправки в UNIX формате
  
  function __construct($login, $pass, $from) {
    $this->login = $login;
    $this->pass = md5($pass);
    $this->from = $from;
  }
  function to($number){
    $phone = $this->clean_number($number);
    if($this->check_number($phone)){
      $this->to = $phone;
      return true;
    }else{
      return false;
    }
  }
  function from($name){
    $this->from = name;
  }
  function text($txt){
    $this->text = $txt;
  }
  private function clean_number($number){
    $phone = preg_replace('/(\s)|(\+)|(\-)|(\()|(\))/i', '', $number);
    return $phone;
  }
  private function check_number($phone){
    return strlen($phone) == 11;
  }
  function send(){
    $data = array(
        'user' =>  $this->login,
        'password'  =>  $this->pass,
        'to'    =>  $this->to,
        'text'  =>  $this->text,
        'from'  =>  $this->from
    );
    $querystring = $this->url.'send';
    $querystring .='?'.http_build_query($data);
    $options = array(
      "http" => array(
        "method" => "GET", 
        )
    );
    
    $result = fopen($querystring,'r',false, stream_context_create($options));
    if ($result){
      $status = stream_get_contents($result);
      fclose($result);      
      return $status;
    }
    
  }

}

