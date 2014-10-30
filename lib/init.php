<?php

/**
 *  Инициализация приложения
 *  @package Easyland/core
 *  @version 0.1.0
 */
define('DS', DIRECTORY_SEPARATOR);
define('ABSPATH', '..'.DS.dirname(__FILE__));

require 'settings.php';
require 'lib/Arr.php';
require 'lib/BEM.php';
require 'lib/Form.php';
require 'lib/Field.php';
require 'lib/HTML.php';

function utm_marks(array $utm){
        foreach ($utm as $mark =>$name){
          if(array_key_exists($mark, $_GET))
          {
                echo "<input type='hidden' name='$mark' value='$_GET[$mark]' />\n";
          }
        }
  }
function statistic($utm){
  utm_marks($utm);
  $ref = Arr::get($_SERVER, 'HTTP_REFERER');
    echo "<input type=hidden name='ip' value='".$_SERVER['REMOTE_ADDR']."' />\n";
    echo "<input type=hidden name='referer' value='".$ref."' />\n";
}