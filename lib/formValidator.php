<?php

/**
 * Description of formValidator
 *
 * @author Evgeniy Baranov (master@kompolom.ru) 
 * 
 * 
 * 
 * array @rules массив правил валидации имя поля => правила
 * 
 * $rules = array(
 *   'formID'=>array(  //форма
 *       'name'=>array(  //поле
 *           'required', //методы валидатора
 *           'name',
 *           'maxLenght'=>10,//метод с параметром
 *       ),
 *   ),
 *  );
 * 
 * 
 * 
 * @validate проверяет значени на соответствие правилам.
 *  Возвращает boolean значение пройдена ли проверка
 * @getError текст ошибки либо Пустая строка, если проверка пройдена.
 */
class formValidator {
  /**
   *
   * @var array $rules набор объектов-правил
   */
  private $rules = array();
  /**
   *
   * @var array $messages сообщения об ошибках
   * $messages[form_id][field_name]=> error_message
   */
  private $messages = array();
  private $errorTxt;        //Текст последней ошибки
  private $formFields;      //поля формы
  /**
   *
   * @var bool указывает что форма не прошла валидацию
   */
  private $hasError = FALSE;
          
  function __construct(array $rulesConf, array $form =array()){
    
    $this->checkFormid($form);
    //$this->rules = $rulesConf;    
    foreach ($rulesConf as $formID => $fields){
      foreach ($fields as $name => $rule){
        foreach($rule as $rulename => $param){
          if(is_int($rulename)){
            $rulename = $param;
            unset($param);
          }
          $ruleStr = 'rule_'.$rulename;
          $this->rules[$formID][$name][] = new $ruleStr($param);//создает объект rule
        }
        
        
      }
    }
  }
  
  
  private function checkFormid($form){
    if(!isset($form['formid']))
      {$form['formid'] = 0;} //если форма без идентификатора, берем стандартные правила
    $this->formFields = $form; //
  }

  /**
   * 
   * @return string текст последней ошибки
   */
  public function get_error(){
    return $this->errorTxt;
  }
  /**
   * 
   * @return array все ошибки
   */
  public function get_errors(){
    return $this->messages;
  }
  
  /**
   * В функцию передается массив с полями формы.
   * Если функция вызывается без параметров,
   * Анализируется форма переданная в конструктор
   * @return bool Результат валидации
   */
  public function validate($form = NULL){
    if (is_array($form)){
      $this->checkFormid($form);
    }
    $form = $this->formFields;
    /* запоминаем ID формы */
    $id = $form['formid'];
    
    //набор правил для формы
    $formRules = $this->rules[$id];
    
    foreach ($formRules as $fieldName => $rules){
      
      foreach($rules as $rule){
        /*для каждого правила выполняем метод check*/
        $value = $form[$fieldName];
        $result = $rule->check($value);
        if(!$result){
          $this->errorTxt = $rule->getError();
          $this->messages[$id][$fieldName] = $this->errorTxt;
          $this->hasError = TRUE;
        }
      }
    }
    return !$this->hasError;
  }

}


