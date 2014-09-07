$(document).ready(function () {
  
  $('form').submit(function(){
    send($(this));
    return false;
 }); 

  // Маска для телефона
  $('.phone').mask("+7 (999) 999-9999");

  $('.fancy-trigger, .fancybox-link').fancybox();

/**
 ajax отправка
*/    
function send(f){
  form = $(f);  
  var action = form.attr('action');
  var type = form.attr('method') ||'POST';
  var data= form.serialize();
  data +='&isAjax=1';
        $.ajax({
          url         :   action,
          type        :   type,
          data        :   data,
          beforeSend  :   function(){
              try{   
                var id = form.find('input[name=formid]').val();
                counter.reachGoal('FORM-'+id);
              }catch(e){
                console.warn(e);
              }
                       
              if (form.attr('data-answer')){               
                target = form.attr('data-answer');
                a = form.find(target);
                answ = $(a);
                answ.bind('hasError',function(){
                  $(this).empty();
                });
                
                
                
              }else{
               
              $('body').append('<div id="respond"></div>');
              answ = $('#respond');
              answ.bind('hasError',function(){
                $.fancybox.close();
              });
              $.fancybox.open(answ,{
                minWidth:'200px',minHeight:'100px',padding:0,
                afterClose:function(){
                  answ.remove();
                }
              }); 
              }//else
             answ.html("<div class='preloader'></div>");
             
          },
          success     :   function(data){                  
            var response = JSON.parse(data);
            
            if (response.errors){
              var errors = response.errors;
              answ.trigger('hasError');
              console.log(errors);
              
              for(formid in errors){
                for(field in errors[formid]){
                  var msg = errors[formid][field];
                  var $input = form.find('input[name='+field+']');
                  $input.addClass('error').after('<label class="error">'+msg+'</label>');
                  $input.one('focus',function(e){
                    $(this).next('.error').remove();                    
                  });
                }
              }
            };//error
            if (response.msg){
              
              answ.addClass('alert-success').html(response.msg);
            };
            
          },
          error       : function(){
              answ.addClass('alert-error').html("Очень жаль, но произошла ошибка.");
          }
      });               
          
  }

});
