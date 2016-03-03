/* ========================================================================
 * Author : Manish
 * Module : Category
 * Date	  : 01-07-2014
 * site   : zendtestcube.com
 * ======================================================================== */

/*category */

$(function() {

	/* add Category Modal */
	$("#btnCategoryCreate").click(function(e) {
		$('#myModalLabel').html("Add new Category");

		$.ajax({
			url : "/category/add",
			success : function(result) {
				$("#modal-body").html(result);

			}
		});
	});


	$(document).on('click', '#deleteCategory', function() {
		

		var catid = $("#deleteCategory").val();

		$.ajax({
			type : "POST",
			url : "/category/deleteCategory",
			data : {id : catid},
			success : function(result) {
				$('#myModal').modal('hide');
				$.ajax({
					url : "/category/list",
					success : function(result) {
						window.location.reload();
					}
				});
			}
		});

	});

	/*document.ready finished */

});

/* Edit Category Modal */

function editCat(catId){
	
	$('#myModalLabel').html("Edit Category");
	
	$.ajax({
		url : "/category/edit",
		data : {catid : catId},
		success : function(result) {
			$("#modal-body").html(result);
		}
	});
}

/* Delete Category Modal */

// calling the delete confirmation box
function deleteCat(catId) {
	$('#myModalLabel').html("Delete Category");

	var name = $("#deleteCat"+catId).data('name');
	$.ajax({
		url : "/category/delete",
		data : {catid : catId,catname : name},
		success : function(result) {

			$("#modal-body").html(result);

		}
	});
}

$(document).on('click','#btnAddCategory',
		function() {

			if (validatecatname() == 1) {

				$('#myModal').modal('show');

				return 0;
			}

			else if (validatecatname() == 0) {
				var catname = $('#txtCategoryName').val();
			//var catNameTesting = catname.replace(/\s/g, '');
				

				var catid = 0;
				$.ajax({
					url : "/category/add",
					type : 'POST',
					data : {
						name : catname,
						//catNameForTesting : catNameTesting,
						id : catid
					},
					dataType : 'json',

					success : function(result) {
						if (result.status == 0) {

							$('#myModal').modal('hide');

							window.location.assign("category");
						}

						else if (result.status == 1) {
							//alert('sgahsghj');
						} else if (result.status == 2) {
							$('#errCat').html('Category already taken').css(
									'color', 'red');
						}
						else if (result.status == 3) {
							$('#errCat').html('Enter valid Category Name').css(
									'color', 'red');
						}

					}
				});
			}

		});

/*
                 var catname = "#" + catname;
                 var catnameVal = $(catname).val().trim();

         $.ajax({

                 type: 'POST',
                 data: {txtVal:catnameVal},
                 dataType: 'json',
                 url: '/category/checkVal',

                 success:function(data){
                         if(data.val === 0){
                         return 0;
                         
                     } else {
                         $('#errCat').html('Category already taken').css('color','red');
                        
                        return 1;
                     }
                 }

             });


 */

/*reload category list */

function reloadcatlist() {
	$.ajax({
		url : "/category/list",
		success : function(result) {
			$("#catlist").html(result);

		}
	});
}

/* multiple delete   */

$(document).ready(function() {
	$('#chkAll').click(function() {
		$(':checkbox[name=deleteall]').prop('checked', this.checked);
	});
});


$(document).on('click', '.chk', function() {
	 document.getElementById("chkAll").checked = false;
});





$(document).on('click', '#multiDeleteCatbtn', function() {
	
	$('#myModalLabel').html("Delete Categories");
	var atLeastOneIsChecked = $('input[name="deleteall"]:checked').length > 0;
	if(atLeastOneIsChecked)
	{
		$('#errorCat').html('');
		

	$.ajax({
		url : "/category/deleteall",
		success : function(result) {
			$("#modal-body").html(result);

		}
	});

	}
	else
	{
		$("#modal-body").html('You havent selected any checkbox');
		 //alert('You havent selected any checkbox');
	}	//alert('working');
	
	});

	/*$.ajax({
	        
	        type: "POST",
	        data: "",

	        url: base_url +'index.php?controller=category&function=deleteMulCatName', //the script to call to get data          

	        //data: "", //you can insert url argumnets here to pass to api.php for example "id=5&parent=6"
	        dataType: 'html',
	       
	        
	        beforeSend: function() {
	
	        },
	        success: function(response) {
	            $('#myModalLabel').html("Delete Category");
	            $('#modal-body').html(response);
	              
	       
	        },
	        complete: function() {
	
	        },
	        error: function() {
	            
	        }
	    });*/




// opening multiple delete window

function deleting() {

	var j = 0;
	var boxes = document.getElementsByClassName('chk');
	var box = new Array();

	for ( var i = 0; i < boxes.length; i++) {
		if (boxes[i].checked) {
			box[j] = boxes[i].value;
			j++;
		}
	}
	var str = box.join();

	$.ajax({
		url : "/category/deleteSelected",
		data : {
			id : str
		},
		success : function(result) {
			window.location.assign("category");
			//alert("successful");

		}
	});

}

/*$("#delmulcat").click(function(){
 var id=$(".form-control").attr('id');

 alert(id);

 $.ajax({
 url : "/category/deleteall",
 success : function(result) {
 $("#myModal").html(result);

 }
 });

 });*/

$(document).on('click','#savechanges',
		function() {

			if (validateeditcatname() == 1) {

				$('#myModal').modal('show');

				return 0;
			}

			else if (validateeditcatname() == 0) {

				var catname = $('#txtCategoryName').val();
				var catname = catname.trim();
				var catid = $('#hide').val();

				$.ajax({
					url : "/category/edit",
					type : 'POST',
					data : {
						name : catname,
						id : catid
					},
					dataType : 'json',

					success : function(result) {
						if (result.status == 0) {

							$('#myModal').modal('hide');

							window.location.assign("category")
						}

						else if (result.status == 1) {
							$('#errCatEdit').html(
									'Category already taken or same name').css(
									'color', 'red');
						}
						//  $('#myModal').modal('hide');

						// window.location.assign("category")

					}
				});
			}

		});


$(document).on('focus','#txtCategoryName',
		function() {
			$('#errCatEdit').html('');
			$('#errCat').html('');
});


function validatecatname() {

	var catname = 'txtCategoryName';
	if (isNull(catname) === 0) {
		$('#errCat').html('Please Enter Category Name').css('color', 'red');
		return 1;
	} else if (validateCategoryName(catname) === 0) {
		$('#errCat').html('Enter valid Category Name').css('color', 'red');
		return 1;
	}
	return 0;
}

function validateeditcatname() {

	var catname = 'txtCategoryName';
	//alert(isNull(catname));
	if (isNull(catname) === 0) {
		$('#errCatEdit').html('Please Enter Category Name').css('color', 'red');
		return 1;
	} else if (validateCategoryName(catname) === 0) {
		$('#errCatEdit').html('Enter valid Category Name').css('color', 'red');
		return 1;
	}
	return 0;
}
