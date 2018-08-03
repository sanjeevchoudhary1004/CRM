<style>
    .filelists {
        margin: 20px 0;
    }

    .filelists h5 {
        margin: 10px 0 0;
    }

    .filelists .cancel_all {
        color: red;
        cursor: pointer;
        clear: both;
        font-size: 10px;
        margin: 0;
        text-transform: uppercase;
    }

    .filelist {
        margin: 0;
        padding: 10px 0;
    }

    .filelist li {
        background: #fff;
        border-bottom: 1px solid #ECEFF1;
        font-size: 14px;
        list-style: none;
        padding: 5px;
        position: relative;
    }

    .filelist li:before {
        display: none !important;
    }
    /* main site demos */

    .filelist li .bar {
        background: #eceff1;
        content: '';
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 0;
        z-index: 0;
        -webkit-transition: width 0.1s linear;
        transition: width 0.1s linear;
    }

    .filelist li .content {
        display: block;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .filelist li .file {
        color: #455A64;
        float: left;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 50%;
        white-space: nowrap;
    }

    .filelist li .progress {
        color: #B0BEC5;
        display: block;
        float: right;
        font-size: 10px;
        text-transform: uppercase;
    }

    .filelist li .cancel {
        color: red;
        cursor: pointer;
        display: block;
        float: right;
        font-size: 10px;
        margin: 0 0 0 10px;
        text-transform: uppercase;
    }

    .filelist li.error .file {
        color: red;
    }

    .filelist li.error .progress {
        color: red;
    }

    .filelist li.error .cancel {
        display: none;
    }
</style>

<form role="form" name="PortfolioMediaForm" id="PortfolioMediaForm" method="post" enctype="multipart/form-data">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h4 class="modal-title">Media</h4>
    </div>
    <div class="modal-body">
		<div class="upload"></div>
    	<div class="filelists">
            <h5>Complete</h5>
            <ol class="filelist complete">
            </ol>
            <h5>Queued</h5>
            <ol class="filelist queue">
            </ol>
            <span class="cancel_all">Clear All</span>
        </div>
    </div>
    <div class="modal-footer">
        <div class="row">
            <div class='col-xs-6'>
            </div>
            <div class='col-xs-6'>
                <input type='hidden' name='PortfolioID' id='PortfolioID' value='<?php
                echo $portfolio_id;
                ?>'/>
                <button tyep="submit" class='btn btn-warning' aria-hidden="true" id="MediaModalClose">Close</button>
            </div>
        </div>
    </div>
</form>
