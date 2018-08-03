<form role="form" name="DisableUserForm" id="DisableUserForm" method="post">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button" >&times;</button>
        <h4 class="modal-title">Disable User '<?php
        echo $user_name;;
        ?>'</h4>
    </div>
    <div class="modal-body">
        <div class='row'>
        	<div class='col-md-8 col-sm-12 col-xs-12'>
        		<div class='form-group'>
        			<label for='Reason'>Reason for disable <span class='required'>*</span></label>
        			<textarea class='form-control' id='Reason' name='Reason' maxlength='500' ></textarea>
        		</div>
        	</div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class='col-xs-6'>
                <button class='btn btn-default pull-left' id="DisableCloseBtn" data-dismiss="modal" >Close</button>
            </div>
            <div class='col-xs-6'>
                <input type='hidden' name='Status' id='Status' value='Disable' />
            	<input type='hidden' name='Title' id='Title' value='<?php

            echo $data['title'];
            ?>' />
                <input type='hidden' name='UserID' id='UserID' value='<?php

                echo $user_id;
                ?>' />
                <input name="DisableSubmit" id="DisableSubmit" class="btn btn-danger" type="submit" value='Submit'>
            </div>
        </div>
    </div>
</form>