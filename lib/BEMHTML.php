<?php

class BEMHTML{
  
  protected $ctx = array(
      '_isBem'=> false,
      '_isBlock'=>false,
      '_isElem' =>false,
      '_parent'=>false,      
      '_position'=> 0,
      '_mode' => NULL,
      '_modName'=> false,
      '_modVal' => false,
      '_src' => NULL,
      '_buff'=>'', //Строковый буффер
      
      'block'=> false,
      'elem' => false,
      'tag'=>'',
      'mods'=>array(),// [modName]=>modVal
      'cls' =>array(),
      'mix' =>array(),
      'attrs'=>array(), //[attr]=> value
      'js'=>false,
      'content'=>''
  ); 
   static $shortTags = array(
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
      'hr',
      'style'
  );
  static $MD = '_';
  static $ED = '__';
  private $_tpl = array();
  
  function __construct($ctx,$parent=false) {
    $this->ctx['_parent'] = $parent? $parent['block'] : false;
    $this->ctx['_isBlock'] = self::is_block($ctx);
    $this->ctx['_isElem']  = self::is_elem($ctx);
    $this->ctx['_isBem'] = (($this->ctx['_isBlock']) or ($this->ctx['_isElem']));
   
    $this->ctx['_src'] = $ctx;
    if($this->ctx['_isBem']){    
      $this->for_bem($ctx);
    }    
    
  }
  /*точка входа*/
  public static function apply($ctx,$parent=false){
    $bemhtml = new BEMHTML($ctx,$parent);
    return $bemhtml->_apply();
  }
  
  function _apply(){
    
    if(!$this->ctx['_isBem']){
      $str ='';
      $this->ctx['block'] = $this->ctx['_parent']; //проброс блока в массив
    foreach ($this->ctx['_src'] as $item){
      $str .= self::apply($item,  $this->ctx);
     }
     return $str;
    }   
//    if (array_key_exists(0,$this->ctx['content'])||self::is_bem($this->ctx['content']))
//    {
//      $this->ctx['content'] = self::apply($this->ctx['content'],$this->ctx['block']);
//    }
    return $this->ctx['_buff'];//array('block'=>$this->ctx['block'],'elem'=>$this->ctx['elem'],'tag'=>$this->ctx['tag'],'buff'=>$this->ctx['_buff'],'content'=>  $this->ctx['content']);
  }
  
    /*Стандартные моды*/
  function mod_def(){
    $this->ctx['_mode'] = 'def';
    $this->ctx['_buff']='';
    if(is_callable($this->_tpl)){
     return call_user_func_array($this->_tpl, array($this->ctx));
    }   
    /*Bem*/
    $this->ctx['attrs']['class'][] = $this->mod_bem();
    
    /*Mods*/
    $this->ctx['mods'] = $this->mod_mods();
    foreach ($this->ctx['mods'] as $modName => $modVal){
      $this->ctx['_modName'] = $modName;
      $this->ctx['_modVal'] = $modVal;
      $class = self::$MD.$modName;
      $class .= !is_bool($modVal)? self::$MD.$modVal : '';
      $class = $this->ctx['_isBlock']? $this->ctx['block'].$class : $this->ctx['block'].self::$ED.$this->ctx['elem'].$class;
      $this->ctx['attrs']['class'][] = $class;
      $this->get_template();//Перевыбор шаблона
    }
    
    /*Tag*/
    $this->ctx['tag'] = $this->mod_tag($this->ctx);
    $this->ctx['_isShort'] = in_array($this->ctx['tag'], self::$shortTags);
    /*Mix*/
    $this->ctx['mix'] = $this->mod_mix();
    
    
    /*Cls*/
    $this->ctx['cls'] = $this->mod_cls();
    foreach($this->ctx['mix'] as $item){
      switch ($item) {
        case self::is_block($item):
          $this->ctx['cls'][] = $item['block'];
          break;
        
        case self::is_elem($item):
          $this->ctx['cls'][] = Arr::get($item,'block',$this->ctx['block']).self::$ED.$item['elem'];
          break;
        
        default:
          break;
      }      
    }
    /*JS*/
    $this->ctx['js'] = $this->mod_js();
    if($this->ctx['js']){
      foreach ($this->ctx['js'] as $blockName => $js){
        if (is_bool($js)){
          $this->ctx['js'][$blockName] = new ArrayObject();
        }
      } 
      $str = json_encode($this->ctx['js'], 256);
      $str = str_replace('"', '&quot;', $str);
      $this->ctx['attrs'][$this->mod_jsAttr()] = $str;
      $this->ctx['attrs']['class'][] = 'i-bem';
    }
    
    /*Content*/
    $this->ctx['content'] = $this->mod_content($this->ctx);
    
    /*Attrs*/
    $this->ctx['attrs']['class'] = array_merge($this->ctx['attrs']['class'], $this->ctx['cls']);    
    $this->ctx['attrs'] = $this->mod_attrs();
    $attrs = '';
    foreach($this->ctx['attrs'] as $attr => $values){
      $val = is_array($values)? implode(" ",$values):$values;
      $attrs .= ' '.$attr.'="'.$val.'"' ;
    }
    /*Запись строк в буфер*/
    $this->ctx['_buff'] .= "<{$this->ctx['tag']}".$attrs;
    $this->ctx['_buff'] .= !$this->ctx['_isShort']?'>':'';
    $this->ctx['_buff'] .= !is_array($this->ctx['content'])? $this->ctx['content'] : BEMHTML::apply($this->ctx['content'],$this->ctx);
    $this->ctx['_buff'] .= !$this->ctx['_isShort']? "</{$this->ctx['tag']}>" : "/>";
    return $this->ctx;
  }
  function mod_tag($ctx){
    $ctx['_mode'] = 'tag';
    if(array_key_exists('tag',$ctx['_src'])){
      return $ctx['_src']['tag'];
    }
    
    if(is_callable($this->_tpl['tag'])){
      return call_user_func_array($this->_tpl['tag'], array($ctx));
    }else{
     return Arr::get($this->_tpl, 'tag','div');
    }
    
  }
  function mod_bem(){
    $this->ctx['_mode']= 'bem';
    if(is_callable($this->_tpl['bem'])){
      return call_user_func_array($this->_tpl['bem'], array($this->ctx));
    }
    $this->ctx['bem'] = $this->ctx['_isBlock'] ? $this->ctx['block'] : Arr::get($this->ctx,'block', $this->ctx['_parent']['block']).self::$ED.$this->ctx['elem'];   
       
    return $this->ctx['bem'];
  }
  function mod_mods(){
    $this->ctx['_mode']= 'mods';
    if(is_callable($this->_tpl['mods'])){
      return call_user_func_array($this->_tpl['mods'], array($this->ctx));
    }
    if(array_key_exists('mods',$this->_tpl)){
      return Arr::merge($this->ctx['mods'], $this->_tpl['mods']);
    }   
    
    return $this->ctx['mods'];    
      
  }
  
  function mod_mix(){
    $this->ctx['_mode']= 'mix';
    if(is_callable($this->_tpl['mix'])){
      return call_user_func_array($this->_tpl['mix'], array($this->ctx));
    }
    if(is_array($this->_tpl['mix']))
    {
      array_push($this->ctx['mix'], $this->_tpl['mix']);
    }
    return $this->ctx['mix'];
    
  }
  function mod_cls(){
    $this->ctx['_mode']= 'cls';
    if(is_callable($this->_tpl['cls'])){
      return call_user_func_array($this->_tpl['cls'], array($this->ctx));
    }
    if(is_array($this->_tpl['cls'])){
      return array_push($this->ctx['cls'], $this->_tpl['cls']);
    } 
   
    return $this->ctx['cls'];
  }
  function mod_js(){
    $this->ctx['_mode']= 'js';
    if(is_callable($this->_tpl['js'])){
      return call_user_func_array($this->_tpl['js'], array($this->ctx));
    }
    $a = array();
    if($this->ctx['js']){
      $a[$this->ctx['block']] = $this->ctx['js'];
    }
    if(!empty($this->ctx['mix'])){
      foreach ($this->ctx['mix'] as $block){
        if (array_key_exists('js', $block)){
          $a[$block['block']] = $block['js'];
        }
      }
    }
    
    return $a;
  }
  function mod_jsAttr(){
    return 'data-bem';
  }
  function mod_attrs(){
    $this->ctx['_mode']= 'attrs';
    if(is_callable($this->_tpl['attrs'])){
      return call_user_func_array($this->_tpl['attrs'], array($this->ctx));
    }
    if(is_array($this->_tpl['attrs']))
    {
      array_push($this->ctx['attrs'], $this->_tpl['attrs']);
    }       
    return $this->ctx['attrs'];
  }
  function mod_content(){
    $this->ctx['_mode'] = 'content';
    if(is_callable($this->_tpl['content'])){
      return call_user_func_array($this->_tpl['content'], array($this->ctx));
    }else{
     return Arr::get($this->_tpl, 'content',$this->ctx['content']);
    }
  }  
  /*-- End Mods--*/

  /*Вспомогательные функции*/
  
  /**
   * 
   * @param array $ctx
   * @return bool 
    */
  static function is_block($ctx){
    return (array_key_exists('block', $ctx)) and (!array_key_exists('elem', $ctx));
  }
  /**
   * 
   * @param array $ctx
   * @return bool
   */
  static function is_elem($ctx){
    return array_key_exists('elem', $ctx);
  }
  static function is_bem($ctx){
    return (self::is_block($ctx)) or (self::is_elem($ctx));
  }
  
  function normalize(){
   
    $this->ctx['block'] = $this->ctx['_isBlock']? $this->ctx['_src']['block'] : $this->ctx['_parent'];
    if($this->ctx['_isElem']){      
      $this->ctx['elem'] = $this->ctx['_src']['elem'];
    }
  }
   function for_bem($ctx){
     $this->ctx['content'] = Arr::get($ctx,'content');   
     $this->ctx['mix'] = Arr::get($ctx,'mix');
     $this->ctx['mods'] = Arr::get($ctx,'mods');
     $this->ctx['js'] = Arr::get($ctx,'js',false);
     $this->normalize();
      /*Применяем шаблон*/
     $this->get_template();
     $this->mod_def();
   }
   
   function get_template()
   {
     $this->_tpl = array_merge($this->_tpl, Template::get()->apply($this->ctx));
   }
       
  
}

