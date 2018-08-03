getDivisions();

//Getting Divisions Records From DataBase
function getDivisions(){
	divisions_table = $('#divisions-datatable').DataTable({
		"processing": true,
        "serverSide": true,
        "bDestroy": true,
	    "ajax" : {
	        "url" : SITE_URL + "division/data",
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

function divisionStatusUpdate(id,status){
	$.post(SITE_URL+"division/enable-disable",{'DivisionID': id,'Status':status}, function(a){
	data = $.parseJSON(a);
	if(data.status == 'success'){
			$.toaster(data.message, data.title, 'success');
			getDivisions();
		}else{
			$.toaster(data.message, data.title, 'danger');
		}
	});
}
//Validations Functionality
$(function(){
	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
		  return this.optional(element) || /^[a-z]+$/i.test(value);
	}, "Letters only please"); 
	
	//jQuery.validator.addMethod("numbersonly", function(value, element) {
		 // return this.optional(element) || /^[0-9]+$/i.test(value);
	//}, "Numbers only please");
	
	jQuery.validator.addMethod("validChar", function(value, element) {
		  return this.optional(element) || /^([1-zA-Z0-1@.\s]{1,500})$/i.test(value);
	}, "Valid chars only please"); 
	
	//Add & Edit Functionality
	$(document).on('click', '#AddDivision, #EditDivision', function(e){
		e.preventDefault();
        var division_id = 0;
        if($(this).attr('id') == 'EditDivision'){
        	//var division_id = $(this).data('division-id');
        	 division_id = $('.DivisionID:checked').val();
        	if($('.DivisionID:checked').size() == 0){
        		$.toaster('Please select Division to edit.', 'Edit divisions', 'warning');
                return;
            }else if($('.DivisionID:checked').size() != 1){
        		$.toaster('Please select only one Division to edit.', 'Edit divisions', 'warning');
                return;
            }
        }
		$('#modal').modal('show');
		$.post(SITE_URL+"division/form/"+division_id, function(data){
			$('#modal-content').html(data);
			$('#DivisionForm').validate({
				rules:{
					DivisionName: {required: true, minlength: 2, maxlength:50,  remote: {
						url: SITE_URL+"division/checkdivisionname",
	    		        type: "post",
	    		        data: {
	    		        	DivisionID: $('#DivisionID').val(),
	    		        	DivisionName: function() {
	    		        		return $("#DivisionName").val();
	    		          },
	    		        }
	    		      }
					},
					DivisionCode: {required: true, minlength: 2, remote: {
						url: SITE_URL+"division/checkdivisioncode",
	    		        type: "post",
	    		        data: {
	    		        	DivisionID: $('#DivisionID').val(),
	    		        	DivisionCode: function() {
	    		        		return $("#DivisionCode").val();
	    		          },
	    		        }
	    		      }
					},
					
					Reason: {required: true, validChar: true}
				},
				messages:{
					DivisionName: {required: 'Please enter Division Name.', minlength: 'Division Name must be minimum of 2 and maximum of 50 characters.', remote: 'Division Name already exists.'},
					DivisionCode: {required: 'Please enter Division Code.', minlength: 'Division Code must be minimum of 2 characters.', remote: 'Division Code already exists.'},
					Reason : {required: 'Please enter Reason', validChar: 'Reason must be of 500 valid characters or less.'}
				},
				submitHandler: function(form) {
					$('#DivisionSubmit').prop('disabled', true);
					$('#DivisionForm').ajaxSubmit({url: SITE_URL + "division/save", success: function(a, b, c, d){
		   				data = $.parseJSON(a);
		   				if(data.status == 'success'){
		   					$.toaster(data.message, data.title, 'success');
		   					$('#modal').modal('hide');
		   					getDivisions();
		   				}else{
		   					$('#DivisionSubmit').prop('disabled', false);
		   					$.toaster(data.message, data.title, 'danger');
		   				}
		   			}});
					return false;
				}
			});
		});
	});
	
	//SelectAll CheckBox Functionality
	$(document).on('click', '.DivisionID', function(e){
		if($('.DivisionID:enabled').size() == $('.DivisionID:checked:enabled').size()){
			$('#AllDivisions').prop('checked', true);
		}else{
			$('#AllDivisions').prop('checked', false);
		}
	});
	
	$(document).on('click', '#AllDivisions', function(){
		$('.DivisionID:enabled').prop('checked', $(this).prop('checked'));
	});
	
	//Active & Deactive Status Functionality
	$(document).on('click', '.division-toggle', function(){
		var division_id = $(this).data('division-id');
		var checked = $(this).prop('checked');
		var division_name = $(this).data('division-name');
		if(!checked){
			$.confirm({
	    	    title: 'Disable Division',
	    	    content: 'Do you really want to disable ?',
	    	    buttons: {
	    	    	confirm: function () {
	    	    		$('#modal').modal('show');
	    	    		$.post(SITE_URL+"division/disableform/"+division_id, function(data){
	    	    			$('#modal-content').html(data);
	    	    			$('#DisableDivisionForm').validate({
	    	    				rules:{
	    	    					Reason: {required: true, validChar: true}
	    	    				},
	    	    				messages:{
	    	    					Reason: {required: 'Disable comment is required.', validChar: 'Disable Comment must be of 500 valid characters or less.'}
	    	    				},
	    	    				submitHandler: function(){
	    	    					//block();
	    	    					$('#DisableSubmit').prop('disabled', true);
	    	    					$('#DisableDivisionForm').ajaxSubmit({'url': SITE_URL +'division/enable-disable', success: function(a, b, c, d){
	    	    						data = $.parseJSON(a);
	    	    						if(data.status == 'success'){
	    	    							//unblock();
	    	    		   					$.toaster(data.message, data.title, 'success');
	    	    		   					$('#modal').modal('hide');
	    	    		   					getDivisions();
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
	    	        	$("#toggle"+division_id).prop('checked', true);
	    	        }
	    	    }
	    	});
		}else{
			//block();
			$.post(SITE_URL+"division/enable-disable", {'Status' : 'Enable', 'DivisionID':division_id, 'DivisionName':division_name}, function(a){
    			data = $.parseJSON(a);
				if(data.status == 'success'){
					//unblock();
   					$.toaster(data.message, data.title, 'success');
   					getDivisions();
   				}else{
   					//unblock();
   					$.toaster(data.message, data.title, 'danger');
   				}
    		});
		}
	});
	//Delete Functionality
	$(document).on('click', '#DeleteDivision', function(e){
		e.preventDefault();
    	if($('.DivisionID:checked').size() != 1){
    		$.toaster('Please select one division to delete.', 'Delete Division', 'warning');
            return;
        }
    	jQuery.validator.addMethod("validChar", function(value, element) {
  		  return this.optional(element) || /^([1-zA-Z0-1@.\s]{1,500})$/i.test(value);
  	}, "Valid chars only please"); 
    	division_id = $('.DivisionID:checked').val();
    	$.confirm({
    		title: 'Delete Division',
    	    content: 'Do you really want to delete?',
    	    buttons: {
    	        confirm: function () {
    	        	$('#modal').modal('show');
    	    		$.post(SITE_URL+"division/delete-form/"+division_id, function(data){
    	    			$('#modal-content').html(data);
    	    			$('#DeleteDivisionForm').validate({
    	    				rules:{
    	    					Reason: {required: true, validChar: true}
    	    				},
    	    				messages:{
    	    					Reason: {required: 'Delete comment is required.', validChar: 'Delete Comment must be of 500 valid characters or less.'}
    	    				},
    	    				submitHandler: function(){
    	    					$('#DeleteSubmit').prop('disabled', true);
    	    					$('#DeleteDivisionForm').ajaxSubmit({'url': SITE_URL +'division/delete', success: function(a, b, c, d){
    	    						data = $.parseJSON(a);
    	    						if(data.status == 'success'){
    	    		   					$.toaster(data.message, data.title, 'success');
    	    		   					$('#modal').modal('hide');
    	    		   					getDivisions();
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
								getDivisions();
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