
function isNull(Id){
	
	var id = "#" + Id;
	
	var txtVal = $(id).val().trim();
	if(txtVal.length==0){
		return 0;
	} else {
		return 1;
	}
}

function validateUserName(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(txtVal.length < 5)
	{
		return 2;
	}
	else if(txtVal.length>20)
	{
		return 3;
	}
	else if(!txtVal.match(/^[a-zA-Z0-9_]{5,20}$/)){
		return 0;
	} else {
		return 1;
	}
}

function validateFirstName(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(txtVal.length < 2)
	{
		return 2;
	}
	else if(txtVal.length>20)
	{
		return 3;
	}
	else if(!txtVal.match(/^[a-zA-Z]{2,20}$/)){
		return 0;
	} else {
		return 1;
	}
}


function validateCategoryName(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(!txtVal.match(/^[a-zA-Z0-9_' '+#.]{1,20}$/)){
		return 0;
	} else {
		return 1;
	}
}

function validateEmail(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(!txtVal.match(/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i )){
		return 0;
	} else {
		return 1;
	}
}

function validateFname(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(!txtVal.match(/^[a-zA-Z]{1,20}$/)){
		return 0;
	} else {
		return 1;
	}
}

function validatePassword(Id){
	var id = "#" + Id;
	
	var txtVal = $(id).val().trim();
	if(txtVal.length < 5){
		return 0;
	} else {
		return 1;
	}
}

function matchPassword(create,con){
	
	var id1 = "#" + create;
	var id2 = "#" + con;
	var txtVal1 = $(id1).val().trim();
	var txtVal2 = $(id2).val().trim();
	if(txtVal1 != txtVal2){
		return 0;
	} else {
		return 1;
	}

}

function validateCaptchaLength(Id){
	var id = "#" + Id;
	var txtVal = $(id).val().trim();
	if(txtVal.length<6){
		return 0;
	} else {
		return 1;
	}
}

