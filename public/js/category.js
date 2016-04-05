$(document).ready(function(){
	$(document).on('click','#create-category', function() {
		createCategory();
	});
	
	$(document).on('click','#submit-category', function() {
		createCategory($('#add-category').serialize(), 'post');
	});
	
	$(document).on('click', '#confirm-delete', function() {
		var ids = $('#confirm-delete').data('id').toString();
		
		if(ids.split(",").length == 1) {
			deleteCategory(this, ids, 'post');
		} else {
			deletSelected(ids, 'post');
		}
	});

	var prevSelectedVlue = $("#tbl-select-value option:selected").val();
	$(document).on('change', '#tbl-select-value', function() {
		var selectedValue = this.value;
		var selectedPage = $('#tbl-pagination li.active').find('a').text().trim();
		var offset = (selectedPage - 1) * selectedValue;
		var rowCount = parseInt($('#row-count').val());
		
		if(prevSelectedVlue < selectedValue) {
			updatePagination('remove', selectedValue);
		} else if(prevSelectedVlue > selectedValue) {
			updatePagination('add', selectedValue);
		}
		
		if(offset >= rowCount) {
			var lastLi = $('#tbl-pagination li:nth-last-child(2)');
			offset = parseInt(lastLi.find('a').text().trim());
			offset = (offset - 1) * selectedValue;
			lastLi.addClass('active');
			populateTable(selectedValue, offset);
		} else if(prevSelectedVlue != selectedValue) {
			populateTable(selectedValue, offset);
		}
	
		prevSelectedVlue = selectedValue;
	});
	
	$('#tbl-pagination li').on('click', function(){
		pagination(this);
	});
	
	$(document).on('click', '#tbl-pagination li:first-child', function() {
	});

	$(document).on('click', '#tbl-pagination li:last-child', function() {
	});
	
	$('#chkAll').click(function() {	
		var checkAllChecked = $('#chkAll:checked').length;
		if(checkAllChecked) {
			$(':checkbox[name=deleteall]').prop('checked', true);
		} else {
			$(':checkbox[name=deleteall]').prop('checked', false);
		}	
		showHideMultiDelete();
	});
	
	$(document).on('click', 'input[name="deleteall"]', function() {
		showHideMultiDelete();
	});
	
	$(document).on('click', '#multi-delete', function() {
		var atLeastOneIsChecked = $('input[name="deleteall"]:checked').length;
		var j = 0;
		
		if(atLeastOneIsChecked > 0) {
			var boxes = $('input[name="deleteall"]:checked');
			var box = new Array();
			$.each(boxes, function(key, value){
				box[key] = $(value).val();
			});
			deletSelected(box.join());
		}
	});
});

$(document).on('click','#savechanges', function() {
	if (validateeditcatname() == 1) {
		$('#myModal').modal('show');
		return 0;
	} else if (validateeditcatname() == 0) {
		var catname = $('#txtCategoryName').val();
		var catid = $('#hide').val();
		var catname = catname.trim();
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
				} else if (result.status == 1) {
						$('#errCatEdit').html('Category already taken or same name').css('color', 'red');
				}
			}
		});
	}
});

function validatecatname() {
	var catname = 'txtCategoryName';
	if (isNull(catname) === 0) {
		$('#errCat').html('Please Enter Category Name').css('color', 'red');
		return 1;
	} else if (validateUserName(catname) === 0) {
		$('#errCat').html('Enter valid Category Name').css('color', 'red');
		return 1;
	}
	return 0;
}

function validateeditcatname() {
	var catname = 'txtCategoryName';
	if (isNull(catname) === 0) {
		$('#errCatEdit').html('Please Enter Category Name').css('color', 'red');
		return 1;
	} else if (validateUserName(catname) === 0) {
		$('#errCatEdit').html('Enter valid Category Name').css('color', 'red');
		return 1;
	}
	return 0;
}

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
};

var addRows = function(rowData) {
	var row = "";
	$.each(rowData, function(key, value) {
		row = '<tr>'+
				'<td>'+
					'<input type="checkbox" id="newchk" name="deleteall" value="'+value.id+'">'+
				'</td>'+
				'<td>'+
					'<a id="'+value.id+'" class="viewQuestion" href="9" title="Select the category to add questions">'+
						value.name+
					'</a>'+
				'</td>'+
				'<td>'+
					'<a style="cursor:pointer;" onclick="deleteCategory(this,'+value.id+')" class="col-sm-3">'+
						'<span aria-hidden="true" class="glyphicon glyphicon-remove"></span>'+
						'</a>' +
						'<a style="cursor:pointer;" onclick="createCategory('+value.id+')" class="col-sm-3">'+
							'<span aria-hidden="true" class="glyphicon glyphicon-edit"></span>'+
						'</a>'+
						'<a style="cursor:pointer;" href="/category/viewCategory/'+value.id+'" class="col-sm-3">'+
							'<span aria-hidden="true" class="glyphicon glyphicon-eye-open"></span>'+
						'</a>'+
				'</td>'+
			'</tr>';
		$('#example tbody').append(row);
	});
};

var removeRows = function(limit) {
	limit = limit -1;
	$("#example tbody tr:gt("+limit+")").remove();
};

var updatePagination = function(addOrRemove, selectedValue) {
	var rowCount = parseInt($('#row-count').val());
	var pageCount = Math.ceil(rowCount / selectedValue);
	var currentPageCount = $('#tbl-pagination li').length - 1;
	
	if(addOrRemove == 'add') {
		var page;
		for(var i=currentPageCount;i <= pageCount;i++) {
			$('<li><a href="#">'+i+'</a></li>').insertBefore('#tbl-pagination li:last-child');
		}
	} else {
		$('#tbl-pagination li:gt('+pageCount+'):not(:last-child)').remove();
	}
	$('#tbl-pagination li').on('click', function(){
		pagination(this);
	});
};

var pagination = function(self) {
	var rowCount = parseInt($('#row-count').val());
	
	if($(self).find('a').attr('aria-label') != 'Previous' && 
		$(self).find('a').attr('aria-label') != 'Next') {
		var selectedPage = $(self).find('a').text().trim();
		var previousPage = $('#tbl-pagination li.active').text().trim();
		var selectedValue = $("#tbl-select-value option:selected").val(); 
		
		if(offset > rowCount) {
			var lastLi = $('#tbl-pagination li:nth-last-child(2)');
			offset = lastLi.find('a').text().trim();
			lastLi.addClass('active');
			populateTable(selectedValue, offset);
		} else if(selectedPage != previousPage) {
			var offset = (selectedPage - 1) * selectedValue;
			populateTable(selectedValue, offset);
		}	
		$('#tbl-pagination li.active').removeClass('active');
		$(self).addClass('active');
	}
};

var populateTable = function(limit, offset) {
	$.ajax({
		url : "/category/fetch",
		type : 'get',
		data : {limit:limit, offset:offset},
		dataType : 'json',
		success : function(result) {
			$("#example tbody tr").remove();
			addRows(result);
		}
	});	
};

//create and update category
var createCategory = function(data, type) {
	
	if (typeof type == "undefined") {
		type = "get";
	}
	
	if(typeof data == "undefined") {
		data = "";
	}
	
	if(type == "get" && data) {
			data = {id : data};
	}
	$.ajax({
		url : "/category/addCategory",
		type : type,
		data : data,
		dataType : 'json',
		success : function(result) {
			
			if(result.html) {
				$("#popup-content").html(result.html);
				$('#myModal').modal('show');
			} else {
				$('#myModal').modal('hide');
				
				if (result.status == 1) {
					window.location.href = "/category";
				} else if (result.status == 2) {
					$('#errCat').html('Some error occured').css('color', 'red');
				}
			}
		}
	});		
};

var deleteCategory = function(self, id, type) {

	if(typeof type == "undefined") {
		type = "get";
	}
	$.ajax({
		url : "/category/deleteCategory",
		type : type,
		data : {id : id},
		dataType : 'json',
		success : function(result) {
			
			if(result.html) {
				$("#popup-content").html(result.html);
				$('#myModal').modal('show');
			} else {
				$(self).parent().parent().remove();
			}
		}
	});		
};

var deletSelected = function(str, type) {
	
	if(typeof type == "undefined") {
		type = "get";
	}
	$.ajax({
		url : "/category/deleteSelected",
		type : type,
		dataType : 'json',
		data : { ids : str },
		success : function(result) {
			if(result.html) {
				$("#popup-content").html(result.html);
				$('#myModal').modal('show');
			} else if(result.status = 1){
				$('#myModal').modal('hide');
				window.location.href = "/category";
			}
		}
	});
};

var showHideMultiDelete = function() {
	var numOfChecked = $('input[name="deleteall"]:checked').length;
	
	if(numOfChecked) {
		$('#multi-delete').show();	
	} else {
		$('#multi-delete').hide();	
	}
};