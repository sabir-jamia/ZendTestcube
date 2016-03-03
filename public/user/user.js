$(document).ready(function(){
	$(document).on('click','#login-submit', function() {
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
						$('#login-text2').html('Wrong Credentials');
						$('#txtPassword').val('');
					} else if(data.flag=='notconfirmed') {
						$('#login-text2').html('Account Not Active!');
						$('#txtPassword').val('');
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
	});
	
	$(document).on('click','#register-submit',function () {
		if($('#register').valid()) {
			$('#loadingImage').show();
    		$.ajax({
    			type: 'POST',
    			url: '/user/register',
    			data: {emailTxt:$("#txt-email").val()},
    			dataType: 'json',
    			success:function(result) {
    				if(result.status==1) {
    					$('#loadingImage').hide();
    					$('#txt-email').parent().append('<label id="txt-email-error" class="error" for="txt-email">Please enter a valid email address.</label>');
    				} else if(result.status==2) {
    					$('#content').html(result.html);
    				}
    			}		
    		});
    	}
	});
	
    $(document).on('click','#forgot-password',function () {
    	if($('#frm-forgot-password').valid()) {
    		$('#loadingImage').show();
    		$.ajax({
    			type: 'POST',
    			url: '/user/forgotPassword',
    			data: {emailTxt:$("#txt-email").val()},
    			dataType: 'json',
    			success:function(result) {
    				if(result.status==1) {
    					$('#loadingImage').hide();
    					$('#txt-email').parent().append('<label id="txt-email-error" class="error" for="txt-email">Please enter a valid email address.</label>');
    				} else if(result.status==2) {
    					$('#content').html(result.html);
    				}
    			}		
    		});
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
});