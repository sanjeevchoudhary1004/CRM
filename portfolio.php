<?php

/**
 * Portfolio file
 *
 * This file has Portfolio business logic
 *
 * @category Portfolio
 * @package	Portfolio
 * @version		0.1
 * @since		0.1
 */

/**
 * Portfolio class
 *
 * Portfolio
 *
 * @category Portfolio
 * @package Portfolio
 * @version Release: 0.1
 * @since 24.May.2018
 * @author Manikanta Pola
 */
class Portfolio extends SaralController
{

    /**
     * Portfolio model
     *
     * @var object
     */
    public $portfolio_modal;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->portfolio_modal = $this->loadModel("portfolio/PortfolioModel");
    }

    /**
     * Portfolios listing
     */
    function doList()
    {
        // $this->getInstance('UAC')->canAccess(1); TODO: need to add access for portfolios List.
        $data['js'] = 'portfolios.js';
        $data['page'] = 'Portfolios';
        $data['breadcrumb'] = array(
            array(
                'url' => '',
                'text' => 'Portfolios',
                'active' => true
            )
        );
        $data['view'] = 'portfolio/list';
        $this->loadView('admin', $data);
    }

    /**
     * Portfolio data for datatable
     */
    function doData()
    {
        // $this->getInstance('UAC')->canAccess(1); TODO: need to add access for Roles List.
        $post = $this->getPostData();
        $portfolios = $this->portfolio_modal->getList($post);
        echo json_encode($portfolios);
    }

    /**
     * Portfolio form to add
     */
    public function doForm()
    {
        $data['portfolio_id'] = $portfolio_id = $this->getParam(2);
        $data['user'] = array();
        $data['view'] = 'portfolio/form';
        $this->loadView('blank', $data);
    }

    /**
     * check Portfolio Code existence
     */
    public function doCheckCode()
    {
        $post = $this->getPostData();
        if ($this->portfolio_modal->checkCode($post)) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * check Portfolio Name existence
     */
    public function doCheckName()
    {
        $post = $this->getPostData();
        if ($this->portfolio_modal->checkName($post)) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * save/update user
     */
    public function doSave()
    {
        try {
            $post = $this->getPostData();
            // if ($post['UserID'])
            // $this->getInstance('UAC')->canAccess(3);
            // else
            // $this->getInstance('UAC')->canAccess(2);

            $title = $post['PortfolioID'] ? 'Edit Portfolio' : 'Add Portfolio';
            $this->portfolio_modal->savePortfolio($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => $post['PortfolioID'] ? 'Portfolio updated successfully' : 'Portfolio added successfully',
                'title' => $title
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage(),
                'title' => $title
            ));
        }
    }

    /**
     * Portfolio form to edit
     */
    public function doEdit()
    {
        $data['portfolio_id'] = $portfolio_id = $this->getParam(2);
        $data['user'] = array();
        if ($data['portfolio_id']) {
            // $this->getInstance('UAC')->canAccess(3, true); TODO: need to add access for users List data

            $data['js'] = array(
                'admin/portfolio-media.js',
                'core.js',
                'upload.js'
            );

            $data['page'] = 'Portfolios';
            $data['portfolio'] = $this->portfolio_modal->getPortfolio($portfolio_id);

            $data['breadcrumb'] = array(
                array(
                    'url' => $this->siteURL() . 'portfolio/list',
                    'text' => 'Portfolios',
                    'active' => true
                ),
                array(
                    'url' => '',
                    'text' => $data['portfolio']['PortfolioName'],
                    'active' => true
                )
            );

            $data['view'] = 'portfolio/edit';
            $this->loadView('admin', $data);
        }
    }

    public function doUpload()
    {
        $data['portfolio_id'] = $portfolio_id = $this->getParam(2);
        $data['user'] = array();
        $data['view'] = 'portfolio/upload';
        $this->loadView('blank', $data);
    }

    public function doSavePortfolioMedia()
    {
        try {
            $post = $this->getPostData();

            $title = 'Portfolio Media';

            $this->portfolio_modal->savePortfolioMedia($post);

            echo json_encode(array(
                'status' => 'success',
                'message' => 'document uploaded successfully',
                'title' => $title
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage(),
                'title' => $title
            ));
        }
    }

    /**
     * Portfolio Media data for datatable
     */
    function doMediaData()
    {
        // $this->getInstance('UAC')->canAccess(1); TODO: need to add access for Roles List.
        $post = $this->getPostData();
        $portfolio_media = $this->portfolio_modal->getMediaList($post);
        echo json_encode($portfolio_media);
    }

    /**
     * delete portfolio documents form
     */
    public function doDeletePortfolioForm()
    {
        // $this->getInstance('UAC')->canAccess(4);
        $data['portfolio_id'] = $portfolio_id = $this->getParam(2);
        $portfolio = $this->portfolio_modal->getPortfolio($portfolio_id);
        $data['portfolio_name'] = $portfolio['PortfolioName'];
        $data['view'] = 'portfolio/delete';
        $this->loadView('blank', $data);
    }

    /**
     * delete portfolio documents
     */
    public function doDeletePortfolio()
    {
        try {
            // $this->getInstance('UAC')->canAccess(4);

            $post = $this->getPostData();
            $title = 'Delete Portfolio';
            $this->portfolio_modal->deletePortfolio($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => "Portfolio " . $post['Name'] . " is deleted.",
                'title' => $title
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage(),
                'title' => $title
            ));
        }
    }

    /**
     * delete portfolio documents form
     */
    public function doDeleteMediaForm()
    {
        // $this->getInstance('UAC')->canAccess(4);
        $post = $this->getPostData();
        $data['media_ids'] = $post['MediaIDs'];
        $data['view'] = 'portfolio/delete-media';
        $this->loadView('blank', $data);
    }

    /**
     * delete portfolio documents
     */
    public function doDeleteMedia()
    {
        try {
            // $this->getInstance('UAC')->canAccess(4);

            $post = $this->getPostData();
            $title = 'Delete Portfolio Documents';
            $this->portfolio_modal->deleteMedia($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Portfolio document(s) is deleted.',
                'title' => $title
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage(),
                'title' => $title
            ));
        }
    }
}