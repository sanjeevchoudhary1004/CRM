getDoctors();

function getDoctors(){
	doctors_table = $('#doctors-datatable').DataTable({
		"processing": true,
        "serverSide": true,
        "bDestroy": true,
	    "ajax" : {
	        "url" : SITE_URL + "doctor/data",
	        "type" : "post",
	        "data":{ }
	    },
	    "dom" : 'tip',
	    "columnDefs" : [ {
	        "orderable" : false,
	        "targets" : [ 0,7 ]
	    } ],
	    "order" : [ [1, 'asc' ] ],
	});
	
	$('#Search').keyup(function(){
		doctors_table.search($(this).val()).draw() ;
	});
	
}

function doctorStatusUpdate(id,status){
		$.post(SITE_URL+"doctor/enable-disable",{'DoctorID': id,'Status':status}, function(a){
		data = $.parseJSON(a);
		if(data.status == 'success'){
				$.toaster(data.message, data.title, 'success');
				getDoctors();
			}else{
				$.toaster(data.message, data.title, 'danger');
			}
		});
}


$(function(){	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
		  return this.optional(element) || /^[a-z]+$/i.test(value);
	}, "Letters only please"); 
	
	jQuery.validator.addMethod("validChar", function(value, element) {
		  return this.optional(element) || /^([1-zA-Z0-1@.\s]{1,500})$/i.test(value);
	}, "Valid chars only please"); 
	
	//Custome Email Validation
	$.validator.addMethod("customEmail", function(value, element) {
		return /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value);
	},"Please enter valid Email.");
	//Custome Email Validation ENDS
	
	$(document).on('click', '#AddDoctor, #EditDoctor', function(e){
		e.preventDefault();
        var doctor_id = 0;
        if($(this).attr('id') == 'EditDoctor'){
        	doctor_id = $('.DoctorID:checked').val();
        	if($('.DoctorID:checked').size() == 0){
        		$.toaster('Please select doctor to edit.', 'Edit Doctor', 'warning');
                return;
            }else if($('.DoctorID:checked').size() != 1){
        		$.toaster('Please select only one doctor to edit.', 'Edit Doctor', 'warning');
                return;
            }
        }
		$('#modal').modal('show');
		$.post(SITE_URL+"doctor/form/"+doctor_id, function(data){
			$('#modal-content').html(data);
			$('#DoctorForm').validate({
				rules:{
					DoctorCode: {required: true, remote: {
	    		        url: SITE_URL+"doctor/check-doctor-code",
	    		        type: "post",
	    		        data: {
	    		        	DoctorID: $('#DoctorID').val(),
	    		        	DoctorCode: function() {
	    		        		return $( "#DoctorCode" ).val();
	    		          },
	    		        }
	    		      }},
					FirstName: {required: true, minlength: 2, lettersonly: true},
					LastName: {required: true, minlength: 2, lettersonly: true},
					HospitalName: {required: true},
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
					ContactPhone: {required: true, digits: true, minlength: 10},
					Address: {required: true},
					ReasonForUpdate:{required: true}
				},
				messages:{
					DoctorCode: {required: 'Please enter Doctor Code', remote:'Doctor Code is already exists.'},
					FirstName: {required: 'Please enter First Name.', minlength: 'First name must be minimum of 2 and maximum of 50 characters.'},
					LastName: {required: 'Please enter Last Name.', minlength: 'Last name must be minimum of 2 and maximum of 50 characters.'},
					HospitalName: {required: 'Please enter Hospital Name.'},
					EmailID: {required: 'Please enter User Email.', email: 'Please enter valid Email.'},
					ContactPhone: {required: 'Please enter Contact Number.',digits: 'Contact number allows only numbers.', minlength: 'Contact number atleast 10 digits'},
					Address: {required: 'Please enter Address.'},
					ReasonForUpdate: {required: 'Please enter Resaon For Update.'}
				},
				submitHandler: function(form) {
				$('#DoctorSubmit').prop('disabled', true);
	   			$('#DoctorForm').ajaxSubmit({url: SITE_URL + "doctor/save", success: function(a, b, c, d){
	   				data = $.parseJSON(a);
	   				if(data.status == 'success'){
	   					$.toaster(data.message, data.title, 'success');
	   					$('#modal').modal('hide');
	   					getDoctors();
	   				}else{
	   					$('#DoctorForm').prop('disabled', false);
	   					$.toaster(data.message, data.title, 'danger');
	   				}
	   			}});
	   			return false;
	   		  }
			});
		});
	});
	
	$(document).on('click', '.DoctorID', function(e){
		if($('.DoctorID:enabled').size() == $('.DoctorID:checked:enabled').size()){
			$('#AllDoctors').prop('checked', true);
		}else{
			$('#AllDoctors').prop('checked', false);
		}
	});
	
	$(document).on('click', '#AllDoctors', function(){
		$('.DoctorID:enabled').prop('checked', $(this).prop('checked'));
	});
	
	$(document).on('change','.status-switch',function() {
		var thischeckbox = $(this).prop('checked');
		var doctor_id = $(this).data('doctor-id');
		var status = 'Disabled';
		if(thischeckbox==true){
			status = 'Active';
			doctorStatusUpdate(doctor_id,status);
		}else{
	    	$.confirm({
	    	    title: 'Disable Doctor',
	    	    content: 'Do you really want to disable?',
	    	    buttons: {
	    	        confirm: function () {
	    	        	$('#modal').modal('show');
	    	    		$.post(SITE_URL+"doctor/disable-form/"+doctor_id, function(data){
	    	    			$('#modal-content').html(data);
	    	    			$('#DisableDoctorForm').validate({
	    	    				rules:{
	    	    					Reason: {required: true, validChar: true}
	    	    				},
	    	    				messages:{
	    	    				    Reason: {required: 'Disable comment is required.', validChar: 'Disable Comment must be of 500 valid characters or less.'}
	    	    				},
	    	    				submitHandler: function(){
	    	    					$('#DisableSubmit').prop('disabled', true);
	    	    					$('#DisableDoctorForm').ajaxSubmit({'url': SITE_URL +'doctor/enable-disable', success: function(a, b, c, d){
	    	    						data = $.parseJSON(a);
	    	    						if(data.status == 'success'){
	    	    		   					$.toaster(data.message, data.title, 'success');
	    	    		   					$('#modal').modal('hide');
	    	    		   					getDoctors();
	    	    		   				}else{
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
	    	        	$("#toggle"+doctor_id).prop('checked', true);
	    	        }
	    	    }
	    	});
		}
	});
	
	
	$(document).on('click', '#DeleteDoctor', function(e){
		e.preventDefault();
    	if($('.DoctorID:checked').size() != 1){
    		$.toaster('Please select one doctor to delete.', 'Delete Doctor', 'warning');
            return;
        }
    	jQuery.validator.addMethod("validChar", function(value, element) {
  		  return this.optional(element) || /^([1-zA-Z0-1@.\s]{1,500})$/i.test(value);
  	}, "Valid chars only please"); 
    	doctor_id = $('.DoctorID:checked').val();
    	$.confirm({
    	    title: 'Delete Doctor',
    	    content: 'Do you really want to delete?',
    	    buttons: {
    	        confirm: function () {
    	        	$('#modal').modal('show');
    	    		$.post(SITE_URL+"doctor/delete-form/"+doctor_id, function(data){
    	    			$('#modal-content').html(data);
    	    			$('#DeleteDoctorForm').validate({
    	    				rules:{
    	    					Reason: {required: true, validChar: true}
    	    				},
    	    				messages:{
    	    					Reason: {required: 'Delete comment is required.', validChar: 'Delete Comment must be of 500 valid characters or less.'}
    	    				},
    	    				submitHandler: function(){
    	    					$('#DeleteSubmit').prop('disabled', true);
    	    					$('#DeleteDoctorForm').ajaxSubmit({'url': SITE_URL +'doctor/delete', success: function(a, b, c, d){
    	    						data = $.parseJSON(a);
    	    						if(data.status == 'success'){
    	    		   					$.toaster(data.message, data.title, 'success');
    	    		   					$('#modal').modal('hide');
    	    		   					getDoctors();
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
	
	jQuery.validator.addMethod('ext', function(v, e){
		return this.optional(e) || /\.xlsx?$/i.test(v);
	}, 'Only xls/xlsx');
	
	$(document).on('click', '#ImportDoctorXLS', function(e){
		$('#modal').modal('show');
		$.post(SITE_URL+"doctor/import-doctor-form/", function(data){
			$('#modal-content').html(data);
			$('#ImportDoctorForm').validate({
				rules:{DoctorXLS: {required: true, ext: 'xlsx?'}},
				messages:{DoctorXLS: {required: 'Please select file', ext: 'Only xls/xlsx'}},
				submitHandler: function(f){
					$('#file-err').hide();
					$('#SubmitXLS').prop('disabled', true);
					$('#ImportDoctorForm').ajaxSubmit({
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
						url: SITE_URL+"doctor/import-doctor-save",
						success: function(a){
							data = $.parseJSON(a);
							if(data.status == 'success'){
								getDoctors();
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