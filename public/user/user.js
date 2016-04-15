$(document).ready(function(){
	$(document).on('click','#login-submit', function() {
		if($('#login').valid()) {
			var formData = $('#login').serialize();
			$.ajax({
				type: 'POST',
				url: '/user/login',
				data: formData,
				success:function(data){
					try {
						if(data.flag=='loginsuccess') {
							window.location.href = "/dashboard";
						} else if(data.flag=='loginFail') {
							showAlertPanel('#loginbox .panel-body', 'alert-danger', 'Wrong Credentials!');
						} else if(data.flag=='notconfirmed') {
							showAlertPanel('#loginbox .panel-body', 'alert-danger', 'Account Not Active!');
						}
					} catch(e){
						alert("Server error");
					}
				}		
			});
		}
	});
	
	$(document).on('click','#register-submit',function () {
		if($('#register').valid()) {
			loaderWait('show');
    		$.ajax({
    			type: 'POST',
    			url: '/user/register',
    			data: $('#register').serialize(),
    			dataType: 'json',
    			success:function(result) {
					loaderWait('hide');
    				if(result.status==1) {
    					showAlertPanel('#register', 'alert-success', result.message);
    					document.getElementById("register").reset();
    				} else if(result.status==2) {
    					$('#content').html(result.html);
    				}
    			}		
    		});
    	}
	});
	
	$(document).on('click','#refreshbutton',function() {
		$.ajax({
			url: '/user/refresh',
			data: '',
			dataType: 'json',
			success:function(data){
				$('#captcha-image').attr('src', '/captcha/' + data.src); 
		        $('#captcha-hidden').val(data.id);
			}
		});
	});
	
    $(document).on('click','#forgot-password',function () {
    	loaderWait('show');
    	$('#txt-email-error').remove();
    	if($('#frm-forgot-password').valid()) {
    		$.ajax({
    			type: 'POST',
    			url: '/user/forgotPassword',
    			data: {emailTxt:$("#txt-email").val()},
    			dataType: 'json',
    			success: function(result) {
    				if(result.status==1) {
    					loaderWait('hide');
    					$('#txt-email').parent().append('<label id="txt-email-error" class="error text-danger" for="txt-email">Please enter a valid email address.</label>');
    				} else if(result.status==2) {
    					loaderWait('hide');
    	            	$('#myModal').modal('hide');
    	            	$('.modal-backdrop').remove();
    					$('#popup-content').html('');
    					showAlertPanel('#loginbox .panel-body', 'alert-success', result.message);
    				}
    			}		
    		});
    	} else {
    		loaderWait('hide');
    	}
    });
    
    $(document).on('click','#ancr-register',function(){
    	$.ajax({
    		type: 'Get',
            dataType: 'html',
            url: '/user/register',
            data: '',
            success: function(data){
            	$('#content').html(data);
            }
    	});
    });
    
    $(document).on('click','#ancr-forgot-password',function(){
    	$.ajax({
    		type: 'Get',
            dataType: 'html',
            url: '/user/forgotPassword',
            data: '',
            success: function(data){
            	$('#popup-content').html(data);
            	$('#myModal').modal('show');
            }
    	});
    });
        
    var loaderWait = function (action) {
    	if(action == 'show') {
    		var height = window.innerHeight/2;
    		var width = window.innerWidth/2;
    		$('body').append('<div class="clo-md-3 modal fade in" style="display:block;z-index:1070">'+
    				'<img id="loadingImage" lass="img-responsive" src="/img/loading.gif"'+
    				' style="width: 80px; margin-top:220px; margin-left:620px;" >'+
    				'</div>');
    		$('body').append('<div id="loader-backdrop" class="modal-backdrop fade in" style="z-index:1060;"></div>');
        } else {
        	$('#loadingImage').parent('div').remove();
        	$('#loader-backdrop').remove();
        }
    }
    
    var showAlertPanel = function (selector, alertType, message) {
    	$(selector + ' .alert').remove();
		$(selector).prepend(
				'<div style="display: block" class="alert '+alertType+' alert-dismissible fade in">'+
					'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
						'<span aria-hidden="true">&times;</span>'+
					'</button>'+
					'<p>'+message+'</p>'+
				'</div>');
    }
});