$(function(){"use strict";$("#phpcontactform").submit(function(e){e.preventDefault()}).validate({rules:{first_name:"required",last_name:"required",email:{required:!0,email:!0},phone:{required:!0,number:!0},message:"required"},messages:{first_name:"Your first name please",last_name:"Your last name please",email:"We need your email address",phone:"Please enter your phone number",message:"Please enter your message"},submitHandler:function(e){$("#js-contact-btn").attr("disabled",!0);var a=$("#phpcontactform").data("redirect"),s=!1;("none"==a||""==a||null==a)&&(s=!0),$("#js-contact-result").html('<p class="help-block">Please wait...</p>');var t=$("#js-contact-result").data("success-msg"),r=$("#js-contact-result").data("error-msg"),l=$(e).serialize();return $.ajax({type:"POST",data:l,url:"php/contact.php",cache:!1,success:function(e){$(".form-group").removeClass("has-success"),"success"==e?s?$("#js-contact-result").fadeIn("slow").html('<div class="alert alert-success top-space">'+t+"</div>").delay(3e3).fadeOut("slow"):window.location.href=a:$("#js-contact-result").fadeIn("slow").html('<div class="alert alert-danger top-space">'+r+"</div>").delay(3e3).fadeOut("slow"),$("#js-contact-btn").attr("disabled",!1)}}),!1}})});