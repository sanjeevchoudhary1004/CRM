getRoles();

function getRoles(){
	roles_table = $('#roles-datatable').DataTable({
		"processing": true,
        "serverSide": true,
        "bDestroy": true,
	    "ajax" : {
	        "url" : SITE_URL + "role/data",
	        "type" : "post"
	    },
	    "dom" : 'tip',
	    "columnDefs" : [ {
	        "orderable" : false,
	        "targets" : [ 0 ]
	    } ],
	    "order" : [ [3, 'asc' ] ],
	});
}


$(function(){
	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
		  return this.optional(element) || /^[a-z]+$/i.test(value);
	}, "Letters only please"); 
	
	jQuery.validator.addMethod("numbersonly", function(value, element) {
		  return this.optional(element) || /^[0-9]+$/i.test(value);
	}, "Numbers only please");
	
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
	
	$(document).on('click', '#AddRole, #EditRole', function(e){
		e.preventDefault();
        var role_id = 0;
        if($(this).attr('id') == 'EditRole'){
        	role_id = $('.RoleID:checked').val();
        	if($('.RoleID:checked').size() == 0){
        		$.toaster('Please select Role to edit.', 'Edit Role', 'warning');
                return;
            }else if($('.RoleID:checked').size() != 1){
        		$.toaster('Please select only one Role to edit.', 'Edit Role', 'warning');
                return;
            }
        }
		$('#modal').modal('show');
		$.post(SITE_URL+"role/form/"+role_id, function(data){
			$('#modal-content').html(data);
			$('#RoleForm').validate({
				rules:{
					Title: {required: true, minlength: 2, maxlength:50,  remote: {
						url: SITE_URL+"role/check-role-title",
	    		        type: "post",
	    		        data: {
	    		        	RoleID: $('#RoleID').val(),
	    		        	Title: function() {
	    		        		return $("#Title").val();
	    		          },
	    		        }
	    		      }
					},
					RoleCode: {required: true, minlength: 2, remote: {
						url: SITE_URL+"role/check-role-code",
	    		        type: "post",
	    		        data: {
	    		        	RoleID: $('#RoleID').val(),
	    		        	RoleCode: function() {
	    		        		return $("#RoleCode").val();
	    		          },
	    		        }
	    		      }
					},
					RoleOrder: {required: true, numbersonly: true, remote: {
	    		        url: SITE_URL+"role/check-role-order",
	    		        type: "post",
	    		        data: {
	    		        	RoleID: $('#RoleID').val(),
	    		        	RoleOrder: function() {
	    		        		return $("#RoleOrder").val();
	    		          },
	    		        }
	    		      }
					},
					Reason: {required: true, validChar: true}
				},
				messages:{
					Title: {required: 'Please enter Title.', minlength: 'Role Title must be minimum of 2 and maximum of 50 characters.', remote: 'Role Title already exists.'},
					RoleCode: {required: 'Please enter Role Code.', minlength: 'Role Code must be minimum of 2 characters.', remote: 'Role Code already exists.'},
					RoleOrder: {required: 'Please enter Role Order.', remote: 'Role Order already exists.'},
					Reason : {required: 'Please enter Reason', validChar: 'Reason must be of 500 valid characters or less.'}
				},
				submitHandler: function(form) {
					block();
					$('#RoleSubmit').prop('disabled', true);
					$('#RoleForm').ajaxSubmit({url: SITE_URL + "role/save", success: function(a, b, c, d){
		   				data = $.parseJSON(a);
		   				if(data.status == 'success'){
		   					unblock();
		   					$.toaster(data.message, data.title, 'success');
		   					$('#modal').modal('hide');
		   					getRoles();
		   				}else{
		   					unblock();
		   					$('#RoleSubmit').prop('disabled', false);
		   					$.toaster(data.message, data.title, 'danger');
		   				}
		   			}});
					return false;
				}
			});
		});
	});
	
	$(document).on('click', '.RoleID', function(e){
		if($('.RoleID:enabled').size() == $('.RoleID:checked:enabled').size()){
			$('#AllRoles').prop('checked', true);
		}else{
			$('#AllRoles').prop('checked', false);
		}
	});
	
	$(document).on('click', '#AllRoles', function(){
		$('.RoleID:enabled').prop('checked', $(this).prop('checked'));
	});
	
	$(document).on('click', '.role-toggle', function(){
		var role_id = $(this).data('role-id');
		var checked = $(this).prop('checked');
		var role_title = $(this).data('role-title');
		if(!checked){
			$.confirm({
	    	    title: 'Disable Role',
	    	    content: 'Do you really want to disable ?',
	    	    buttons: {
	    	    	confirm: function () {
	    	    		$('#modal').modal('show');
	    	    		$.post(SITE_URL+"role/disable-form/"+role_id, function(data){
	    	    			$('#modal-content').html(data);
	    	    			$('#DisableRoleForm').validate({
	    	    				rules:{
	    	    					Reason: {required: true, validChar: true}
	    	    				},
	    	    				messages:{
	    	    					Reason: {required: 'Disable comment is required.', validChar: 'Disable Comment must be of 500 valid characters or less.'}
	    	    				},
	    	    				submitHandler: function(){
	    	    					block();
	    	    					$('#DisableSubmit').prop('disabled', true);
	    	    					$('#DisableRoleForm').ajaxSubmit({'url': SITE_URL +'role/status-change', success: function(a, b, c, d){
	    	    						data = $.parseJSON(a);
	    	    						if(data.status == 'success'){
	    	    							unblock();
	    	    		   					$.toaster(data.message, data.title, 'success');
	    	    		   					$('#modal').modal('hide');
	    	    		   					getRoles();
	    	    		   				}else{
	    	    		   					unblock();
	    	    		   					$('#DisableSubmit').prop('disabled', false);
	    	    		   					$.toaster(data.message, data.title, 'danger');
	    	    		   				}
	    	    		   			}});
	    	    		   			return false;
	    	    				}
	    	    			});
	    	    		});
	    	        },
	    	        cancel: function () {
	    	        	$("#toggle"+role_id).prop('checked', true);
	    	        }
	    	    }
	    	});
		}else{
			block();
			$.post(SITE_URL+"role/status-change/", {'Status' : 'Enable', 'RoleID': role_id, 'Title':role_title}, function(a){
    			data = $.parseJSON(a);
				if(data.status == 'success'){
					unblock();
   					$.toaster(data.message, data.title, 'success');
   					getRoles();
   				}else{
   					unblock();
   					$.toaster(data.message, data.title, 'danger');
   				}
    		});
		}
	});
	
	$(document).on('click','#DisableCloseBtn', function(e){
		getRoles();
	});
	
	
	$(document).on('click', '#DeleteRole', function(e){
		e.preventDefault();
    	if($('.RoleID:checked').size() != 1){
    		$.toaster('Please select one role to delete.', 'Delete Role', 'warning');
            return;
        }
    	role_id = $('.RoleID:checked').val();
    	$.confirm({
    	    title: 'Delete Role',
    	    content: 'Do you really want to delete?',
    	    buttons: {
    	        confirm: function () {
    	        	$('#modal').modal('show');
    	    		$.post(SITE_URL+"role/delete-form/"+role_id, function(data){
    	    			$('#modal-content').html(data);
    	    			$('#DeleteRoleForm').validate({
    	    				rules:{
    	    					Reason: {required: true, validChar: true}
    	    				},
    	    				messages:{
    	    					Reason: {required: 'Delete comment is required.', validChar: 'Delete Comment must be of 500 valid characters or less.'}
    	    				},
    	    				submitHandler: function(){
    	    					block();
    	    					$('#DeleteSubmit').prop('disabled', true);
    	    					$('#DeleteRoleForm').ajaxSubmit({'url': SITE_URL +'role/delete-role', success: function(a, b, c, d){
    	    						data = $.parseJSON(a);
    	    						if(data.status == 'success'){
    	    							unblock();
    	    		   					$.toaster(data.message, data.title, 'success');
    	    		   					$('#modal').modal('hide');
    	    		   					getRoles();
    	    		   				}else{
    	    		   					unblock();
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

	
});