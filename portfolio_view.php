<style>
	#portfolios-datatable tr td:first-child{
		width:50px;
	}

	#portfolios-datatable tr th:first-child .checkbox{
		margin-left:5px;
	}
</style>
<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class='row'>
                	<div class='col-md-3 col-sm-12'>
                	</div>
                    <div class='col-md-9 col-sm-12 text-right'>
						<input type='button' id='AddPortfolio' value='Add' class='btn btn-success'/>
                        <input type='button' id='DeletePortfolio' value='Delete' class='btn btn-danger'/>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-sm-12'>
                        <div class='clearfix'></div>
                        <table class="datatable table table-striped " id='portfolios-datatable'>
                            <thead>
                                <tr>
                                    <th width='20'><div class='checkbox checkbox-info'><input type='checkbox' name='AllPortfolios' class='AllPortfolios' id='AllPortfolios' value='0' /><label for='AllPortfolios'></label></div></th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Media</th>
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
<div aria-hidden="true" role="dialog" tabindex="-1" id="modal" class="modal fade modal-info" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" id='modal-content'></div>
    </div>
</div>
