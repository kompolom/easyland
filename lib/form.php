<?php

/*
 * получает и хранит данные введенные в форму
 */
abstract class frontData{
  protected $fields =array();  
  protected $source=array();
  protected $writer;
  protected $translator;
  
          function __construct(array $names){                
        foreach ($names as $name => $desc){
            if(is_int($name)){
                $this->fields[$desc] = $this->source[$desc];
            }else{
                $this->fields[$name] = $this->source[$name];                
            }        
        }//foreach
        $this->translator = new dataTranslator($names);
    }//construct
    
    static function getInstance(array $names, $method=NULL){
      switch ($method){
        case 'post':
          return new postFrontData($names);       
        case 'get':
          return new getFrontData($names);          
        default :
          return new getFrontData($names);
      }
    }
//   возвращаяет поля с непустыми значениями
    function get_fields(){
        foreach ($this->fields as $index => $value){
            if(!empty($this->fields[$index])){
                $translation = $this->translate($index);
                $result[$translation]=$value;                              
            }
        }
        return $result;
    }
    /**
     * 
     * @return array All form fields
     */
    function get_raw_fields(){
      return $this->fields;
    }
    
    public function get_field($index){
        return $this->fields[$index];
    }
    /*удаляет поле */
    public function remove_field($index){
        if(array_key_exists($index, $this->fields)){
            unset($this->fields[$index]);
            return true;
        }else{
            return false;
        }
    }
    public function get_formid(){
      return $this->fields['formid'];
    }
    
    function translate($fieldname){ 
      return $this->translator->translate_field($fieldname);               
    }


    /*изменяет значение поля
     *  $index - ключ поля
     *  $value - новое значение
     */
    function update_field($index,$value){
      if(key_exists($index, $this->fields)){
      $this->fields[$index] = $value;
      return true;
      }  else {
        return false;  
      }
    }

    /* немного "магии"... Если метода нет в данном классе, создается экземпляр
     * класса frontDataWriter и пытаемся вызвать метод в нем.
     */
    function __call($methodname, $arg) {
      $this->writer = new frontDataWriter($this);
      if (method_exists($this->writer, $methodname)){
        return $this->writer->$methodname($arg);
      }
    }//добавить Exeption;
}

class postFrontData extends frontData{
  
  function __construct(array $names) {
    
      $this->source = $_POST;   
    parent::__construct($names);
  }  
}

class getFrontData extends frontData{
  
 function __construct(array $names){
   $this->source = $_GET;
   parent::__construct($names);
 } 
}





/*
 * отвечает за вывод данных. ожидает объект класса frontData
 */
class frontDataWriter{
    private $data;
    
    function __construct(frontData $data){
      $this->data = $data->get_fields();  
    }
    function write_list(){
        $text = '<ul>';
        foreach ($this->data as $name => $value){
            $text=$text.'<li>'.$name.' : <b>'.$value.'</b></li>';
        }
        $text = $text.'</ul>';
        return $text;
    }
    function write_table(){
        $text = '<table  width=\'500\' cellspacing=\'0\' cellpadding=\'5\' border=\'1\' bordercolor=\'1\' style=\'border:solid 1px #000;border-collapse:collapse;\'>';
        foreach ($this->data as $name => $value){
            $text=$text.'<tr><td  bgcolor=#efeeee style="background:#efeeee">'.$name.' </td><td><b>'.$value.'</b></td></tr>';
        }
        $text = $text.'</table>';
        return $text;
    }
    function write_plain($SHOW_NAMES = FALSE){
      $text = '';
      foreach ($this->data as $name => $value){
            $text.= $SHOW_NAMES? $name.":".$value : $value;
            $text.="\n";
        }
     return $text;
    }
}
/*
 * class dataTranslator
 * перевод полей
 */
class dataTranslator{
  
  protected $fieldsNames = array();
  
  
  function __construct($translations) {
    if(is_array($translations)){
      $this->fieldsNames = array_merge($this->fieldsNames, $translations);
    }else{
      array_push($this->fieldsNames, $translations);
    }    
  }//cconstruct
  
  function translate_field($fieldname){
    if(isset($this->fieldsNames[$fieldname])){
      $trans = $this->fieldsNames[$fieldname];
      return $trans;
    }else{
      return $fieldname;
    }
  }
  
}



?>
