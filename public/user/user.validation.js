$(document).ready(function(){
	var errorSpanAdd = function(elementId, message){
		var error = '<span class="text-danger" style="display: inline;">'+
					'<label id="'+elementId+'-error" class="error" style="display: inline;" for='+
					elementId+'>'+message+ 
					'</label></span>';
		$('#'+elementId).parent('div').append(error);
	}
	
	var errorSpanRemove = function(elementId) {
		$('#'+elementId+'-error').parent('span').remove();
	}

	$.validator.addMethod("validName", function(value, element, regexp) {
		var re = new RegExp(regexp);
		return re.test(value);
	}, "Enter valid Name");
	
	$('#register').validate({
		rules: {
			'username':{
				required : true,
            	validName : /^[a-zA-Z0-9_]*$/,
            	minlength : 5,
            	maxlength :20,
            	remote : {
            			url : '/user/checkUserExists',
            			type : 'post',
            			data : {'user' :function() {return $('#username').val();}}
            	}
       	 	},
        	'firstName':{
        		required: true,
            	validName : /^[a-zA-Z]*$/,
            	minlength :2,
            	maxlength :20
         	},
         	'lastName':{
            	required: true,
            	validName : /^[a-zA-Z]*$/,
            	minlength :2,
            	maxlength :20
            },
         	'email':{
            	required: true,
            	minlength :7,
            	email: true,
            	maxlength :150,
            	remote : {
        			url : '/user/checkUserExists',
        			type : 'post',
        			data : {'user' :function() {return $('#email').val();}}
            	}
         	},
         	'password':{
            	required: true,
            	minlength :5,
            	maxlength :150
         	},
         	'confirmPassword':{
            	required: true,
            	minlength :5,
            	maxlength :150,
            	equalTo : '#password'
          	},
          	'captcha[input]' : {
          		required : true,
          		remote : {
        			url : '/user/checkCaptcha',
        			type : 'post',
        			data : {'captchaHidden':function() {return $('#captcha-hidden').val();}}
            	}
          	}
    	},
    	messages: {
    		'username' : {
    			remote : "Username already taken"
    		},
    		'email' : {
    			remote : "Email already taken"
    		},
    		'captcha[input]' : {
    			remote : "Captcha is invalid"
    		}
    	},
        errorPlacement: function(label, element) {
            label.addClass('text-danger');
            label.insertAfter(element);
        },
        wrapper: 'span',
	    highlight: function(element) {
	        $(element).parent('div').addClass('has-error has-feedback');
	    },
	    unhighlight: function(element) {
	    	$(element).parent('div').removeClass('has-error has-feedback');
	    },
    	submitHandler: function (form) {
    		return false;
    	}
	});
	
	$('#login').validate({ // initialize the plugin
		rules: {
			'userName':{
				required: true,
				maxlength :20
   	 		},
   	 		'password':{
   	 			required: true,
   	 			maxlength :150
   	 		}
		},
		errorPlacement: function () {},
	    highlight: function(element) {
	        $(element).parent('div').addClass('has-error has-feedback');
	    },
	    unhighlight: function(element) {
	        $(element).parent('div').removeClass('has-error has-feedback');
	    },
		submitHandler: function (form) {
			return false;
		}
	});
	
	$('#frm-forgot-password').validate({
		rules: {
			'txt-email':{
				required: true,
	            email: true,
	            maxlength :30
	         }
	    },
	    errorPlacement: function(label, element) {
            label.addClass('text-danger');
            label.insertAfter(element);
        },
        wrapper: 'span',
	    highlight: function(element) {
	        $(element).parent('div').addClass('has-error has-feedback');
	    },
	    unhighlight: function(element) {
	        $(element).parent('div').removeClass('has-error has-feedback');
	    },
	    submitHandler: function (form) {
	    	return false;
	    }
	});
});