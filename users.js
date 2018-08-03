getUsers();
function getUsers(){
	users_table = $('#users-datatable').DataTable({
		"processing": true,
        "serverSide": true,
        "bDestroy": true,
	    "ajax" : {
	        "url" : SITE_URL + "user/data",
	        "type" : "post",
	        "data":{
	        	'UserRole': $('#UserRole').val(),
	        	'UserStatus': $('#UserStatus').val()
	        }
	    },
	    "dom" : 'tip',
	    "columnDefs" : [ {
	        "orderable" : false,
	        "targets" : [ 0, 6 ]
	    } ],
	    "order" : [ [1, 'asc' ] ],
	});
	
	$('#Search').keyup(function(){
		users_table.search($(this).val()).draw() ;
	});
}


$(function(){
	
	$('#UserRole').change(function(){
		getUsers();
	});
	
	$('#UserStatus').change(function(){
		getUsers();
	});
	
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
	
	$(document).on('click', '#AddUser, #EditUser', function(e){
		e.preventDefault();
        var user_id = 0;
        if($(this).attr('id') == 'EditUser'){
        	user_id = $('.UserID:checked').val();
        	if($('.UserID:checked').size() == 0){
        		$.toaster('Please select user to edit.', 'Edit User', 'warning');
                return;
            }else if($('.UserID:checked').size() != 1){
        		$.toaster('Please select only one user to edit.', 'Edit User', 'warning');
                return;
            }
        }
		$('#modal').modal('show');
		
		$.post(SITE_URL+"user/form/"+user_id, function(data){
			$('#modal-content').html(data);
			$("#ZoneID").select2();

			$('#UserForm').validate({
				rules:{
					FirstName: {required: true, minlength: 2, lettersonly: true},
					LastName: {required: true, minlength: 2, lettersonly: true},
					Username: {required: true, minlength: 4, alphaspecial: true, remote: {
	    		        url: SITE_URL+"user/check-username",
	    		        type: "post",
	    		        data: {
	    		        	UserID: $('#UserID').val(),
	    		        	Username: function() {
	    		        		return $( "#Username" ).val();
	    		          },
	    		        }
	    		      }
					},
					EmailID: {required: true, customEmail: true, remote: {
	    		        url: SITE_URL+"user/check-email",
	    		        type: "post",
	    		        data: {
	    		        	UserID: $('#UserID').val(),
	    		        	EmailID: function() {
	    		        		return $( "#EmailID" ).val();
	    		          },
	    		        }
	    		      }
					},
					RoleID: {required: true},
					'ZoneID[]': {required: true},
					ReasonForUpdate : {required: true}
				},
				messages:{
					FirstName: {required: 'Please enter First Name.', minlength: 'First name must be minimum of 2 and maximum of 50 characters.'},
					LastName: {required: 'Please enter Last Name.', minlength: 'Last name must be minimum of 2 and maximum of 50 characters.'},
					Username: {required: 'Please enter User Name.', minlength: 'User name must be minimum of 4 and maximum of 15 characters.', remote: 'User Name already exists.'},
					EmailID: {required: 'Please enter User Email.', email: 'Please enter valid Email.', remote: 'User Email already exists.'},
					RoleID: {required: 'Please select Role.'},
					'ZoneID[]': {required: 'Please select Zone'},
					ReasonForUpdate : {required: 'Please enter reason.'}
				},
				submitHandler: function(form) {
					$('#UserSubmit').prop('disabled', true);
		   			$('#UserForm').ajaxSubmit({url: SITE_URL + "user/save", success: function(a, b, c, d){
		   				data = $.parseJSON(a);
		   				if(data.status == 'success'){
		   					$.toaster(data.message, data.title, 'success');
		   					$('#modal').modal('hide');
		   					getUsers();
		   				}else{
		   					$('#UserSubmit').prop('disabled', false);
		   					$.toaster(data.message, data.title, 'danger');
		   				}
		   			}});
	   			return false;
	   		}
			});
		});
	});
	
	$(document).on('click', '.UserID', function(e){
		if($('.UserID:enabled').size() == $('.UserID:checked:enabled').size()){
			$('#AllUsers').prop('checked', true);
		}else{
			$('#AllUsers').prop('checked', false);
		}
	});
	
	$(document).on('click', '#AllUsers', function(){
		$('.UserID:enabled').prop('checked', $(this).prop('checked'));
	});
	
	
	$(document).on('click', '#DeleteUser', function(e){
		e.preventDefault();
    	if($('.UserID:checked').size() != 1){
    		$.toaster('Please select one user to delete.', 'Delete User', 'warning');
            return;
        }
    	user_id = $('.UserID:checked').val();
    	$.confirm({
    	    title: 'Delete User',
    	    content: 'Do you really want to delete?',
    	    buttons: {
    	        confirm: function () {
    	        	$('#modal').modal('show');
    	    		$.post(SITE_URL+"user/delete-form/"+user_id, function(data){
    	    			$('#modal-content').html(data);
    	    			$('#DeleteUserForm').validate({
    	    				rules:{
    	    					Reason: {required: true, validChar: true}
    	    				},
    	    				messages:{
    	    					Reason: {required: 'Delete comment is required.', validChar: 'Delete Comment must be of 500 valid characters or less.'}
    	    				},
    	    				submitHandler: function(){
    	    					$('#DeleteSubmit').prop('disabled', true);
    	    					$('#DeleteUserForm').ajaxSubmit({'url': SITE_URL +'user/delete', success: function(a, b, c, d){
    	    						data = $.parseJSON(a);
    	    						if(data.status == 'success'){
    	    		   					$.toaster(data.message, data.title, 'success');
    	    		   					$('#modal').modal('hide');
    	    		   					getUsers();
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

	$(document).on('click', '#ResetPassword', function(e){
		e.preventDefault();
    	if($('.UserID:checked').size() != 1){
    		$.toaster('Please select one user to reset password.', 'Reset Password', 'warning');
            return;
        }
    	user_id = $('.UserID:checked').val();
    	$.confirm({
    		animation: 'none',
    	    title: 'Reset Password',
    	    content: 'Do you really want to reset password?',
    	    buttons: {
    	        confirm: function () {
    	        	block();
    	        	$.post(SITE_URL+"user/reset-password/"+user_id,{'UserID': user_id}, function(d){
    	        		data = $.parseJSON(d);
						if(data.status == 'success'){
							unblock();
		   					$.toaster(data.message, data.title, 'success');
		   				}else{
		   					unblock();
		   					$.toaster(data.message, data.title, 'danger');
		   				}
    	        	});
    	        },
    	        cancel: function () {
    	        }
    	    }
    	});
	});
	
	$(document).on('click', '.user-toggle', function(){
		var user_id = $(this).data('user-id');
		var checked = $(this).prop('checked');
		var user_last_name = $(this).data('last-name');
		if(!checked){
			$.confirm({
	    	    title: 'Disable User',
	    	    content: 'Do you really want to disable ?',
	    	    buttons: {
	    	    	confirm: function () {
	    	    		$('#modal').modal('show');
	    	    		$.post(SITE_URL+"user/disableform/"+user_id, function(data){
	    	    			$('#modal-content').html(data);
	    	    			$('#DisableUserForm').validate({
	    	    				rules:{
	    	    					Reason: {required: true, validChar: true}
	    	    				},
	    	    				messages:{
	    	    					Reason: {required: 'Disable comment is required.', validChar: 'Disable Comment must be of 500 valid characters or less.'}
	    	    				},
	    	    				submitHandler: function(){
	    	    					//block();
	    	    					$('#DisableSubmit').prop('disabled', true);
	    	    					$('#DisableUserForm').ajaxSubmit({'url': SITE_URL +'user/enable-disable', success: function(a, b, c, d){
	    	    						data = $.parseJSON(a);
	    	    						if(data.status == 'success'){
	    	    							//unblock();
	    	    		   					$.toaster(data.message, data.title, 'success');
	    	    		   					$('#modal').modal('hide');
	    	    		   					getUsers();
	    	    		   				}else{
	    	    		   					//unblock();
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
	    	        	$("#toggle"+user_id).prop('checked', true);
	    	        }
	    	    }
	    	});
		}else{
			//block();
			$.post(SITE_URL+"user/enable-disable", {'Status' : 'Active', 'UserID': user_id}, function(a){
    			data = $.parseJSON(a);
				if(data.status == 'success'){
					//unblock();
   					$.toaster(data.message, data.title, 'success');
   					getUsers();
   				}else{
   					//unblock();
   					$.toaster(data.message, data.title, 'danger');
   				}
    		});
		}
	});
	
	
	jQuery.validator.addMethod('ext', function(v, e){
		return this.optional(e) || /\.xlsx?$/i.test(v);
	}, 'Only xls/xlsx');
	
	$(document).on('click', '#ImportXLS', function(e){
		$('#modal').modal('show');
		$.post(SITE_URL+"user/import-user-form/", function(data){
			$('#modal-content').html(data);
			$('#ImportUserForm').validate({
				rules:{UserXLS: {required: true, ext: 'xlsx?'}},
				messages:{UserXLS: {required: 'Please select file', ext: 'Only xls/xlsx'}},
				submitHandler: function(f){
					$('#file-err').hide();
					$('#SubmitXLS').prop('disabled', true);
					$('#ImportUserForm').ajaxSubmit({
						size: 200 * 1024 * 1024,
						beforeSend: function() {
					        var percentVal = '0%';
					        $(document).find('.progress-bar').width(percentVal)
					        $(document).find('.progress-number').html(percentVal);
					    },
					    uploadProgress: function(event, position, total, percentComplete, failure) {
						    if(failure){
							    $('#file-err').html('File not accepted, size exceeded 200 MB.').show();
							    $('.progress-group').hide();
							    $('#SubmitXLS').prop('disabled', false);
						    }else{
						    	$('.progress-group').show();
						    	$('#cancel-btn').hide();
				 				$('#SubmitXLS').hide();
						    }
					        var percentVal = percentComplete + '%';
					        $(document).find('.progress-bar').width(percentVal)
					        $(document).find('.progress-number').html(percentVal);
					    },
						url: SITE_URL+"user/import-user-save",
						success: function(a){
							data = $.parseJSON(a);
							if(data.status == 'success'){
								getUsers();
								$('#modal').modal('hide');
						 		$.toaster({ title : data.title, priority : data.priority, message : data.message});
						 	}else if(data.status == 'failure'){
						 		$('.modal-body').html('');
						 		$('#cancel-btn').show();
						 		$('.modal-body').html(data.message);
						 	}
						}
					});
					return false;
				}
			});
		});
		
	});
	
	
});