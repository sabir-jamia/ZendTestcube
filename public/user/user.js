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
							$('#login-alert').text('Wrong Credentials');
							$('#login-alert').show();
						} else if(data.flag=='notconfirmed') {
							$('#login-alert').text('Account Not Active!');
							$('#login-alert').show();
						}
					} catch(e){
						$('#content').html(data);
						if ($('#serverUserError ul li').text() !== "") {
							$('#serverUserError').css("display","block");
						}
						if ($('#serverPassError ul li').text() !== "") {
							$('#serverPassError').css("display","block");
						}
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
    				console.log(result);
					loaderWait('hide');
    				if(result.status==1) {
    					$('#signupalert').removeClass().addClass('alert alert-success');
    					$('#signupalert').show();
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
    			success:function(result) {
    				if(result.status==1) {
    					loaderWait('hide');
    					$('#txt-email').parent().append('<label id="txt-email-error" class="error text-danger" for="txt-email">Please enter a valid email address.</label>');
    				} else if(result.status==2) {
    					loaderWait('hide');
    					$('#popup-content .modal-body').html(result.html);
    					$('#forgot-password').remove();
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
    
    function loaderWait(action) {
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
});