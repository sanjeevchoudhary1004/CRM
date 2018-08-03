<style>
.form-group {
    margin-bottom: 15px !important;
}
</style>
<form role="form" name="PortfolioForm" id="PortfolioForm" method="post">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close"
            type="button"
        >&times;</button>
        <h4 class="modal-title"><?php
        echo $portfolio_id ? 'Update Portfolio' : 'Add Portfolio';
        ?></h4>
    </div>
    <div class="modal-body">
    	<div class='row'>
            <div class="col-md-12 col-sm-12 col-xs-12">
            	<div class='form-group'>
                    <label for="FirstName">Portfolio Name <span class="required">*</span></label>
                <?php
                $portfolio_name = (isset($portfolio['PortfolioName']) ? $portfolio['PortfolioName'] : '');
                echo <<<SARAL
                <input type="text" data-msg-remote="{$this->getErrorMessage(3002)}" class="form-control" id="PortfolioName" placeholder="Enter Portfolio Name" name="PortfolioName" maxlength='50' value="{$portfolio_name}" />
SARAL;
                ?>

               </div>
            </div>
       </div>
       <div class='row'>
            <div class="col-md-12 col-sm-12 col-xs-12">
            <div class='form-group'>
                    <label for="LoginID">Portfolio Code<span class="required">*</span></label>
                  <?php
                $portfolio_code = (isset($portfolio['PortfolioCode']) ? $portfolio['PortfolioCode'] : '');
                $readonly = (isset($portfolio['PortfolioCode']) ? 'readonly' : '');
                echo <<<SARAL
                <input type='text' data-msg-remote="{$this->getErrorMessage(3001)}"   class='form-control' id='PortfolioCode' name='PortfolioCode' placeholder='Enter Portfolio Code' maxlength='25' value='{$portfolio_code}' $readonly />
SARAL;
                ?>

                    </div>
            </div>

        </div>

        <div class='row'>
			<div class='col-md-12 col-xs-12 col-sm-12'>
				<div class='form-group'>
					<label for='PortfolioDescription'>Portfolio Description<span class='required'>*</span></label>
					<textarea class='form-control' rows="3" cols="3" name="Description" id="Description" maxlength="500" ></textarea>
				</div>
			</div>
		</div>

    </div>
    <div class="modal-footer">
        <div class="row">
            <div class='col-xs-6'>
                <button class='btn btn-default pull-left' aria-hidden="true" data-dismiss="modal">Close</button>
            </div>
            <div class='col-xs-6'>
                <input type='hidden' name='PortfolioID' id='PortfolioID' value='<?php

                echo $portfolio_id;
                ?>'/>
                <input name="PortfolioSubmit" id="PortfolioSubmit" class="btn btn-info" type="submit" value='Submit'>
            </div>
        </div>
    </div>
</form>