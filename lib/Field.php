<?php

/**
 * @package Easyland
 * @version 0.1
 * @uses BEM.php 
 */
class Field{
  protected $_src;
  public $tpl = array(
      "block"=>"input",
      "mods"=>array(),
      "content"=>array(
          "elem"=>"box",
          "content"=>array(
              "elem"=>"control",
              "attrs"=>array()
          )
      )
  );
          
  function __construct($id, $params = array()) {
    global $fieldsrc;
    $this->_src = Arr::merge($fieldsrc[0],$fieldsrc[$id]);
    $this->tpl['js']= Arr::get($this->_src,'params',true);
    $this->tpl['content']['content']['attrs']['placeholder']= Arr::get($this->_src,'placeholder');
    $this->tpl['content']['content']['attrs']['name']= Arr::get($this->_src,'name');
    $this->tpl['content']['content']['attrs']['value']= Arr::get($this->_src,'value');
    $this->tpl['content']['content']['attrs']['required']= Arr::get($this->_src,'required');
    $this->tpl['content']['content']['attrs']['type']= Arr::get($this->_src,'type');
    $this->tpl['mods']['size'] = Arr::get($this->_src,'size','x');
    $this->tpl['mods']['type'] = Arr::get($this->_src,'type');
    $this->tpl['mods']['name'] = Arr::get($this->_src,'name');
    $this->tpl['mods']['theme'] = Arr::get($this->_src,'theme','normal');
    $this->tpl = Arr::merge($this->tpl, $params);
  }
    
  public function getInstance($id){
    return new Field($id, $params = array());
  }
  public function render(){
    echo BEMHTML::apply($this->tpl);
  }
  
  function get_name(){
    return $this->_src['name'];
  }
  
  public function get_fieldname(){
    return Arr::get($this->_src,'fieldname', $this->_src['name']);
  }
}
