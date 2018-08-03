<form name='ImportUserForm' method='post' action='' id='ImportUserForm'>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-title">Import User</h4>
	</div>
	<div class="modal-body">
		<div class='form-group'>
			<label>Select File</label>
			<input type='file' name='UserXLS' id='UserXLS' class='form-control' />
			<label for="File" class="error" id='file-err' style='display: none'></label>
		</div>
		<div class="progress-group" style='display: none'>
			<span class="progress-text">Upload Progress</span> <span
				class="progress-number">0%</span>
			<div class="progress sm">
				<div class="progress-bar progress-bar-aqua" style="width: 0%"></div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default pull-left" id="cancel-btn" data-dismiss="modal">Cancel</button>
		<button type="submit" class="btn btn-success" name="SubmitXLS" id="SubmitXLS">Upload</button>
		<button type="button" class="btn btn-default hide" id="close-btn" data-dismiss="modal">Close</button>
	</div>
</form>