<style>
	#portfolios-media-datatable tr td:first-child{
		width:50px;
	}

	#portfolios-media-datatable tr th:first-child .checkbox{
		margin-left:5px;
	}
	.form-group {
        margin-bottom: 15px !important;
    }
</style>
<form role="form" name="PortfolioForm" id="PortfolioForm" method="post">
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-body">
                    <div class='row'>
                    	<div class='col-md-3 col-sm-12'>
                    	</div>
                        <div class='col-md-9 col-sm-12 text-right'>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6 col-sm-12'>
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

                        <div class='col-md-6 col-sm-12'>
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
                    	<div class='col-md-6 col-xs-12 col-sm-12'>
            				<div class='form-group'>
            					<label for='PortfolioDescription'>Portfolio Description<span class='required'>*</span></label>
            					<?php

                $portfolio_description = (isset($portfolio['Description']) ? $portfolio['Description'] : '');
                echo <<<SARAL
                <textarea class='form-control' rows="3" cols="3" name="Description" id="Description" maxlength="500" >{$portfolio_description}</textarea>
SARAL;
                ?>
            				</div>
            			</div>

            			<div class='col-md-6 col-xs-12 col-sm-12'>
            				<div class='form-group'>
            					<label for='ReasonForUpdate'>Reason For Update<span class='required'>*</span></label>
								<textarea class='form-control' rows="3" cols="3" name="ReasonForUpdate" id="ReasonForUpdate" maxlength="500" ></textarea>
            				</div>
            			</div>


                    </div>

                    <div class="row">
                        <div class='col-xs-6'>

                        </div>
                        <div class='col-xs-6 text-right'>
                            <input type='hidden' name='PortfolioID' id='PortfolioID' value='<?php

                            echo $portfolio_id;
                            ?>'/>
                            <input name="PortfolioSubmit" id="PortfolioSubmit" class="btn btn-success psubmit" type="submit" value='Submit'>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</form>
<div aria-hidden="true" role="dialog" tabindex="-1" id="media-modal" class="modal fade modal-info" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id='media-modal-content'></div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class='row'>
                	<div class='col-md-3 col-sm-12'>
                		<h3 class="text-primary">Documents</h3>
                	</div>
                    <div class='col-md-9 col-sm-12 text-right'>
						<input type='button' id='UploadMedia' value='Add' class='btn btn-success'/>
                        <input type='button' id='DeleteMedia' value='Delete' class='btn btn-danger'/>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-sm-12'>
                        <div class='clearfix'></div>
                        <table class="datatable table table-striped " id='portfolios-media-datatable'>
                            <thead>
                                <tr>
                                    <th width='20'><div class='checkbox checkbox-info'><input type='checkbox' name='AllPortfolioMedias' class='AllPortfolioMedias' id='AllPortfolioMedias' value='0' /><label for='AllPortfolioMedias'></label></div></th>
                                    <th>Name</th>
                                    <th>Media Type</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

