<form role="form" name="ResetPasswordForm" id="ResetPasswordForm" method="post">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close"
            type="button"
        >&times;</button>
        <h4 class="modal-title">Reset Password for <?php
        echo $user_name;
        ?></h4>
    </div>
    <div class="modal-body">
        <div class='row'>
        	<div class='col-md-6 col-sm-12 col-xs-12'>
        		<div class='form-group'>
        			<label for='Password'>Password <span class='required'>*</span></label>
        			<input type='password' class='form-control' id='Password' name='Password' />
        		</div>
        	</div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class='col-xs-6'>
                <button class='btn btn-default pull-left'
                    aria-hidden="true" data-dismiss="modal"
                >Close</button>
            </div>
            <div class='col-xs-6'>
                <input type='hidden' name='UserID' id='UserID'
                    value='<?php
                    
                    echo $user_id;
                    ?>'
                /> <input name="ResetPasswordSubmit" id="ResetPasswordSubmit"
                    class="btn btn-info" type="submit" value='Submit'
                >
            </div>
        </div>
    </div>
</form>