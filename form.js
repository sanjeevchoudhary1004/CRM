getForms();

function getForms(){
	$('#AllForms').prop('checked', false);
	roles_table = $('#forms-datatable').DataTable({
		"processing": true,
        "serverSide": true,
        "bDestroy": true,
	    "ajax" : {
	        "url" : SITE_URL + "form/data",
	        "type" : "post"
	    },
	    "dom" : 'tip',
	    "columnDefs" : [ {
	        "orderable" : false,
	        "targets" : [ 0 ]
	    } ],
	    "order" : [ [1, 'asc' ] ],
	});
}

jQuery.validator.addMethod("lettersonly", function(value, element) {
	  return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Letters only please"); 

jQuery.validator.addMethod("alphaspecial", function(value, element) {
	  return this.optional(element) || /^([a-zA-Z0-9-@._#$%*]{1})+[a-zA-Z0-9-@._#$%*]{1,15}$/i.test(value);
}, "Special characters are not allowed other than [. @ # $ % _ - *]. LoginId should not contain whitespaces.");


jQuery.validator.addMethod("validChar", function(value, element) {
	  return this.optional(element) || /^([1-zA-Z0-1@.\s]{1,500})$/i.test(value);
}, "Valid chars only please"); 

//Custome Email Validation
$.validator.addMethod("customEmail", function(value, element) {
	return /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value);
},"Please enter valid Email.");
//Custome Email Validation ENDS

$(document).on('click', '.FormID', function(e){
	if($('.FormID:enabled').size() == $('.FormID:checked:enabled').size()){
		$('#AllForms').prop('checked', true);
	}else{
		$('#AllForms').prop('checked', false);
	}
});

$(document).on('click', '#AllForms', function(){
	$('.FormID:enabled').prop('checked', $(this).prop('checked'));
});



$(document).on('click', '#AddForm, #EditForm', function(e){
	e.preventDefault();
    var form_id = 0;
    if($(this).attr('id') == 'EditForm'){
    	form_id = $('.FormID:checked').val();
    	if($('.FormID:checked').size() == 0){
    		$.toaster('Please select form to edit.', 'Edit Form', 'warning');
            return;
        }else if($('.FormID:checked').size() != 1){
    		$.toaster('Please select only one form to edit.', 'Edit Form', 'warning');
            return;
        }
    }
	$('#modal').modal('show');
	
	$.post(SITE_URL+"form/form-data/"+form_id, function(data){
		$('#modal-content').html(data);
		$("#ZoneID").select2();

		$('#Form').validate({
			rules:{
				FormName: {required: true, minlength: 2, lettersonly: true},
				FormCode: {required: true, minlength: 4, alphaspecial: true, remote: {
    		        url: SITE_URL+"form/check-form-name",
    		        type: "post",
    		        data: {
    		        	FormID: $('#FormID').val(),
    		        	FormCode: function() {
    		        		return $( "#FormCode" ).val();
    		          },
    		        }
    		      }
				},
				FormTypeID: {required: true},
				ReasonForUpdate : {required: true}
			},
			messages:{
				FormName: {required: 'Please enter Form Name.', minlength: 'Form name must be minimum of 2 and maximum of 50 characters.'},
				FormCode: {required: 'Please enter Form Code.', minlength: 'Form must be minimum of 4 and maximum of 50 characters.', remote: 'Form Code already exists.'},
				FormTypeID: {required: 'Please select Form Type.'},
				ReasonForUpdate : {required: 'Please enter reason.'}
			},
			submitHandler: function(form) {
				$('#FormSubmit').prop('disabled', true);
	   			$('#Form').ajaxSubmit({url: SITE_URL + "form/save", success: function(a, b, c, d){
	   				data = $.parseJSON(a);
	   				if(data.status == 'success'){
	   					$.toaster(data.message, data.title, 'success');
	   					$('#modal').modal('hide');
	   					getForms();
	   				}else{
	   					$('#FormSubmit').prop('disabled', false);
	   					$.toaster(data.message, data.title, 'danger');
	   				}
	   			}});
	   			return false;
			}
		});
	});
});

$(document).on('click', '#DeleteForm', function(e){
	e.preventDefault();
	if($('.FormID:checked').size() != 1){
		$.toaster('Please select one Form to delete.', 'Delete Form', 'warning');
        return;
    }
	form_id = $('.FormID:checked').val();
	$.confirm({
	    title: 'Delete Form',
	    content: 'Do you really want to delete?',
	    buttons: {
	        confirm: function () {
	        	$('#modal').modal('show');
	    		$.post(SITE_URL+"form/delete-form/"+form_id, function(data){
	    			$('#modal-content').html(data);
	    			$('#DeleteFormData').validate({
	    				rules:{
	    					Reason: {required: true, validChar: true}
	    				},
	    				messages:{
	    					Reason: {required: 'Delete comment is required.', validChar: 'Delete Comment must be of 500 valid characters or less.'}
	    				},
	    				submitHandler: function(){
	    					$('#DeleteSubmit').prop('disabled', true);
	    					$('#DeleteFormData').ajaxSubmit({'url': SITE_URL +'form/delete', success: function(a, b, c, d){
	    						data = $.parseJSON(a);
	    						if(data.status == 'success'){
	    		   					$.toaster(data.message, data.title, 'success');
	    		   					$('#modal').modal('hide');
	    		   					getForms();
	    		   				}else{
	    		   					$('#DeleteSubmit').prop('disabled', false);
	    		   					$.toaster(data.message, data.title, 'danger');
	    		   				}
	    		   			}});
	    		   			return false;
	    				}
	    			});
	    		});
	        },
	        cancel: function () {
	        }
	    }
	});
});


$(document).on('click', '#PublishForm', function(e){
	e.preventDefault();
	if($('.FormID:checked').size() != 1){
		$.toaster('Please select one Form to publish.', 'Publish Form', 'warning');
        return;
    }
	form_id = $('.FormID:checked').val();
	var status = $('.FormID:checked').data('status');
	if($.trim(status.toLowerCase()) == "published"){
		$.toaster('This Form has already published', 'Publish Form', 'warning');
        return;
	}
	$.confirm({
	    title: 'Publish Form',
	    content: 'Do you really want to publish?',
	    buttons: {
	        confirm: function () {
	        	$.post(SITE_URL+"form/publish",{"FormID" : form_id}, function(a){
					data = $.parseJSON(a);
					if(data.status == 'success'){
	   					$.toaster(data.message, data.title, 'success');
	   					getForms();
	   				}else{
	   					$.toaster(data.message, data.title, 'danger');
	   				}
	   			});
	        },
	        cancel: function () {
	        }
	    }
	});
});

$(document).on('click', '#FormNameLink', function(){
	var form_id = $(this).data("fid");
	location.href = SITE_URL+"questionnaire/list/"+form_id;
});



