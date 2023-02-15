var formdata,page,result,buttonpressed,i,id,fd,formname,files,isSubmitting=false;

(function($){
    $.fn.anform = function(options){
    	$('form').on('submit',function(e){
			e.preventDefault();

			formname = $(this).attr("name");	
			fd = new FormData(this);
			
			/* For form name as the POST array is send and to check isset */
			fd.append(formname,formname);
			
			valctrlbtn(formname,'checkbox');
			valctrlbtn(formname,'radio');
			
			page = $(this).attr('action');
			$.each($('form[name="'+formname+'"] input[type="file"]'),function(){
				if($(this).val()) 
					files = true;
				else
					files = false;
			});
			AjaxwithImages(fd,page,files);		
		});

    	$('form').on('click','[type="reset"]',function(e){
    		$(this).find('span.errmsgs').remove();
    		$(this).closest('form').find('span.errmsg').remove(); /* This can be improved */
		});
    };

    $.fn.pagination = function(options){
    	var pgthis = $(this);
    	var settings = $.extend({
            page: 1,
            routepath: "route/pagination_route.php",
            previous: "Previous",
            next: "Next",
            container: $(pgthis).attr("id")
        }, options );

		var paginationdata = {page_num_req:settings.page,abt_txtall_req:options.abt,cont_txtall_req:settings.container,previous_txtall_req:settings.previous,next_txtall_req:settings.next};
		var pagination = anAjax(settings.routepath,paginationdata,'html');
		pagination.success(function(msg){
			$(pgthis).html(msg);
		});
    };
}(jQuery));

function AjaxwithImages(fd,pg,afile)
{
	if(isSubmitting) { return; }
    isSubmitting = true;
	$.ajax({
		url: pg,
		type: "POST",
		dataType: "json",
		data:  fd,
		contentType:false,
		processData:false,
		xhr: function () 
		{
			var xhr = new window.XMLHttpRequest();
			if(afile==true)
			{
				$('body').prepend('<div id="an_progress"><div><span></span><div class="progress"></div></div></div>');
				
				xhr.upload.addEventListener("progress", function (evt) {
				    if (evt.lengthComputable) 
				    {
				        var percentComplete = evt.loaded / evt.total;
				        //console.log(Math.round(percentComplete* 100));
				        $('.progress').css({
				            width: percentComplete * 100 + '%'
				        });
				        $('#an_progress span').html(Math.round(percentComplete*100)+'%');
				        
				        if (percentComplete === 1) 
				        {
				            $('.progress').addClass('hide');
				            $('#an_progress div').html('Files Uploaded Successfully. Please Wait ...');
				        }
				    }
				}, false);
				xhr.addEventListener("progress", function (evt) {
				    if (evt.lengthComputable) {
				        var percentComplete = evt.loaded / evt.total;
				        //console.log(Math.round(percentComplete* 100));
				        $('.progress').css({
				            width: percentComplete * 100 + '%'
				        });
				    }
				}, false);
				return xhr;
			}
			else
			{
				return xhr;
			}
		},
		beforeSend: function() 
		{   
			$("button[type='submit']").attr('disabled','disabled');
    		$("input[type='submit']").attr('disabled','disabled'); 
	        $('body').prepend('<div id="anloader"></div>'); 
        },
		success: function(xml) 
		{	
			isSubmitting = false;	
			$('span.errmsgs').html('');
			$('span.errmsg').remove();
			OnSuccess(xml);	
		},
		error: function() 
		{
			isSubmitting = false;
	        alert('ajax call failed');
	    },
	    complete: function() 
	    {
	    	isSubmitting = false;
	    	$("button[type='submit']").removeAttr('disabled');
    		$("input[type='submit']").removeAttr('disabled');
	    	$('#anloader').remove();
	    	$('#an_progress').remove();
			$('#an-err').remove();
        }
	 });
}

function OnSuccess(result)
{
	if(result[0])
	{
		$.each(result, function(i) {
			var jsonid = result[i].key;
			/*ErrorClass(jsonid,result[i].success,result[i].msg);*/
			ErrorAuto(jsonid,result[i].success,result[i].msg);
			ErrMsg(result[i]);
		});
	}
	else
	{
		ErrMsg(result);
	}
}

function ErrMsg(resmsg)
{
	if(resmsg.success=='2')
	{
		swal({title: resmsg.title, text: resmsg.msg},function(){});
	}

	if(resmsg.success=='3')
	{
		setTimeout(function(){
		  window.location.assign(resmsg.redirect);
		}, 500);
	}

	if(resmsg.success=='4')
	{
		swal({title: "", text: resmsg.msg},function(){
			setTimeout(function(){
				window.location.assign(resmsg.redirect);
			}, 500);
		});
	}

	if(resmsg.success=='5')
	{
		swal({title: "", text: resmsg.msg},function(){
			setTimeout(function(){
				$('[name="'+formname+'"]')[0].reset();
				$('.errmsg').remove();
			}, 500);
		});
	}

	if(resmsg.success=='6')
	{
		var callFunction = window[resmsg.func];
		callFunction(resmsg); // resmsg is parameter
	}

	if(resmsg.success=='7')
	{
		setTimeout(function(){				
			$('body .showSweetAlert').append('<span id="an-err">'+resmsg.err+'</span>');
		}, 100);
	}

	if(resmsg.success=='8')
	{
		swal({title: "", text: resmsg.msg},function(){
			setTimeout(function(){
				window.location.reload();
			}, 500);
		});
	}

	if(resmsg.success=='9')
	{
		swal({title: "", text: resmsg.msg},function(){
			setTimeout(function(){
				$('[name="'+formname+'"]')[0].reset();
				window.location.assign(resmsg.redirect);
			}, 500);
		});
	}
}

/** Start Find Checkboxes and Radio **/

function valctrlbtn(formname,control)
{
	$("form[name='"+formname+"']").find('input[type='+control+']').each(function(){
		var checkname = $(this).attr("name");
		var isDisabled = $('[name="'+checkname+'"]').is(':disabled');
	    if (isDisabled) 
	    {
	        // Do nothing
	    } 
	    else 
	    {
	    	if(!$('[name="'+checkname+'"]').is(':checked'))
			fd.append(checkname, "");
	    }		
	});
}

/** End Find Checkboxes and Radio **/

/** Find Error Display ***/
function ErrorAuto(name,check,msg)
{	
	if(check==0)
	{	
		if(/(.*?)(\[)(\d{1,3})(\])/.test(name))
		{
			var _this = $('[name="'+formname+'"] [name="'+name+'"]');
			if(!CustomErr($(_this),msg,check))
			{
				$(_this).next("span.errmsg").remove();
				$(_this).after('<span class="errmsg">'+msg+'</span>');
			}
		}
		else
		{
			$('[name="'+formname+'"] [name="'+name+'"]').each(function(i) {
				if(!CustomErr($(this),msg,check))
				{
					$(this).next("span.errmsg").remove();
					$(this).after('<span class="errmsg">'+msg+'</span>');
				}
			})
		}
	}	
}

function CustomErr($element,msg,check)
{
	var acodee = $element.attr('data-errfor');
	if(typeof acodee !== typeof undefined && acodee !== false)
	{
		if(check==0)
		{
			$('[name="'+formname+'"]').find('#'+acodee).attr('class','errmsgs').html(msg);
		}
		else if(check==1)
		{
			$('[name="'+formname+'"]').find('#'+acodee).html('');
		}

		return true;
	}
	else
	{
		return false;
	}
}

function ErrorClass(name,check)
{
	var regExp = /\[(\d{1,3})\]/; // Regex (For sval which is by default) to find [0],[1] etc..
	var matches = regExp.exec(name);

	var regExpMval = /\[()\]/; // Regex (For mval) to find [] with blank box
	var matchesMval = regExpMval.exec(name);

	if(check==0)
	{	
		if(matches)
		{
			var sqbracregExp = /(.*?)(\[)(\d{1,3})(\])/;
			var mat = sqbracregExp.exec(name);
			var newname = mat[1]+mat[2]+mat[4];
			
			$('[name="'+formname+'"] [name="'+newname+'"]').each(function(i) {
				if(i==mat[3])
				{
					$('[name="'+name+'"]').removeAttr('class','err');
					$(this).attr('class','err');
				}
			})	
		}
		else if(matchesMval)
		{
			$('[name="'+name+'"]').next('span.errmsg').remove();
			$('[name="'+name+'"]').last().after('<span class="errmsg">'+msg+'</span>');
		}
		else
		{
			$('[name="'+name+'"]').removeAttr('class','err');
			$('[name="'+name+'"]').attr('class','err');
		}
	}
	else
	{
		if(matches)
		{
			var sqbracregExp = /(.*?)(\[)(\d{1,3})(\])/;
			var mat = sqbracregExp.exec(name);
			var newname = mat[1]+mat[2]+mat[4];
			
			$('[name="'+formname+'"] [name="'+newname+'"]').each(function(i) {
				if(i==mat[3])
				{
					$(this).removeAttr('class','err');
				}
			})
		}
		else
		{
			$('[name="'+name+'"]').removeAttr('class','err');
		}
	}
}

function ErrorImage(name,check,msg)
{
	
}

/* Start Main Ajax */
function anAjax(url,data,datatype,sync) 
{
	sync = typeof sync !== 'undefined' ? sync : false;
	if(isSubmitting && !sync){ return; }
    isSubmitting = true;
	var myajax = $.ajax({
	    url: url,
		type: "POST",
		dataType: datatype,
		data:  data,
		async: sync,
		complete:function()
		{
			isSubmitting = false;
		}
	});
	return myajax;
}
/* End Main Ajax */

/* For drag priority see the documentation */

/* For captch see the documentation */

$(document).ready(function(){
	$('form').anform();
});
