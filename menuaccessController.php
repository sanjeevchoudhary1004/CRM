<?php

class AccessControl extends SaralController
{

    private $access_control_model;

    function __construct()
    {
        parent::__construct();
        $this->access_control_model = $this->loadModel("menu/MenuAccessControlModel");
    }

    /**
     * This function will load the Access Modules view page
     */
    function doShow()
    {
        $data['js'] = array(
            'admin/menu-access.js'
        );
        $data['page'] = 'access';
        $data['breadcrumb'] = array(
            array(
                'url' => '',
                'text' => 'Access Modules',
                'active' => true
            )
        );
        $data['view'] = "menu/access-control";
        $am = $this->access_control_model->getAccessModules();
        $data['roles'] = $am['roles'];
        $data['access_modules'] = $am['access_modules'];

        $this->loadView("admin", $data);
    }

    /**
     * This function will send data to the modal
     */
    function doSave()
    {
        try {
            $post = $this->getPostData();
            $this->access_control_model->saveOne($post);

            $data = array(
                "status" => "success",
                "message" => "User Access Control updated succesfully"
            );
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage()
            ));
        }
        echo json_encode($data);
    }

    /**
     * This function will save all the selected roles permissions
     */
    function doSaveAll()
    {
        try {

            $post = $this->getPostData();
            $this->access_control_model->saveAll($post);

            $data = array(
                "status" => "success",
                "message" => "User Access Control updated succesfully"
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'failure',
                'message' => $e->getMessage()
            );
        }
        echo json_encode($data);
    }
}

