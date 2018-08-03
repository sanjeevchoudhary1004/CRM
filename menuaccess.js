$(function(){
		$('.SelectAll').click(function(){
			var role_id = $(this).data('role');
			var checked = $(this).prop('checked')?'Yes':'No';
			if(checked == 'Yes' && $('.access-role[data-role='+role_id+']').size() == $('.access-role[data-role='+role_id+']:checked').size()){

			}else{
				$('.access-role[data-role='+role_id+']').prop('checked', $(this).prop('checked'));
				$.post(SITE_URL + 'admin/access-control/save-all', {'RoleID': role_id, 'Checked': checked}, function(d){
					var data = $.parseJSON(d);
					if(data.status == 'success'){
						toastr.success(data.message, 'User Access Control');
					}else{
						toastr.error(data.message, 'User Access Control');
					}
				});
			}
		});

		$('.access-role').click(function(){
			var role_id = $(this).data('role');
			var module_id = $(this).data('module');
			var checked = $(this).prop('checked')?'Yes':'No';
			$.post(SITE_URL + 'admin/access-control/save', {'RoleID': role_id, 'Checked': checked, 'ModuleID': module_id }, function(d){
				var data = $.parseJSON(d);
				if(data.status == 'success'){
					toastr.success(data.message, 'User Access Control');
				}else{
					toastr.error(data.message, 'User Access Control');
				}
			});
		});
	});