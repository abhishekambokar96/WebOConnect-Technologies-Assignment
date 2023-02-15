$(document).ready(function () { 
  $(document).on("submit","form",function(e){
    formname = $(this).attr("name");
    var dataobj=document.getElementById(formname);
    skip="true";
    skip=dataobj.getAttribute("data-skip");    
    validate_method="";
    v_status=true;
    validate_method=dataobj.getAttribute("data-validate_method");         
    if( validate_method!=null  ){
      if(validate_method!=""){   
        v_status=window[validate_method]();
      }      
    }
    if(skip=='true' || skip==null){         
      return;  
    } 
    e.preventDefault();
    fd = new FormData(this);  
    page = $(this).attr('action');    
    preventAjax="1";   
    if(v_status){
      callAjax(fd,page);  
    }
  });
});
function addLoader(){ 

  $('body').append('<div style="display:grid;" class="spincontainer"><div style="height: 65px;" class="spinspinflex"><div class="spinloader"></div></div><div class="spinload-text">Please be patient this will take quiet a few minutes...</div></div>');
}
function removeLoader(){ 
 $('.spincontainer').remove();   
}
function createOption(data,firstOpt,value){ 
  var setselected="";
  var options="";
  var options =options + "<option value='' "+setselected+">"+firstOpt+"</option>";  
  if(data.id==value){
    setselected="selected";
  }
  $.each(data, function(i, obj) {
    options += "<option value='"+obj.id+"' "+setselected+">"+obj.name+"</option>";
  });
  return options;
}
function createOptionOnChange(data,firstOpt,selected_id){
  var setselected="";
  var options="";   
  //console.log(data);
  var options =options + "<option value='' "+setselected+">"+firstOpt+"</option>";
  $.each(data, function(i, obj) {   
    if(obj.cmp_id==selected_id){
      options += "<option value='"+obj.id+"' "+setselected+">"+obj.name+"</option>";
    }
  });
  return options;
}
function callAjax(fd,pg)
{  
  isSubmitting = true;
  $.ajax({
    url: pg,
    type: "POST",
    dataType: "json",
    data:  fd,
    contentType:false,
    processData:false, 
    beforeSend: function() 
    {
      $("button[type='submit']").attr('disabled','disabled');    
      $("input[type='submit']").attr('disabled','disabled');
      addLoader(); 
    },
    success: function(json) 
    {
      if(json.res_code==1){
        callfunction(json);
      }    
    },
    /*error: function(e) 
    {
      console.log(e);
      alert('ajax call failed'+e);
    },*/
    complete: function(json) 
    {   
      /*responseText=  JSON.parse(json.responseText);
      console.log(responseText.res_code);
      if(responseText.res_code==2){

         callfunction(responseText);
       }*/
       removeLoader();
       $("button[type='submit']").removeAttr('disabled');
       $("input[type='submit']").removeAttr('disabled');
     }
   });
}

function customAjax(pg,fd,res=false){
  if(fd!=""){
    fd=JSON.parse(fd);
  } 
  var ajaxreturn = $.ajax({
    url: pg,
    type: "POST",
    dataType: "json",
    data: fd,
   /* contentType:false,
    processData:false, */
    success: function(json) 
    {         
      if(json.res_code==1 && !res){
        callfunction(json);
      }    
    }
  });
  if(res) { return ajaxreturn; }
}
function callfunction(data){  
  window[data.method](data);  
} 


function empty(data)
{
  if(typeof(data) == 'number' || typeof(data) == 'boolean')
  { 
    return false; 
  }
  if(typeof(data) == 'undefined' || data === null)
  {
    return true; 
  }
  if(typeof(data.length) != 'undefined')
  {
    return data.length == 0;
  }
  var count = 0;
  for(var i in data)
  {
    if(data.hasOwnProperty(i))
    {
      count ++;
    }
  }
  return count == 0;
}
$.validator.addMethod("alpha", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z]+$/);
});
$.validator.addMethod("alphaSpace", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
});
jQuery.validator.addMethod("alphanumeric", function(value, element) {
  return this.optional(element) || /^[a-zA-Z0-9 .'\n]+$/.test(value);
}); 
/*
 *
 *  validate youtube url
 *
 */
 function validateYouTubeUrl(element) {    
  var url = $(element).val();    
  if(url!=""){
    if (url != undefined || url != '') {        
      var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
      var match = url.match(regExp);
      if (match && match[2].length == 11) {
        $("#video_url").parent().removeClass("has-error").addClass("has-success");
        $("#video_url").siblings("label").addClass("show");            
        document.getElementById('video_url_span').innerHTML="";
        $("#video_url").val('https://www.youtube.com/embed/' + match[2] + '?autoplay=1&enablejsapi=1');

      } else {            
        $("#video_url").parent().removeClass("has-success").addClass("has-error");
        $("#video_url").siblings("label").addClass("hide");
        $("#video_url_span").val('video_url_span');
        document.getElementById('video_url_span').innerHTML="<b>Invalid <i class='fa fa-youtube'></i> Youtube link</b>";            
      }
    }
  }

}

function RegSuccMsg(data){
  // swal(
  //   "Information",data.message,"success").then(function () {
  //   location.reload(true);
  // });

  swal({
    title: "Information",
    text: data.message,
    type: "success",
    allowOutsideClick:false,
    allowEscapeKey:false
  }).then(function(){
    location.reload(true);
  })
}
function RegSuccMsgNoReload(data){
  swal("Information",data.message,"success").then(function () {
  });
}
function loginErrorMsg(data){
  swal("Information",data.message,"error");
}
function  redirect(data){
  window.location=data.link;
}

function redirect_with_msg(data){
  swal({
    title: data.title,
    text: data.message,
    type: data.type,
    allowOutsideClick:false,
    allowEscapeKey:false
  }).then(function(){
    window.location=data.link;
  })
}
function RegErrorMsg(data){
  swal("Error",data.message,"error");
}

function reload(){
  location.reload(true);
}

$(document).ready(function () { 
  /* Jquery  Custom Validation*/
  $.validator.addMethod("alpha", function(value, element) {
    return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
  });
  $(document).on('focus', 'input[type=number]', function (e) {
    $(this).on('mousewheel.disableScroll', function (e) {
      e.preventDefault()
    })
  })
  $(document).on('blur', 'input[type=number]', function (e) {
    $(this).off('mousewheel.disableScroll')
  })
  /* End  Jquery  Custom Validation*/
});

function getYears(monthCount) {
  function getPlural(number, word) {
    return number === 1 && word.one || word.other;
  }

  var months = { one: 'month', other: 'months' },
  years = { one: 'year', other: 'years' },
  m = monthCount % 12,
  y = Math.floor(monthCount / 12),
  result = [];

  y && result.push(y + ' ' + getPlural(y, years));
  m && result.push(m + ' ' + getPlural(m, months));
  return result.join(' and ');
}
function monthDiff(d1, d2) {   
  var months;
  months = (d2.getFullYear() - d1.getFullYear()) * 12;
  months -= d1.getMonth() + 1;

  months += d2.getMonth();
  return months <= 0 ? 0 : months;
}

function validate_due_form(){  
  $('.styled').each(function () {
    $(this).rules("add", {
      required: true
    });
  });
  $('.remark').each(function () {
    $(this).rules("add", {
      required: true
    });
  });
}

function is_valid_date(d){
  if ( Object.prototype.toString.call(d) === "[object Date]" ) {    
    if ( isNaN( d.getTime() ) ) {  
      return false;
    }
    else {
      return true;
    }
  }
  else {
    return true;
  }
}

function format_date(date){
 var d = new Date(date),
 month = '' + (d.getMonth()+1),
 day = '' + (d.getDate()),
 year = d.getFullYear();
 return [year, month, day].join(',');
}

var daysofweek = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
var month =['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

/*function clock(){
    // setting up my variables
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    var day = h<=11 ? 'AM': 'PM';
    //console.log(h);
    var h_half= h>12 ? h%12 : h;
    var daytoday = today.getDay();
    var date = today.getDate();
    var mon = today.getMonth();
    var year = today.getFullYear();    
    // adding leading zeros to them
    h_half = h_half<10? '0'+h_half: h_half;
    m = m<10? '0'+m: m;
    s = s<10? '0'+s: s;

    // writing it down in the document
    document.getElementById('hours').innerHTML = h_half;
    document.getElementById('min').innerHTML = m;
    document.getElementById('sec').innerHTML = s;
    document.getElementById('time').innerHTML = day;
    // document.getElementById(''+daysofweek[daytoday]+'').style.color = '#ff9800';    
    document.getElementById('day').innerHTML = date;
    document.getElementById('month').innerHTML = month[mon];
    document.getElementById('year').innerHTML = year;

  }*/

// var inter = setInterval(clock,400);

function custSuccMsg(data){
  swal({
    text: data.message,
    type: "success"
  }).then(function(){
    location.reload(true);
  });
}

function selectOption(data, dom){
  $(dom).val(data).trigger('change');
}

function success_modal_close(data) {
  swal({
    title: "Success",
    text: data.message,
    type: "success",
    confirmButtonClass: "btn-primary",
    confirmButtonText: "OK"
  }).then(function() {
    $('#'+data.modal).modal('hide');
  });
}
