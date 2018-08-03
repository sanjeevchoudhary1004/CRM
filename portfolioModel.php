<?php

/**
 * PortfolioModel file
 *
 * This file is the PortfolioModel
 *
 * @category Portfolio
 * @package	PortfolioModel
 * @version		0.1
 * @since		0.1
 */

/**
 * PortfolioModel class
 *
 * PortfolioModel
 *
 * @category Portfolio
 * @package PortfolioModel
 * @version Release: 0.1
 * @since 24.May.2018
 * @author Manikanta Pola
 */
class PortfolioModel extends SaralModel
{

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * sending roles data to datatable
     *
     * @param array $post
     * @return mixed[]
     */
    public function getList($post)
    {
        $dt = new DataTable();

        $columns = array(
            'PortfolioID',
            'PortfolioName',
            'PortfolioCode',
            'Description'
        );

        $cond = " WHERE Status ='Active' ";

        $clauses = $dt->getClauses($columns, $post);

        $qry = "SELECT SQL_CALC_FOUND_ROWS * FROM product_portfolios  $cond ";
        $qry .= $clauses['clauses'];
        $records = $this->getRecords($qry, $clauses['params'], true);

        $data = array();
        foreach ($records as $record) {

            $disabled = $record['Status'] == 'Deleted' ? 'disabled' : '';

            $checkbox_column = "<div class='checkbox checkbox-info'><input type='checkbox' name='PortfolioIDs[]' class='PortfolioID' id='P$record[PortfolioID]' value='$record[PortfolioID]' $disabled /><label for='P$record[PortfolioID]'>&nbsp;</label></div>";
            $portfolio_name = "<a href='javascript:void(0)' class='PortfolioName' data-pid='$record[PortfolioID]' >" . $record['PortfolioName'] . "</a>";
            $data[] = array(
                $checkbox_column,
                $portfolio_name,
                $record['PortfolioCode'],
                $record['Description']
            );
        }
        $total_records = $this->getOne('SELECT FOUND_ROWS()');
        return array(
            "draw" => intval($post['draw']),
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        );
    }

    /**
     * returns single portfolio info for given portfolio id
     *
     * @param integer $portfolio_id
     * @return array
     */
    function getPortfolio($portfolio_id)
    {
        return $this->getRecord("SELECT * FROM product_portfolios WHERE PortfolioID = ?", array(
            $portfolio_id
        ), true);
    }

    /**
     * check Potfolio Code
     *
     * @param array $post
     * @return mixed
     */
    function checkCode($post)
    {
        if ($post['PortfolioID']) {
            return $this->getOne("SELECT COUNT(1) FROM product_portfolios WHERE PortfolioCode = ? AND PortfolioID <> ?", array(
                $post['PortfolioCode'],
                $post['PortfolioID']
            ));
        } else {
            return $this->getOne('SELECT COUNT(1) FROM product_portfolios WHERE PortfolioCode = ?', array(
                $post['PortfolioCode']
            ));
        }
    }

    /**
     * check Potfolio Name
     *
     * @param array $post
     * @return mixed
     */
    function checkName($post)
    {
        if ($post['PortfolioID']) {
            return $this->getOne("SELECT COUNT(1) FROM product_portfolios WHERE PortfolioName = ? AND PortfolioID <> ?", array(
                $post['PortfolioName'],
                $post['PortfolioID']
            ));
        } else {
            return $this->getOne('SELECT COUNT(1) FROM product_portfolios WHERE PortfolioName = ?', array(
                $post['PortfolioName']
            ));
        }
    }

    /**
     * save/update Portfolio
     *
     * @param array $post
     * @throws Exception
     */
    function savePortfolio($post)
    {
        try {
            $this->start();
            $logged_in_user_id = $this->getSession('UserID');
            $now = date('Y-m-d H:i:s');

            $params = array(
                'PortfolioName' => $post['PortfolioName'],
                'PortfolioCode' => $post['PortfolioCode'],
                'Description' => $post['Description']
            );

            if ($post['PortfolioID']) {
                $params['UpdatedBy'] = $logged_in_user_id;
                $params['UpdatedOn'] = $now;
                $this->updateRecord('product_portfolios', $params, array(
                    'PortfolioID' => $post['PortfolioID']
                ), array(), $post['ReasonForUpdate']);
            } else {
                $params['CreatedBy'] = $logged_in_user_id;
                $params['CreatedOn'] = $now;
                $this->insertRecord('product_portfolios', $params);
            }
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    function savePortfolioMedia($post)
    {
        try {
            $this->start();
            $logged_in_user_id = $this->getSession('UserID');
            $now = date('Y-m-d H:i:s');

            $portfolio_id = $post['PortfolioID'];

            $portfolio_media_url = $this->getRootPath() . 'media/portfolios';

            $folder_path = $portfolio_media_url . '/' . $portfolio_id;

            if (! file_exists($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $f = $_FILES["file"];
            $file = $f["name"];

            $file_name = date('ymdHis') . '_' . basename($_FILES["file"]["name"]);
            $target_file = $folder_path . '/' . $file_name;

            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $db_file_name = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
            $db_upload_path = $portfolio_id . '/' . $file_name;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $media_params = array(
                    'PortfolioID' => $portfolio_id,
                    'FileName' => $db_file_name,
                    'FileType' => $file_type,
                    'UploadPath' => $db_upload_path,
                    'CreatedBy' => $logged_in_user_id,
                    'CreatedOn' => $now,
                    'Status' => 'Active'
                );
                $this->insertRecord('portfolio_documents', $media_params);
            } else {
                $this->undo();
                throw $e;
            }

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * sending media data to datatable
     *
     * @param array $post
     * @return mixed[]
     */
    public function getMediaList($post)
    {
        $dt = new DataTable();

        $columns = array(
            'DocumentID',
            'FileName',
            'FileType',
            'UploadPath'
        );

        $cond = " WHERE Status ='Active' ";
        if (isset($post['PortfolioID']) && $post['PortfolioID'] != "") {
            $cond .= " AND PortfolioID =  " . $post['PortfolioID'] . " ";
        }

        $clauses = $dt->getClauses($columns, $post);

        $qry = "SELECT SQL_CALC_FOUND_ROWS * FROM portfolio_documents  $cond ";
        $qry .= $clauses['clauses'];
        $records = $this->getRecords($qry, $clauses['params'], true);

        $data = array();
        foreach ($records as $record) {

            $disabled = $record['Status'] == 'Deleted' ? 'disabled' : '';

            $checkbox_column = "<div class='checkbox checkbox-info'><input type='checkbox' name='MediaIDs[]' class='MediaID' id='M$record[DocumentID]' value='$record[DocumentID]' $disabled /><label for='P$record[DocumentID]'>&nbsp;</label></div>";

            $data[] = array(
                $checkbox_column,
                $record['FileName'],
                $record['FileType'],
                $record['UploadPath']
            );
        }
        $total_records = $this->getOne('SELECT FOUND_ROWS()');
        return array(
            "draw" => intval($post['draw']),
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        );
    }

    /**
     * delete portfolio
     *
     * @param array $post
     * @throws Exception
     */
    function deletePortfolio($post)
    {
        try {
            $this->start();
            $this->updateRecord('product_portfolios', array(
                'Status' => 'Deleted'
            ), array(
                'PortfolioID' => $post['PortfolioID']
            ), array(), $post['Reason']);
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * delete portfolio documents(media)
     *
     * @param array $post
     * @throws Exception
     */
    function deleteMedia($post)
    {
        try {
            $this->start();
            $portfolio_document_ids = explode(",", $post['MediaIDs']);
            foreach ($portfolio_document_ids as $portfolio_document_id) {
                $this->updateRecord('portfolio_documents', array(
                    'Status' => 'Deleted'
                ), array(
                    'DocumentID' => $portfolio_document_id
                ), array(), $post['Reason']);
            }
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }
}