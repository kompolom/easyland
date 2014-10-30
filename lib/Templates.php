<?php
/**
 * @version 0.1.0
 */
class Template{
  public $templates = array();
  static $MD = '_';
  static $ED = '__';

  
  private static $instance;
  
  protected function __construct() {
    $this->_template();
  }
  
  public static function get(){
    if (empty(self::$instance)){
      self::$instance = new Template();
    }
    return self::$instance;
  }
  
  /**
   * 
   * @param type $ctx
   * $ctx = array(
      '_isBem'=> false,
      '_isBlock'=>false,
      '_isElem' =>false,
      '_parent'=>false,      
      '_position'=> 0,
      '_mode' => NULL,
      '_modName'=> NULL,
      '_modVal' => NULL,
      '_src' => NULL,
      
      'block'=> false,
      'elem' => false,
      'tag'=>null,
      'mods'=>array(),// [modName]=>modVal
      'cls' =>array(),
      'attrs'=>array(), //[attr]=> value
      'js'=>false,
      'content'=>''
  ); 
   * @return type
   */
  function apply($ctx){
    return  $this->get_template($ctx['block'],$ctx['elem'],$ctx['_modName'], $ctx['_modVal']);
//    $this->ctx = $ctx;
//    $this->ctx = $this->mod_def($this->ctx);   
//    return $this->ctx;
//    //return BEM::tpl($this->template);
  }
  
 
  
  function get_template($b, $e, $mName, $mVal){
    $tplName = $b;
    if($e){      
      $tplName = $b.self::$ED. $e;
    }
    if($mName){
      $tplName .= self::$MD. $mName;
    }
    if(!is_bool($mVal)){
      $tplName .= self::$MD. $mVal;
    }
    
   
      if(!array_key_exists($tplName, $this->templates)) 
        {return array();}     
      return $this->templates[$tplName];
  }
  
  /*template functions*/
  function _template(){
    $this->templates['page'] = array(
      'tag'=>'html', //мода tag
      'attrs'=>function($ctx){return false;},
      'content'=> function($ctx){       
        return array(
            array('elem'=>'head'),
            array('elem'=>'body',  
                'content'=>$ctx['content']
                  ),                   
            );
      }
    );
    $this->templates['page__head'] = array(
        'tag' => 'head',
        'attrs'=>function($ctx){return false;}
        );
    $this->templates['page__body'] = array(
        'tag'=>'body',
        'bem'=>'page',
        'content'=> function($ctx){
          return array(
              'block'=>'page',
              'elem'=>'wrapper',
              'content'=>$ctx['content']
          );
        } 
    );
        
    $this->templates['section'] =array(
        "tag"=>"section",
        "content"=> function($ctx){
            return array(
                "block"=>"container",                
                "content"=>$ctx['content']
            );
        }
    );

    
    $this->templates['input'] = array(
       'tag' => 'span'
    );

    $this->templates['input__box'] = array(
       'tag' => 'span'
    );
    $this->templates['input__control'] = array(
       'tag' => 'input'
    );

    $this->templates['input_type_separate'] = array(
       'tag' => 'span'
    );
    
    $this->templates['button'] = array(
        "tag"=>"button",
        "mix"=>array('block'=>'button','elem'=>'control'),
        "attrs"=> function($ctx){
          $c = Arr::merge($ctx['attrs'],array( 
           "role" => 'button',
           "type" => Arr::get($ctx['_src'],'type','button'),           
        ));
        return $c;
        },
        "content"=> function($ctx){
          return array(
              "tag"=>"span",
              "elem"=>"text",
              "content"=>$ctx['_src']['text']
          );
        }
    );
    
    $this->templates['list'] = array(
        "tag"=>"ul"
    );
    
    $this->templates['list_ordered'] = array(
        "tag"=>"ol"
    );
    
    $this->templates['list__item'] = array(
        "tag"=>"li"
    );

    $this->templates['heading'] = array(
        "tag"=>function($ctx){
          return 'h'.$ctx['mods']['l'];
        }
    );
    
    $this->templates['image']= array(
      "tag" => "img",
      "attrs"=>function($ctx){
        $src = $ctx['_src']['url'];
        $c = Arr::merge($ctx['attrs'],array( 
           "src" => $src,
           "alt" => Arr::get($ctx['_src'],'alt'),
           "title"=>Arr::get($ctx['_src'],'title'),
        ));
        return $c;
      }
    );
    
   
    
  }//template
}
