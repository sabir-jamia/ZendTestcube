$(document).ready(function(){
	$('#register').validate({ // initialize the plugin
		rules: {
			'user-name':{
				required: true,
            	maxlength :20
       	 	},
        	'first-name':{
        		required: true,
            	maxlength :20
         	},
         	'last-name':{
            	required: true,
            	maxlength :20
         	},
         	'email':{
            	required: true,
            	email: true,
            	maxlength :150
         	},
         	'password':{
            	required: true,
            	maxlength :150
         	},
         	'confirm-password':{
            	required: true,
            	maxlength :150
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
});