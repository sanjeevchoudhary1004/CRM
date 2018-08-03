<?php

/**
 * User file
 *
 * This file has User business logic
 *
 * @category User
 * @package	User
 * @version		0.1
 * @since		0.1
 */

/**
 * User class
 *
 * User
 *
 * @category User
 * @package User
 * @version Release: 0.1
 * @since 26.March.2018
 * @author Manikanta Pola
 */
class User extends SaralController
{

    /**
     * user model
     *
     * @var object
     */
    private $user_model;

    /**
     * zone model
     *
     * @var object
     */
    private $division_model;

    /**
     * construct
     */
    function __construct()
    {
        parent::__construct();
        $this->user_model = $this->loadModel("user/UserModel");
        $this->division_model = $this->loadModel("division/DivisionModel");
    }

    /**
     * is logged in
     */
    function loginCheck()
    {
        if (! $this->getSession('UserID')) {
            $this->redirect($this->siteURL() . 'user/login');
        }
    }

    /**
     * login form
     */
    function doLogin()
    {
        $data['page'] = 'Login';

        if ($this->getSession('UserID')) {
            $this->redirect($this->siteURL() . 'dashboard/view');
        }
        $data['view'] = 'user/login';
        $this->loadView('sign-in', $data);
    }

    /**
     * authentication
     */
    function doAuth()
    {
        try {
            $post = $this->getPostData();
            $result = $this->user_model->authenticate($post);

            $message = 'Logged in successfully';
            echo json_encode(array(
                'status' => 'success',
                'message' => $message,
                'redirect_url' => 'dashboard/view'
            ));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo json_encode(array(
                'status' => 'failure',
                'message' => $message
            ));
        }
    }

    /**
     * logout
     */
    function doLogout()
    {
        $this->removeSession('UserID');
        $this->removeSession('RoleID');
        $this->removeSession('Name');
        $this->redirect($this->siteURL() . 'user/login');
    }

    /**
     * User listing
     */
    function doList()
    {
        $this->loginCheck();
        // $this->getInstance('UAC')->canAccess(1);TODO: need to add access for users List.

        $data['js'] = 'users.js';
        $data['page'] = 'Users';
        $data['breadcrumb'] = array(
            array(
                'url' => '',
                'text' => 'Users',
                'active' => true
            )
        );
        $data['roles'] = $this->user_model->getUsersRoles();
        $data['view'] = 'user/list';
        $this->loadView('admin', $data);
    }

    /**
     * user data for datatable
     */
    function doData()
    {
        // $this->getInstance('UAC')->canAccess(1);TODO: need to add access for users List data.
        $post = $this->getPostData();
        $users = $this->user_model->getUsers($post);
        echo json_encode($users);
    }

    /**
     * form to add or edit
     */
    public function doForm()
    {
        $data['user_id'] = $user_id = $this->getParam(2);
        $data['user'] = array();
        if ($data['user_id']) {
            // $this->getInstance('UAC')->canAccess(3, true); TODO: need to add access for users List data.
            $data['user'] = $this->user_model->getUser($user_id);
            $data['selected_zones'] = $this->division_model->getSelectedZones($user_id);
            $data['user_zones'] = explode(',', $data['selected_zones']);
        } else {
            // $this->getInstance('UAC')->canAccess(2, true); TODO: need to add access for users List data.
            $data['selected_zones'] = array();
        }
        $data['roles'] = $this->user_model->getUsersRoles();
        $data['zones'] = $this->division_model->getZones();

        $data['view'] = 'user/form';
        $this->loadView('blank', $data);
    }

    /**
     * check email existence
     */
    public function doCheckEmail()
    {
        $post = $this->getPostData();
        if ($this->user_model->checkEmail($post)) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * check Login ID/Username existence
     */
    public function doCheckUsername()
    {
        $post = $this->getPostData();
        if ($this->user_model->checkUsername($post)) {
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

            $title = $post['UserID'] ? 'Edit User' : 'Add User';
            $this->user_model->saveUser($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => $post['UserID'] ? 'User updated successfully' : 'User added successfully',
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
     * delete user form
     */
    public function doDeleteForm()
    {
        // $this->getInstance('UAC')->canAccess(4);

        // $post = $this->getPostData();
        $data['user_id'] = $user_id = $this->getParam(2);
        $user = $this->user_model->getUser($user_id);
        $data['user_name'] = $user['FirstName'] . ' ' . $user['LastName'];
        $data['view'] = 'user/delete';
        $this->loadView('blank', $data);
    }

    /**
     * delete user
     */
    public function doDelete()
    {
        try {
            // $this->getInstance('UAC')->canAccess(4);

            $post = $this->getPostData();
            $title = 'Delete User';
            $this->user_model->deleteUser($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Admin user "' . $post['Name'] . '" is deleted.',
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
     * reset password
     */
    public function doResetPassword()
    {
        try {
            // $this->getInstance('UAC')->canAccess(5);

            $post = $this->getPostData();
            $user_id = $this->getParam(2);
            $title = 'Reset Password';
            $user = $this->user_model->saveResetPassword($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Password has been reset for user "' . $user['FirstName'] . "  " . $user['LastName'] . '".',
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
     * change password form
     */
    public function doChangePassword()
    {
        $data['user_id'] = $this->getSession('UserID');
        $data['view'] = 'user/change-password';
        $this->loadView('blank', $data);
    }

    /**
     * match old password
     */
    public function doCheckPassword()
    {
        $post = $this->getPostData();
        if ($this->user_model->checkPassword($post)) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /**
     * update self password
     */
    public function doUpdatePassword()
    {
        try {
            $post = $this->getPostData();
            $title = 'Update Password';
            $this->user_model->updatePassword($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Password changed successfully',
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
     * profile form
     */
    public function doProfile()
    {
        $data['user_id'] = $user_id = $this->getSession('UserID');
        $data['user'] = $this->user_model->getUser($user_id);
        $data['view'] = 'user/profile';
        $this->loadView('blank', $data);
    }

    /**
     * save profile
     */
    public function doSaveProfile()
    {
        try {
            $post = $this->getPostData();
            $title = 'Update Profile';
            $this->user_model->saveProfile($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Profile updated successfully',
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
     * This function will email to PaDiSys team for requesting user's Login Id.
     */
    public function doForgotLoginId()
    {
        try {
            $post = $this->getPostData();
            $title = 'Forgot Login ID';
            $this->user_model->forgotLoginID($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => $this->getErrorMessage(2011),
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
     * This function will check the login id exists.
     */
    public function doCheckLoginId()
    {
        try {
            $post = $this->getPostData();
            $title = 'Forgot Password';
            $user = $this->user_model->checkLoginID($post);
            echo json_encode(array(
                'status' => 'success',
                'message' => $this->getErrorMessage(2011),
                'user_id' => $user->UserID,
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
     * disable User form
     */
    public function doDisableForm()
    {
        // $this->getInstance('UAC')->canAccess(4);
        $data['user_id'] = $user_id = $this->getParam(2);
        $user = $this->user_model->getUser($user_id);
        $data['user_name'] = $user['FirstName'] . ' ' . $user['LastName'];
        $data['view'] = 'user/status-change';
        $this->loadView('blank', $data);
    }
    
    /**
     * enable disable Users
     */
    public function doEnableDisable()
    {
        try {
            // $this->getInstance('UAC')->canAccess(4);
            
            $post = $this->getPostData();
            $title = 'Disable User';
            $this->user_model->changeStatus($post);
            $user = $this->user_model->getUser($post['UserID']);
            $user_name = $user['FirstName'] . ' ' . $user['LastName'];
            $status = ($post['Status'] == 'Active') ? 'Enabled' : 'Disabled';
            echo json_encode(array(
                'status' => 'success',
                'message' => 'User "' . $user_name . '" is "' . $status . '".',
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
     * Import User Form
     */
    public function doImportUserForm()
    {
        $data['view'] = 'user/import-users';
        $this->loadView('blank', $data);
    }

    /**
     * Save Users XLS
     */
    public function doImportUserSave()
    {
        try {
            $post = $this->getPostData();
            $title = 'Import Users';
            if (isset($_FILES['UserXLS']['tmp_name']) && is_uploaded_file($_FILES['UserXLS']['tmp_name'])) {
                include ($this->getPluginPath() . "PHPExcel/IOFactory.php");
                $tmp = $_FILES['UserXLS']['tmp_name'];
                $name = $_FILES['UserXLS']['name'];

                $excel_reader = PHPExcel_IOFactory::createReaderForFile($tmp);

                $excel_reader->setReadDataOnly(true);

                $excel = $excel_reader->load($tmp);
                $highest_col = $excel->setActiveSheetIndex(0)->getHighestColumn();
                $d = $excel->getSheet(0)->toArray(null, true, true, true);
                array_shift($d);
                $this->user_model->saveXlsUser($d);
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Users added successfully',
                    'title' => $title
                ));
            }
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'failure',
                'message' => $e->getMessage(),
                'title' => $title
            ));
        }
    }
}