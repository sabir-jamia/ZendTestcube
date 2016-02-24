$(document).ready(function(){

$(document).on('blur','#txtRegUsername',function(){


var regUnameId = $(this).attr('id');

if(isNull(regUnameId) === 0){
$('#errRegUserName').html('Please enter UserName').css('color','red');
} else if(validateUserName(regUnameId) === 0){
$('#errRegUserName').html('Enter valid UserName').css('color','red');
}else if(validateUserName(regUnameId) === 2){
$('#errRegUserName').html('Minimum length of username : 5 characters').css('color','red');
}
else if(validateUserName(regUnameId) === 3){
$('#errRegUserName').html('Maximum length of username : 20 characters').css('color','red');
}
 else {
	var uname = "#" + regUnameId;
	var unameVal = $(uname).val().trim();

	$.ajax({

		type: 'POST',
		data: {txtVal:unameVal},
		dataType: 'json',
		url: '/user/checkVal',

		success:function(data){
			if(data.val === 0){
				$('#errRegUserName').html('UserName available').css('color','green');
			} else {
				$('#errRegUserName').html('UserName already taken').css('color','red');
			}
		}

	});
}
}).focus(function(){
$('#errRegUserName').html("");
});


//validating firstname......................................



$(document).on('blur','#txtRegFirstname',function(){


var regFnameId = $(this).attr('id');

if(isNull(regFnameId) === 0){
$('#errRegFirstname').html('Please enter Firstname ').css('color','red');
} else if(validateFirstName(regFnameId) === 0){
$('#errRegFirstname').html('Enter valid Firstname').css('color','red');
}
else{
$('#errRegFirstname').html("");	
}
 });/*.focus(function(){
$('#errRegFirstname').html("");*/
/*});*/

//validating last name.....................................

$(document).on('blur','#txtRegLastname',function(){


var regLnameId = $(this).attr('id');

if(isNull(regLnameId) === 0){
$('#errRegLastname').html('Please enter Lastname').css('color','red');
} else if(validateFirstName(regLnameId) === 0){
$('#errRegLastname').html('Enter valid Lastname').css('color','red');
}
else{
$('#errRegLastname').html("");	
}
 });





$(document).on('blur','#txtEmail',function(){	
	var filter = /^([a-zA-Z0-9_\.\-\+0-9])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
var regEmailId = $(this).attr('id');
if(isNull(regEmailId) === 0){
$('#errEmail').show();	
$('#errEmail').html('Please enter Email').css('color','red');
} else if(!filter.test($(this).val())) {
$('#errEmail').html('Enter valid Email').css('color','red');
} else {

	var email = "#" + regEmailId;
	var emailVal = $(email).val().trim();

	$.ajax({

		type: 'POST',
		data: {txtVal:emailVal},
		dataType: 'json',
		url: '/user/checkVal',

		success:function(data){
			if(data.val === 0){
				$('#errEmail').html('Email available').css('color','green');
			} else {
				$('#errEmail').html('Email already taken').css('color','red');
			}
		}
	});
}
}).focus(function(){
$('#errEmail').html('');
});

$(document).on('blur','#txtPass',function(){	

var regPassId = $(this).attr('id');
if(isNull(regPassId) === 0){
$('#errPass').html('Please enter Password').css('color','red');
} else if(validatePassword(regPassId) === 0){
$('#errPass').html('Invalid password(atleast 5 characters required)').css('color','red');
} else {
$('#errPass').html('');
}
}).focus(function(){
$('#errPassword').html('');
});

$(document).on('blur','#txtconfirmPass',function(){	

	var cre = $('#txtPass').attr('id');
	var con = $('#txtconfirmPass').attr('id');
	

var regconfirmPassId = $(this).attr('id');
if(isNull(regconfirmPassId) === 0){
$('#errconfirmPass').html('Please enter Password').css('color','red');
} else if(validatePassword(regconfirmPassId) === 0){
$('#errconfirmPass').html('Invalid password(atleast 5 characters required)').css('color','red');
} else if(matchPassword(cre,con) === 0){
$('#errconfirmPass').html('Password do not match').css('color','red');
}else {
$('#errconfirmPass').html('');
}
}).focus(function(){
$('#errPassword').html('');
});


$(document).on('click','#btnRegister',function(event){	


var userEmail = $('#txtEmail').val();
var cre = $('#txtPass').attr('id');
	var con = $('#txtconfirmPass').attr('id');
	if(matchPassword(cre,con) === 0){
		$('#errconfirmPass').html('Password do not match').css('color','red');
		event.preventDefault();
		return;
	}

var count = 1;
var formElementsId = $('#frmRegister :text , #frmRegister :password').each(function(){

var fieldsId = $(this).attr('id');

if(isNull(fieldsId) === 0){

if(count === 1){
$('#errRegUserName').html('Please enter UserName').css('color','red');
} else if(count === 2){
$('#errRegFirstname').html('Please enter First Name').css('color','red');
} else if(count === 3){
$('#errRegLastname').html('Please enter Last Name').css('color','red');
} else if(count === 4){
$('#errEmail').html('Please enter Email').css('color','red');
} else if(count === 5){
$('#errPass').html('Please enter Passwrod').css('color','red');
} else if(count === 6){
$('#errconfirmPass').html('Please enter Passwrod').css('color','red');
} else if(count === 7){
$('#errCaptcha').html('Invalid captcha').css('color','red');
}
//alert(count);
event.preventDefault();
} 


if(count === 1){
if(validateUserName(fieldsId) === 0){
	$('#errRegUserName').html('Enter valid UserName').css('color','red');
event.preventDefault();
}
} 
else if(count === 2){
if(validateFname(fieldsId) === 0){
	$('#errRegFirstname').html('Enter valid Firstname').css('color','red');
event.preventDefault();
}
}
else if(count === 3){
if(validateFname(fieldsId) === 0){
	$('#errRegLastname').html('Enter valid Lastname').css('color','red');
event.preventDefault();
}
}
else if(count === 4){
if(validateEmail(fieldsId) === 0){
	$('#errEmail').html('Enter valid Email').css('color','red');
event.preventDefault();
}
} else if(count === 5){
if(validatePassword(fieldsId) === 0){
$('#errPass').html('Invalid password(atleast 5 characters required)').css('color','red');
event.preventDefault();
}
} else if(count === 6){
if(validatePassword(fieldsId) === 0){
	$('#errconfirmPass').html('Invalid password(atleast 5 characters required)').css('color','red');
event.preventDefault();
}
} else {

	$('#loadingImageReg').show();
	event.preventDefault();
	
	var formData = $('#frmRegister').serialize();

	$.ajax({

		type: 'POST',
		url: '/user/register',
		data: formData,
		//data: {emailTxt:userEmail},
		dataType: 'json',

		success:function(data){
			$('#loadingImageReg').hide();
			if(data.succ === 1){
				//ajaxReg();
				window.location.assign("?statusmsg=1");
			 //location.reload();
			} else {
				$('#errCaptcha').html('Invalid captcha').css('color','red');
			}
		},

	});

}

count++;

});
});

$(document).on('click','#redirectTOLogin',function(){
	window.location.assign("");
});	


/*function ajaxReg()
{

$.ajax({

		type: 'POST',
		url: '/user/popup',
		data: formData,
		//data: {emailTxt:userEmail},
		dataType: 'json',

		success:function(data){
			$('#loadingImageReg').hide();
			if(data.succ === 1){
				ajaxReg();
			 location.reload();
			} else {
				$('#errCaptcha').html('Invalid captcha').css('color','red');;
			}
		},

	});




}
*/








$(document).on('click','#refreshbutton',function() { 
$('#captchaimg_signup').val("");
$('#errCaptcha').text(''); 
$.ajax({

url: '/user/refer',
data: '',
dataType: 'json',


success:function(data){

$('#captchaimg_signup-image').attr('src', '/captcha/' + data.src); 
               	$('#captchaimg_signup-hidden').val(data.id);
               	

    event.preventDefault();
}
});
      
  }); 

$(document).on('blur','#captchaimg_signup',function(){	

var captchaId = $(this).attr('id');
if(isNull(captchaId) === 0){
$('#errCaptcha').html('Invalid captcha').css('color','red');
}
}).focus(function(){
$('#errCaptcha').val("");
});

$(document).on('click',"#ancrRegister",function(){

       $("#frmRegister")[0].reset();
       $(".regiser-errorTxt").html("");
       $("#fade").fadeIn();
       $("#light").show();
   });

$(document).on('click','#ancrRegister',function(){

                    $.ajax({

                        type: 'Get',
                        dataType: 'html',
                        url: '/user/register',
                        data: '',

                        success: function(data){

                            $('#frmRegister').html(data);
                        }
                    });
                });
	// login page username textbox onfocus function to remove username error
	$(document).on('focus','#txtUserName',function(){
		$('#errUserName').html("");
		$('#serverUserError').css("display","none");
	});
	
	// login page password textbox onfocus function to remove password error
	$(document).on('focus','#txtPassword',function(){
		$('#errPassword').html("");
		$('#serverPassError').css("display","none");
	});
	
	// forgot password popup email textbox onfocus function to remove email error
	$(document).on('focus','#txtForgotPassword',function(){
		$('#errEmailPassword').html("");
	});
	
	/*validation for login form*/

	/*$(document).on('blur','#txtUserName',function(){

			var unameId = $(this).attr('id');

			if(isNull(unameId) === 0){
				$('#errUserName').html('Please enter UserName/Email').css('color','red');
			} else if(validateUserName(unameId) === 0 && validateEmail(unameId) === 0){
				$('#errUserName').html('Enter valid UserName/Email')
			} else {
				$('#errUserName').html('');
			}
			}).focus(function(){
				$('#errUserName').html('');
			});


	$(document).on('blur','#txtPassword',function(){

			var upassId = $(this).attr('id');

			if(isNull(upassId) === 0){
				$('#errPassword').html('Please enter Password').css('color','red');
			} else if(validatePassword(upassId) === 0){
				$('#errPassword').html('Invalid password(atleast 5 characters required)')
			} else {
				$('#errPassword').html('');
			}
			}).focus(function(){
				$('#errPassword').html('');
		  });*/


    $(document).on('click','#btnSubmit',function (event) {
        var t = 1;
        $("#login :text , #login :password").each(function () {
            var n = $(this).attr("id");

            if (isNull(n) === 0) {
                if (t === 1) {
                	$('#serverUserError').css("display", "none");
                	$('#serverPassError').css("display", "none");
                    $("#errUserName").html("Please enter UserName/Email.").css("color", "red");
                } else if (t === 2) {
                    $("#errPassword").html("Please enter password").css("color", "red");
                }
                event.preventDefault();
            } else {
				event.preventDefault();
				var formData = $('#login').serialize();

				$.ajax({

					type: 'POST',
					url: '/user/process',
					data: formData,
					/*dataType: 'json',*/

					success:function(data){
						
						try {
							data = $.parseJSON(data);
						
							if(data.flag=='loginsuccess') {
								  window.location.href = "/dashboard";
							} else if(data.flag=='loginFail') {
									$('#login-text2').html('Wrong Credentials');
									$('#txtPassword').val('');
							}
							else if(data.flag=='notconfirmed') {

									$('#login-text2').html('Account Not Active!');
									$('#txtPassword').val('');
							}
						}
						catch(e){
							$('#content').html(data);
							if ($('#serverUserError ul li').text() !== ""){
								$('#serverUserError').css("display","block");
							}
							if ($('#serverPassError ul li').text() !== ""){
							$('#serverPassError').css("display","block");
							}
						}
					}		
				});
				return false;
			}
            t++;
        })
    });
	

	/*$(document).on('blur','#txtUserName',function(){

		var value = $(this).val();
		$('[name="email"]').val(value);

	});	*/



	$(document).on('click','#forgotPassword',function(){
	
		var txtVal = $('#txtForgotPassword').val().trim();
			if(txtVal.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/)){
				$('#errEmailPassword').html('');
				$('#loadingImage').show();
				$.ajax({
					type: 'POST',
					url: '/user/forgotpassword',
					data: {emailTxt:txtVal},

					dataType: 'json',

					success:function(result){
						if(result.status==1)
						{
							$('#errEmailPassword').show();
							$('#loadingImage').hide();
							$('#errEmailPassword').html('Email id doesnt exist');
							$('#txtForgotPassword').val('');
					
						}
						else if(result.status==2)
							{	
								$('#errEmailPassword').show();
							$('#errEmailPassword').html('Your password has been sent to email');
							$('#forgotPassword').hide();
							$('#loadingImage').hide();
							$('#goToLogin').show();
								$("#goToLogin").click(function(){
											$('#txtForgotPassword').val('');
											$('#errEmailPassword').hide();
											$('#forgotPassword').show();
											$('#goToLogin').hide();
  									$('#myModal').modal('hide');
							 // window.location.href = "/user/login";
											}); 


							  
									//$('#myModal').modal('hide');
							  //window.location.href = "/user/login";
							}
					}		
				});



		
	} else {
		$('#errEmailPassword').html('Invalid email id');
		$('#errEmailPassword').show();
		$("#modalCloseButton").click(function(){
											$('#txtForgotPassword').val('');
											$('#errEmailPassword').hide();
  											$('#myModal').modal('hide');

							 				 //window.location.href = "/user/login";
									}); 

	}




});
});

$(document).on('click','#resetbtn',function (event) {
	$('#errRegUserName').html('	');
	$('#errEmail').html('	');
	$('#errPass').html('	');
	$('#errconfirmPass').html('	');
	$('#errCaptcha').html('	');
	$('#errRegFirstname').html('	');
	$('#errRegLastname').html('	');

});


$(document).on('focus','#captchaimg_signup',function () {
	
	
	$('#errCaptcha').html('	');
	
	$('#captchaimg_signup').val("");
});
