<?php

abstract class rule{
  protected $error; //описание ошибки;
  protected $errcode;//код ошибки;
  protected $hasError;//bool метка если тест не пройден
  protected $param;    //параметр валидации

  function __construct($param = NULL){
    $this->param = $param;
  }

  abstract public function check($val);

  public function getError(){
    if ($this->hasError){
      return $this->error;
    }
  }
  public function getErrcode(){
    if ($this->hasError){
      return $this->errcode;
    }
  }

}


class rule_Required extends rule{
  protected $error = 'Не заполнено обязательное поле';
  protected $errcode = 1;
  

  function check($val){
    if (empty($val)){
      $this->hasError = TRUE;
      return FALSE;
    }  
    return TRUE;
  }

}

class rule_maxLenght extends rule{
  protected $error = 'Превышено допустимое количество символов';
  protected $errcode = 2;
    
  function __construct($param=1){    
    parent::__construct($param);
  }

  function check($val){
    if (strlen($val) > $this->param){
      $this->hasError = TRUE;
      return FALSE;
    }
    return TRUE;
    
  }

  
}
