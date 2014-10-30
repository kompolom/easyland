<?php
require 'BEMHTML.php';
require 'Templates.php';
/**
 * Html snippets
 * @package Easyland
 * @version 0.1
 */
class HTML{

  /**
   * Html button
   * @param string text Button`s text
   * @param string size mod
   * @param string theme mod
   * @param bool submit 
   * @param array mix Mix classes
   * @param array cls Additoin classes
   */
  static function btn($text='press me', $size="x", $theme="normal", $submit=false, $mix=array(), $cls=array())
  {
    $b = array(
      'block'=>'button',
      'js'=>true,
      'mix'=>$mix,
      'cls'=>$cls,
      'mods'=>array( 'theme'=>$theme, 'size'=> $size),
      'text'=>$text
    );
    if($submit){
      $b['type'] = 'submit';
      $b['mix'][] = array('block'=>'form','elem'=>'submit');
    }
    if(!empty($mix))
    {
      $b['mix'] = array_merge($b['mix'],$mix);
    }
    echo BEMHTML::apply($b);
  }

  static function heading($text='', $level=2, $base='', $mix=array(), $cls=array())
  {
    $b = array(
      'block'=>'heading',
      'mods'=>array( 'l'=>$level),
      'content'=>$text
    );
    if(!empty($base))
    {
      $b['mix'] = array_merge($b['mix'],array('block'=>$base,'elem'=>'heading'));
    }
    if(!empty($mix))
    {
      $b['mix'] = array_merge($b['mix'],$mix);
    }
    echo BEMHTML::apply($b);
    
  }

  static function select($text,$name, $items=array(),$size="sx",$theme="islands")
  {
    $selectOpts = array('select'=>array(
      'name'=>$name,
      //'optionsMaxHeight'=>100,
      'live'=>false,
      'text'=>$text,
    ));
?>
  <span class="select_size_<?php echo $size; ?>  select select_mode_radio select_theme_<?php echo $theme ?> i-bem" data-bem="<?php echo BEM::jsattr($selectOpts); ?>">
    <input class="select__control" name="<?php echo $name ?>" type="hidden" />
    <button class="button button_size_auto button_theme_select button_checked button__control select__button i-bem" data-bem="{&quot;button&quot;:{}}" role="button" type="button"><span class="button__text"><?php echo $text; ?></span><i class="icon select__tick"></i>
    </button>
    <div class="popup popup_autoclosable popup_target_anchor popup_theme_<?php echo $theme ?> i-bem" data-bem="<?php echo BEM::jsattr(array("popup"=>array('directions'=>array('bottom-left','bottom-right','top-left','top-right')))) ?>">
      <div class="menu menu_size_x menu_theme_<?php echo $theme ?> menu_mode_radio menu__control select__menu i-bem" data-bem="<?php echo BEM::jsattr(array('menu'=>true)); ?>" role="menu">
      <?php foreach($items as $name => $val): ?>
      <div class="menu-item menu-item_theme_<?php echo $theme ?> i-bem" data-bem="<?php echo BEM::jsattr(array('menu-item'=>array('val'=>$val))); ?>" role="menuitem"><?php echo $name ?></div>
      <?php endforeach; ?>
      </div><!--menu-->
    </div><!--popup-->
    </span><!--select-->
<?php 
  }

  static function checkbox($text, $name, $val, $size="x")
  {
?>
    <label class="checkbox checkbox_theme_normal checkbox_size_<?php echo $size ?> i-bem" data-bem="{&quot;checkbox&quot;:{}}">
    <?php echo $text; ?>
      <span class="checkbox__box">
        <input class="checkbox__control" type="checkbox" autocomplete="off" name="<?php echo $name ?>" value="<?php echo $val ?>">
      </span>
    </label>
<?php
  }
  function input_tel()
  {
    ?>
                <span class="input_type_separate separate input_size_x  input i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>array('name'=>'phone'))) ?>">
                  <input type="hidden" class="separate__control" name="phone" value="" />
                  <label class="input__label">Ваш телефон</label>
                  <span class="input__wrap">
                    <span class="input input_type_tel input_size_xxs input_theme_normal i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>array('mask'=>'+7'))); ?>">
                      <span class="input__box">
                        <input class="input__control separate__input" required  maxlength="3" value="+7" />
                      </span>
                    </span><!--input-->
                    <span class="input input_type_tel input_size_xs input_theme_normal  i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>array('mask'=>'(999)'))); ?>">
                      <span class="input__box">
                        <input class="input__control separate__input" required   placeholder="(   )" />
                      </span>
                    </span><!--input-->
                    <span class="input input_type_tel input_size_s input_theme_normal  i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>array('mask'=>'999-99-99'))); ?>">
                      <span class="input__box">
                        <input class="input__control separate__input" required   placeholder="123-45-67" />
                      </span>
                    </span><!--input-->
                  </span><!--input__wrap-->
                </span><!--input-->
<?php
  }
  function counting_form()
  {
?>
          <?php $f=Form::get(3); $f->start(); ?>
              <div class="form__row">
                <span class="input input_type_text input_theme_normal input_size_sx"><span class="input__box"><input class="input__control" type="text" name="marka" placeholder="Марка" /></span></span><!--input-->
                <span class="input input_type_text input_theme_normal input_size_sx"><span class="input__box"><input class="input__control" type="text" name="model" placeholder="Модель" /></span></span><!--input-->
                <span class="input input_type_date input_theme_normal input_size_sx i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>true)); ?>"><span class="input__box"><input class="input__control" type="text" name="year" placeholder="Год выпуска" /></span><span class="input__picker"></span></span><!--input-->
              </div><!--form-row-->
              <div class="form__row">
                <?php HTML::select('Коробка передач','transmission', array('Автоматическая'=>'Автоматическаая','Вариаторная'=>'Вариаторная','Роботизированная'=>'Роботизироавнная','Механическая'=>'Механическая',)) ?>
                <?php HTML::select('Двигатель','engine', array('Бензиновый'=>'Бензиновый','Дизельный'=>'Дизельный')) ?>
                <?php HTML::select('Cостояние автомобиля','state', array('Отличное'=>'Отличное','Хорошее'=>'Хорошее','Среднее'=>'Среднее','Плохое'=>'Плохое','Битый'=>'Битый автомобиль')) ?>
              </div><!--form-row-->
              <div class="form__row">
                <?php HTML::select('Отопление\охлаждение','cooler', array('Отсутствует'=>'Отсутствует','Кондиционер'=>'Кондиционер','климат-контроль'=>'Климат-контроль'),'l') ?>
                <?php HTML::select('Диски','discs', array('Литые'=>'Литые','Штамповка'=>'Штамповка'),'l') ?>
              </div><!--form-row-->
              <div class="form__row">
                <?php HTML::checkbox('Был в ДТП', 'dtp','Да','x'); ?>
                <?php HTML::checkbox('Есть крашенные детали', 'colored','Да','x'); ?>
                <?php HTML::checkbox('Противотуманные фары', 'protivotuman','Да','x'); ?>
              </div>
              <div class="form__row">
              <span class="input input_theme_normal input_type_textarea input_size_bl">
                <span class="input__box">
                 <textarea name="msg" rows="5" placeholder="Дополнительно об автомобиле" class="input__control"></textarea> 
                </span>
              </span>
              </div>
              <div class="form__row">
              <div class="input-group input-group_inline" >
               <span class="input input_theme_normal input_size_x i-bem" data-bem="<?php echo BEM::jsattr(array('input'=>true)) ?>">
                  <label class="input__label"> Ваше имя</label>
                  <span class="input__box">
                    <input class="input__control" type="text" required name="name" placeholder="Введите ваше имя" />
                  </span>
                </span><!--input-->
              </div><!--input-group-->

              <div class="input-group input-group_inline" >
                <?php HTML::input_tel(); ?>
              </div><!--input-group-->
              </div>
              <button type="submit" class="button button__control button_centred button_size_m form__submit button_theme_normal button_count i-bem">
                <span class="button__text">Рассчитать стоимость</span>
              </button>
            </form>
<?php
  }
  static function metrika($id)
  {
    ?>
    <!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.counter = new Ya.Metrika({id:<?php echo $id; ?>,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/<?php echo $id; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php 
  }
}
