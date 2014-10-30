<?php

/**
 * @package Easyland
 * @version 0.1
 */
class Form{
  protected $fields;
  protected $action;
  protected $mods;
  protected $params;
  protected $name;
  private $_src;
  
  function __construct($id=0) {
    global $formsrc;
    $this->_src = Arr::merge($formsrc[0],$formsrc[$id]);
    $this->id = $id;
    $this->method = $this->_src['method'];
    $this->action = $this->_src['target'];
    $this->submit = $this->_src['submit'];
    $this->name   = $this->_src['name'];
    $this->params = array('form'=>  $this->_src['params']);
    foreach ($this->_src['fields'] as $id){
      $this->fields[] = Field::getInstance($id);
    }
  }
  public static function getInstance($id){
    return new Form($id);
  }
  public function get($id){
    return self::getInstance($id);
  }

  public function start(){    
    ?>
<form id="<?php echo $this->name.'-'.$this->id ?>" class="form form_async form_size_<?php echo Arr::get($this->_src,'size','x') ?> form_theme_<?php echo Arr::get($this->_src,'theme','normal') ?> i-bem" method="<?php echo $this->method ?>" action="<?php echo $this->action ?>" data-bem="<?php echo BEM::jsattr($this->params); ?>">
  <input type="hidden" name="formid" value="<?php echo $this->id ?>"/>  
<?php  
    global $utmmarks;
    statistic($utmmarks);
  }
  public function end(){
    echo "</form>";
  }
  
  public function fields(){
    foreach ($this->fields as $field){
      $field->render();
    }
  }
  public function submit($cls = ''){
    ?>
  <button type="submit" class="button button__control button_theme_normal button_size_<?php echo Arr::get($this->_src,'size','x') ?> form__submit i-bem" data-bem="<?php echo BEM::jsattr(array('button'=>true)) ?>">
    <span class="button__text"><?php echo $this->submit ?></span>
  </button>
  <?php
  }
  function id(){
    return "#".$this->name.'-'.$this->id;
  }
  function get_id(){
    return $this->id;
  }
  /**
   * Возвращает название формы
   * @return string formname
   */
  function get_name(){
    return $this->_src['formname'];
  }
  /**
   * 
   * @return object Field
   */
  function get_fields(){
    return $this->fields;
  }
}
