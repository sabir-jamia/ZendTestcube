$(document).ready(function(){
	$.validator.addMethod("validName", function(value, element, regexp) {
		var re = new RegExp(regexp);
		return re.test(value);
	}, "Enter valid Name");
	
	var validator = $('#register').validate({
		rules: {
			'user-name':{
				required : true,
            	validName : /^[a-zA-Z0-9_]*$/,
            	minlength : 5,
            	maxlength :20
       	 	},
        	'first-name':{
        		required: true,
            	validName : /^[a-zA-Z]*$/,
            	minlength :2,
            	maxlength :20
         	},
         	'last-name':{
            	required: true,
            	validName : /^[a-zA-Z]*$/,
            	minlength :2,
            	maxlength :20
            },
         	'email':{
            	required: true,
            	minlength :7,
            	email: true,
            	maxlength :150
         	},
         	'password':{
            	required: true,
            	minlength :5,
            	maxlength :150
         	},
         	'confirm-password':{
            	required: true,
            	minlength :5,
            	maxlength :150,
            	equalTo : '#password'
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
	    	if()
	    	$(element).parent('div').removeClass('has-error has-feedback');
	    },
    	submitHandler: function (form) {
    		return false;
    	}
	});
	
	var user = '';
	var prevUser = '';
	var errorSpan = function(elementId,value){
		var error = '<span class="text-danger" style="display: inline;">'+
					'<label id="'+elementId+'-error" class="error" style="display: inline;" for='+
					elementId+'>'+value+' is already taken'+ 
					'</label></span>';
		$('#'+elementId).parent('div').append(error);
		$('#'+elementId).attr('data-checkuser', 1);
	}
	function checkUserExists(elementId, value) {
		if(validator.element('#'+elementId)) {
			user = $('#'+elementId).val();
			if(prevUser != user) {
				$.ajax ({
					type: 'post',
					dataType: 'json',
					url: '/user/checkUserExists',
					data: {'user' : $('#'+elementId).val()},
					success: function(data){
						$('#'+elementId+'-error').parent('span').remove();
						$('#'+elementId).attr('data-checkuser', 0);
						if(data.status) {
							errorSpan(elementId, value);
						}
					}
				});
				prevUser = user;
			}
		}
	}
	
	$('#user-name').focusout(function () {
		checkUserExists('user-name', 'Username');
	});
	
	$('#email').focusout(function () {
		checkUserExists('email', 'Email');
	});
	
	$('#login').validate({ // initialize the plugin
		rules: {
			'username':{
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