<?php
/**
 * @package Easyland
 * @version 0.1
 *
 */

class BEM {
  /*Параметры блока*/
  protected $tag = 'div';
  protected $content = '';
  protected $js = FALSE;
  protected $mix = array();
  protected $attrs = array();
  protected $cls = '';
  protected $mods = array();
  
  /*sets*/
  private $isChild = FALSE;
  private $noClose = FALSE;
  private $isBEM = FALSE; //БЭМ сущность, или массив
  private $classes = array(); //Все классы HTML ноды
  private $name = ''; //Имя блока
  protected $parentBlock = NULL;//Родительский блок
  private $jsParams = array(); //js параметры всех блоков
  private static $jsAttr = 'data-bem';
  static $blockparams = array('tag','js','mix','mods','attrs','cls');//список параметров блока которые можено перезаписать
  private static $nonClosingTags = array(
      'input',
      'img',
      'link',
      'meta',
      'param',
      'source',
      'area',
      'base',
      'param',
      'command',
      'col',
      'embed',
      'br',
      'hr'
  );
  private static $needID = array('input','textatra');


  static $ED = '__'; //element delimeter
  static $MD = '_' ;  //modifier delimeter
          
  function __construct($block,$parentBlock = NULL){
    
    /*Есть ли родительский блок */
    if(!is_null($parentBlock)){
      $this->parentBlock = $parentBlock;
      $this->isChild = TRUE;
    }
    
    /*Проверка, что передано БЭМ сущность или обычный массив*/
    $this->isBEM = self::isBEMobject($block);
    if(!$this->isBEM){
      $this->name = $this->parentBlock;
      $this->content = $this->get_content($block);
    }
    
    $this->name = Arr::get($block,'block');
    if(!empty($this->name)){
      $this->classes[] = $this->name;
    }
    /*Перезаписываем дефолтные значения*/
    foreach(self::$blockparams as $param){
      if(array_key_exists($param, $block)){
        $this->$param = $block[$param];
      }
    }
    
    /* добавление элемента */
    if(array_key_exists('elem', $block)){
      $blockName = $this->isChild ? $this->parentBlock : $this->name;
      $this->name = $blockName;
      $this->classes[] = self::elem($blockName, $block['elem']);
    }
    
    
    /*Установка модификаторов*/
    if(!empty($this->mods)){
      $this->classes = array_merge($this->classes, self::mod($this->name,$this->mods));
    }
    /*установка миксов*/
    if(!empty($this->mix)){
      $bem = self::isBEMobject($this->mix);
      if($bem){
        $this->mix($this->mix);
      }else{
        foreach ($this->mix as $mix){
          $this->mix($mix);
        }
      }
    }
    /*Добавление Js*/
    if($this->js){
      $this->classes[] = 'i-bem';
      $this->jsParams[$this->name] = $this->js;
    }
    
    $this->noClose = self::isNoClosing($this->tag);
    
    /*content*/
    if(array_key_exists('content', $block)){
      $this->content = is_array($block['content']) ? $this->get_content($block['content']):$block['content'];
    }
    //Добавление не БЭМ классов
    $this->classes[] = $this->cls;
  }//construct
  static function tpl($template){
    $bem = new BEM($template);
    return $bem->apply();    
  }
  protected function apply_recursive($ctx,$name=NULL){
    $bem = new BEM($ctx,$name);
    return $bem->apply();
  }
  /**
   * Рендеринг блока
   * @return string HTML
   */
  public function apply(){
    $this->attrs['class'] = $this->classes();
    if($this->js){
      $this->attrs[self::$jsAttr] = self::jsAttr($this->jsParams);
    }
    
    
    $openTag = $this->noClose ? "":">";
    $closeTag = $this->noClose? '/>': "</$this->tag>";
    
    $str = $this->isBEM ? "<".$this->tag.$this->get_attrs().$openTag.$this->content.$closeTag : $this->content;
    return $str;
  }
  
  static function isNoClosing($tag){
    return in_array($tag, self::$nonClosingTags);
  }
  
  static function isBEMobject($content){
    return (array_key_exists('block', $content)) or (array_key_exists('elem', $content));
  }
  
  protected function get_content($content) {
    if(is_array($content)){
      if(!$this->isBEM){
        $str = '';
        foreach ($content as $item){
          $str .= $this->apply_recursive($item,  $this->name);
        }
        return $str;
      }else{
        return $this->apply_recursive($content, $this->name);
      }
    }
  }


  static function mod($block,$mods){
    $classes = array();
    foreach ($mods as $modName => $modVal){
      $val = !is_bool($modVal) ? self::$MD.$modVal : '';
      $classes[] = $block.self::$MD.$modName.$val;
    }
    return $classes;
  }
  /**
   * собирает все классы блока в строку
   * 
   * @return string
   */
  protected function classes(){
    $str = implode(' ', $this->classes);
    return $str;
  }
  
  /*Возвращает класс элемента*/
  static function elem($block,$elem){    
    return  $block.self::$ED.$elem;
  }
  
  /**
   * 
   * @param array $params - хеш параметров блока
   * @return string - сформированный json 
   */
  static function jsattr(array $params,$escape=true){
    foreach ($params as $blockName => $js){
      if (is_bool($js)){
        $params[$blockName] = new ArrayObject();
      }
    } 
    $str = json_encode($params, 256);
    $str = $escape ? str_replace('"', '&quot;', $str) : $str;
    return $str;
  }
  
  /*Миксует блоки*/
  protected function mix($mixin){
    $mixBlock = $this->name;
          
      if(array_key_exists('block', $mixin)){
        $mixBlock = $mixin['block'];        
      }
      $mixCls = $mixBlock;
      if(array_key_exists('elem', $mixin)){
        $mixCls = self::elem($mixBlock,$mixin['elem']);
      }
      if(array_key_exists('js', $mixin)){
        $this->jsParams[$mixBlock] = $mixin['js'] ;
      }
      $this->classes[] = $mixCls;
  }
  
  function get_attrs(){
    $str = '';
    foreach ($this->attrs as $name => $value){
      $str .= " ".$name."=\"".$value."\" ";
    }
    return $str;
  }
  
  static function id(){
    return uniqid('uniq');
  }
  
}

