<?php

/**
 * UserModel file
 *
 * This file is the user model
 *
 * @category User
 * @package	UserModel
 * @version		0.1
 * @since		0.1
 */

/**
 * UserModel class
 *
 * User Model
 *
 * @category User
 * @package UserModel
 * @version Release: 0.1
 * @since 26.March.2018
 * @author Manikanta Pola
 */
class UserModel extends SaralModel
{

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * authentication
     *
     * @param array $post
     * @throws Exception
     * @return array
     */
    public function authenticate($post)
    {
        $user = $this->getRecord("SELECT * FROM users WHERE Username = ?", array(
            $post['Username']
        ));
        if (count($user)) {
            $user_id = $user->UserID;
            $now = date('Y-m-d H:i:s');
            $role_id = $user->RoleID;
            if ($user->Status == 'Active') { // if user is active or paused
                if ($this->verifyPassword($post['Password'], $user->Password)) { // valid password

                    $this->setSession("UserID", $user_id);
                    $this->setSession("RoleID", $user->RoleID);
                    $this->setSession("Name", $user->FirstName . ' ' . $user->LastName);
                } else { // password incorrect
                    $msg = $this->getErrorMessage(2003);
                    throw new Exception($msg);
                }
            } else { // user inactive or locked
                $msg = $this->getErrorMessage(2005);
                throw new Exception($msg);
            }
        } else {
            throw new Exception($this->getErrorMessage(2002));
        }
    }

    /**
     * sending users data to datatable
     *
     * @param array $post
     * @return mixed[]
     */
    function getUsers($post)
    {
        $dt = new DataTable();

        $columns = array(
            'Username',
            'LastName',
            'EmailID',
            'r.Title',
            'u.Status'
        );

        $cond = '';

        $clauses = $dt->getClauses($columns, $post);

        if (isset($post['UserRole']) && $post['UserRole'] != '') {
            $cond .= (($cond == '') ? " WHERE " : " AND ") . " u.RoleID = " . $post['UserRole'] . " ";
        }

        if (isset($post['UserStatus']) && $post['UserStatus'] != '') {
            $cond .= (($cond == '') ? " WHERE " : " AND ") . " u.Status = '" . $post['UserStatus'] . "' ";
        }

        $qry = "SELECT SQL_CALC_FOUND_ROWS u.*, r.Title Role FROM users u INNER JOIN roles r ON r.RoleID = u.RoleID $cond ";
        $qry .= $clauses['clauses'];
        $records = $this->getRecords($qry, $clauses['params'], true);

        $data = array();
        foreach ($records as $record) {
            $disabled = $record['Status'] == 'Deleted' ? 'disabled' : '';
            $checked = $record['Status'] == 'Active' ? 'checked' : '';

            $checkbox_column = "<div class='checkbox checkbox-info'><input type='checkbox' name='UserIDs[]' class='UserID' role='$record[RoleID]' id='U$record[UserID]' value='$record[UserID]' $disabled /><label for='U$record[UserID]'>&nbsp;</label></div>";
            $toggle_column = "<label class='switch'><input type='checkbox' id='toggle" . $record['UserID'] . "' class='status-switch user-toggle'  data-user-id='" . $record['UserID'] . "'  data-last-name='" . $record['LastName'] . "'  $checked ><span class='slider round'></span></label>";
            if ($record['Status'] == 'Deleted') {
                $checkbox_column = "";
                $toggle_column = "";
            }

            $data[] = array(
                $checkbox_column,
                $record['Username'],
                $record['LastName'],
                $record['EmailID'],
                $record['Role'],
                $record['Status'],
                $toggle_column
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
     * returns single user info for given user id
     *
     * @param integer $user_id
     * @return array
     */
    function getUser($user_id)
    {
        return $this->getRecord("SELECT * FROM users WHERE UserID = ?", array(
            $user_id
        ), true);
    }

    /**
     * returns list of roles
     *
     * @return array
     */
    function getRoles()
    {
        return $this->getRecords("SELECT * FROM roles ORDER BY RoleOrder");
    }

    /**
     * check email for existenCe
     *
     * @param array $post
     * @return mixed
     */
    function checkEmail($post)
    {
        if ($post['UserID']) {
            $user_exist = $this->getOne("SELECT COUNT(1) FROM users WHERE EmailID = ? AND UserID <> ?", array(
                $post['EmailID'],
                $post['UserID']
            ));
        } else {
            $user_exist = $this->getOne("SELECT COUNT(1) FROM users WHERE EmailID = ?", array(
                $post['EmailID']
            ));
        }

        return $user_exist;
    }

    /**
     * check login id/username
     *
     * @param array $post
     * @return mixed
     */
    function checkUsername($post)
    {
        if ($post['UserID']) {
            return $this->getOne("SELECT COUNT(1) FROM users WHERE Username = ? AND UserID <> ?", array(
                $post['Username'],
                $post['UserID']
            ));
        } else {
            return $this->getOne('SELECT COUNT(1) FROM users WHERE Username = ?', array(
                $post['Username']
            ));
        }
    }

    /**
     * save/update user
     *
     * @param array $post
     * @throws Exception
     */
    function saveUser($post)
    {
        try {
            $this->start();
            $logged_in_user_id = $this->getSession('UserID');
            $now = date('Y-m-d H:i:s');
            $default_password = $this->getConfig('login', 'default_password');

            $params = array(
                'FirstName' => $post['FirstName'],
                'LastName' => $post['LastName'],
                'EmailID' => $post['EmailID'],
                'ContactNumber' => $post['ContactPhone']
            );

            if ($post['UserID']) {
                $user_id = $post['UserID'];
                // $params['Status'] = $post['Status'];
                $params['UpdatedBy'] = $logged_in_user_id;
                $params['UpdatedOn'] = $now;
                $this->updateRecord('users', $params, array(
                    'UserID' => $post['UserID']
                ), array(), $post['ReasonForUpdate']);
            } else {
                $params['Password'] = $this->hashPassword($default_password);
                $params['Username'] = $post['Username'];
                $params['RoleID'] = $post['RoleID'];
                $params['CreatedBy'] = $logged_in_user_id;
                $params['CreatedOn'] = $now;
                $params['Status'] = 'Active';
                $this->insertRecord('users', $params);
                $user_id = $this->getRecordID();
            }

            $zones = $post['ZoneID'];
            $this->getRecord(" UPDATE user_zones SET Status = 'InActive' WHERE UserID = ? ", array(
                $user_id
            ));
            foreach ($zones as $zone) {
                $insert_params = array(
                    'UserID' => $user_id,
                    'ZoneID' => $zone,
                    'Status' => 'Active',
                    'CreatedOn' => $now,
                    'CreatedBy' => $logged_in_user_id
                );
                $this->insertRecord('user_zones', $insert_params);
            }
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * delete user
     *
     * @param array $post
     * @throws Exception
     */
    function deleteUser($post)
    {
        try {
            $this->start();
            $this->updateRecord('users', array(
                'Status' => 'Deleted'
            ), array(
                'UserID' => $post['UserID']
            ), array(), $post['Reason']);

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * update reset password
     *
     * @param array $post
     * @throws Exception
     */
    function saveResetPassword($post)
    {
        try {
            $this->start();
            $user = $this->getUser($post['UserID']);
            $password = $this->generateCode(8);

            $body = "Dear User,<br>";
            $body .= '<p>Your CRM account password has been reset. Please use the random generated password "<b>' . $password . '</b>" to login into your account. You will be redirected to change password screen upon login.</p>';
            $body .= "<p>Random generated password is for one time use only. </p><br>";
            $body .= "<b>CRM Help Desk</b>";

            $this->sendEmail($user['EmailID'], 'CRM Reset Password', $body);

            $this->updateRecord('users', array(
                'Password' => $this->hashPassword($password)
            ), array(
                'UserID' => $post['UserID']
            ), array(), 'resetPassword');

            $this->save();
            return $user;
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * check if old password is correct
     *
     * @param array $post
     * @return boolean
     */
    function checkPassword($post)
    {
        $user = $this->getUser($post['UserID']);
        return $this->verifyPassword($post['OldPassword'], $user['Password']);
    }

    /**
     * update self password
     *
     * @param array $post
     * @throws Exception
     */
    function updatePassword($post)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $user_id = $post['UserID'];
            $this->start();

            $update_params = array(
                'Password' => $this->hashPassword($post['NewPassword'])
            );
            $where_params = array(
                'UserID' => $user_id
            );

            if (isset($post['SecurityQuestion']) && $post['SecurityQuestion'] != "") {
                $update_params['SecurityQuestion'] = $post['SecurityQuestion'];
                $update_params['SecurityQuestionResponse'] = $this->hashPassword($post['Response']);
            }

            $user_info = $this->getUser($user_id);
            // According to roles calculating expiration days
            if ($user_info['RoleID'] == 1 || $user_info['RoleID'] == 2) {
                $expiration_days = $user_info['PasswordExpiry'];
            } else if ($user_info['RoleID'] == 6 || $user_info['RoleID'] == 7) {
                $expiration_days = $this->getConfig('login', 'max_password_expiry');
            } else {
                $study_info = $this->getRecord("SELECT * FROM studies WHERE StudyID = ? ", array(
                    $user_info['DefaultStudy']
                ));
                $expiration_days = $study_info->PasswordExpiry;
            }
            // calculating expired date
            $expiry_date = date('Y-m-d H:i:s', strtotime('+ ' . $expiration_days . ' Days'));
            // if($user_info['PasswordValidity'] == "0000-00-00" || $user_info['PasswordValidity'] == ""){
            $update_params['PasswordValidity'] = $expiry_date;
            // }

            $update_params['LoginAttempts'] = 0;
            $update_params['LastLogin'] = $now;
            $update_params['UpdatedOn'] = $now;
            $update_params['UpdatedBy'] = $user_id;

            $this->updateRecord('users', $update_params, $where_params);

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * update profile
     *
     * @param array $post
     * @throws Exception
     */
    function saveProfile($post)
    {
        try {
            $this->start();
            $this->updateRecord('dn_users', array(
                'Name' => $post['Name']
            ), array(
                'ID' => $post['UserID']
            ));
            $this->setSession('Name', $post['Name']);
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * fetch list of timezones
     *
     * @return array
     */
    public function getTimezones()
    {
        return $this->getRecords("SELECT * FROM timezones ORDER BY TimezoneValue");
    }

    /**
     * check if the logged user role has access to selected moduel
     *
     * @param integer $role_id
     * @param integer $module_id
     * @return bool
     */
    function checkPrivilege($role_id, $module_id)
    {
        return $this->getOne("SELECT COUNT(1) FROM user_access_control WHERE RoleID = ? AND ModuleID = ?", array(
            $role_id,
            $module_id
        ));
    }

    /**
     * returns list of non admin roles
     *
     * @return array
     */
    function getNonAdminRoles()
    {
        return $this->getRecords("SELECT * FROM roles WHERE RoleID NOT IN (1, 2, 9, 11) ORDER BY Title");
    }

    /**
     * study manager, investigator, data manager, monitor, site co-ordinator
     *
     * @return array
     */
    function getStaffRoles()
    {
        return $this->getRecords("SELECT * FROM roles WHERE RoleID NOT IN (1, 2, 9, 11, 6, 7) ORDER BY Title");
    }

    /**
     * sending roles data to datatable
     *
     * @param array $post
     * @return mixed[]
     */
    public function getRolesList($post)
    {
        $dt = new DataTable();

        $columns = array(
            'RoleID',
            'Title',
            'RoleCode',
            'RoleOrder',
            'Status'
        );

        $cond = '  ';

        $clauses = $dt->getClauses($columns, $post);

        $qry = "SELECT SQL_CALC_FOUND_ROWS * FROM roles  $cond ";
        $qry .= $clauses['clauses'];
        $records = $this->getRecords($qry, $clauses['params'], true);

        $data = array();
        foreach ($records as $record) {
            $disabled = $record['Status'] == 'Active' ? '' : 'disabled';
            $checked = $record['Status'] == 'Active' ? 'checked' : '';
            $status = $record['Status'] == 'Inactive' ? 'Disabled' : $record['Status'];
            $data[] = array(
                ($record['RoleID'] == 1) ? '' : "<div class='checkbox checkbox-info'><input type='checkbox' name='RoleIDs[]' class='RoleID' role='$record[RoleID]' id='U$record[RoleID]' value='$record[RoleID]' $disabled /><label for='U$record[RoleID]'>&nbsp;</label></div>",
                $record['Title'],
                $record['RoleCode'],
                $record['RoleOrder'],
                $status,
                ($record['RoleID'] == 1) ? '' : (($record['Status'] == 'Deleted') ? '' : "<label class='switch'><input type='checkbox' id='toggle" . $record['RoleID'] . "' class='status-switch role-toggle' data-role-title='" . $record['Title'] . "'  data-role-id='" . $record['RoleID'] . "'  $checked ><span class='slider round'></span></label>")
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
     * Role information
     *
     * @return array
     */
    public function getRole($role_id)
    {
        return $this->getRecord("SELECT * FROM roles WHERE RoleID = ? ", array(
            $role_id
        ), true);
    }

    /**
     * check Role Title
     *
     * @param array $post
     * @return mixed
     */
    public function checkRoleTitle($post)
    {
        if ($post['RoleID']) {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE Title = ? AND RoleID <> ?", array(
                trim($post['Title']),
                $post['RoleID']
            ));
        } else {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE Title = ? AND Status <> 'Deleted' ", array(
                trim($post['Title'])
            ));
        }
    }

    /**
     * check Role Code
     *
     * @param array $post
     * @return mixed
     */
    public function checkRoleCode($post)
    {
        if ($post['RoleID']) {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE RoleCode = ? AND RoleID <> ?", array(
                trim($post['RoleCode']),
                $post['RoleID']
            ));
        } else {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE RoleCode = ? AND Status <> 'Deleted' ", array(
                trim($post['RoleCode'])
            ));
        }
    }

    /**
     * check Role Order
     *
     * @param array $post
     * @return mixed
     */
    public function checkRoleOrder($post)
    {
        if ($post['RoleID']) {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE RoleOrder = ? AND RoleID <>  ?", array(
                trim($post['RoleOrder']),
                $post['RoleID']
            ));
        } else {
            return $this->getOne("SELECT COUNT(1) FROM roles WHERE RoleOrder = ? AND Status <> 'Deleted' ", array(
                trim($post['RoleOrder'])
            ));
        }
    }

    /**
     * save/update Role
     *
     * @param array $post
     * @throws Exception
     */
    public function saveRole($post)
    {
        try {
            $this->start();
            $user_id = $this->getSession('UserID');
            $now = date('Y-m-d H:i:s');
            $role_params = array(
                'Title' => $post['Title'],
                'RoleCode' => $post['RoleCode'],
                'RoleOrder' => $post['RoleOrder']
            );
            if ($post['RoleID'] != 0) {
                $this->updateRecord('roles', $role_params, array(
                    'RoleID' => $post['RoleID']
                ), array(), $post['Reason']);
            } else {
                $role_params['CreatedBy'] = $user_id;
                $role_params['CreatedOn'] = $now;
                $role_params['Status'] = 'Active';
                $this->insertRecord('roles', $role_params);
                $msg = "Role \"$post[Title] \" is created.";
            }
            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * delete role
     *
     * @param array $post
     * @throws Exception
     */
    public function deleteRole($post)
    {
        try {
            $this->start();
            $this->updateRecord('roles', array(
                'Status' => 'Deleted'
            ), array(
                'RoleID' => $post['RoleID']
            ), array(), $post['Reason']);

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * Disalbe/Enable Role
     *
     * @param array $post
     * @throws Exception
     */
    public function changeRoleStatus($post)
    {
        try {
            $status = ($post['Status'] == 'Disable') ? 'Inactive' : 'Active';
            $this->start();
            if ($status == 'Disable') {
                $this->updateRecord('roles', array(
                    'Status' => $status
                ), array(
                    'RoleID' => $post['RoleID']
                ), array(), $post['Reason']);
            } else {
                $this->updateRecord('roles', array(
                    'Status' => $status
                ), array(
                    'RoleID' => $post['RoleID']
                ));
            }

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * will get Roles list based on Logged in User.
     *
     * @return array
     */
    public function getUsersRoles()
    {
        $role_id = $this->getSession('RoleID');

        $cond = " WHERE Status = 'Active' ";
        return $this->getRecords("SELECT RoleID, Title FROM roles $cond");
    }

    /**
     * Disalbe/Enable User
     *
     * @param array $post
     * @throws Exception
     */
    public function changeStatus($post)
    {
        try {
            $status = ($post['Status'] == 'Disable') ? 'Disabled' : 'Active';
            $this->start();
            if ($status == 'Disable') {
                $this->updateRecord('users', array(
                    'Status' => $status
                ), array(
                    'UserID' => $post['UserID']
                ), array(), $post['Reason']);
            } else {
                $this->updateRecord('users', array(
                    'Status' => $status
                ), array(
                    'UserID' => $post['UserID']
                ));
            }

            $this->save();
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }

    /**
     * Save XLS Doctor
     *
     * @param array $post
     * @throws Exception
     */
    function saveXlsUser($xls_data)
    {
        try {
            $this->start();
            $error_array = array();
            $user_id = $this->getSession('UserID');
            $now = date('Y-m-d H:i:s');
            $default_password = $this->getConfig('login', 'default_password');
            $row_no = 2;

            if ($xls_data) {
                foreach ($xls_data as $rec) {

                    $username = trim($rec['A']);
                    $first_name = trim($rec['B']);
                    $last_name = trim($rec['C']);
                    $email = trim($rec['D']);
                    $phone = trim($rec['E']);
                    $role_code = trim($rec['F']);
                    $zone_code = trim($rec['G']);

                    if (($username == "") && ($first_name == "") && ($last_name == "") && ($email == "") && ($phone == "") && ($role_code == "") && ($zone_code == "")) {
                        // Nothing to do
                    } else {
                        if ($username == '') {
                            $error_array[$row_no][] = 'Username is empty';
                        } else {
                            $exist_doctor_code = $this->getOne('SELECT COUNT(1) FROM users WHERE Username = ?', array(
                                $username
                            ));
                            if ($exist_doctor_code) {
                                $error_array[$row_no][] = 'Username is exist';
                            }
                        }
                        if ($first_name == '') {
                            $error_array[$row_no][] = 'First name is empty';
                        }
                        if ($last_name == '') {
                            $error_array[$row_no][] = 'Last name is empty';
                        }
                        if ($email == '') {
                            $error_array[$row_no][] = 'Email is empty';
                        } else {
                            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $error_array[$row_no][] = 'Invalid Email format ';
                            } else {
                                $exist_email_id = $this->getOne('SELECT COUNT(1) FROM users WHERE EmailID = ?', array(
                                    $email
                                ));
                                if ($exist_email_id) {
                                    $error_array[$row_no][] = 'Email ID is exist';
                                }
                            }
                        }
                        if ($role_code == '') {
                            $error_array[$row_no][] = 'Role Code is empty';
                        } else {
                            $role_availability = $this->getOne('SELECT COUNT(1) FROM roles WHERE RoleCode = ?', array(
                                $role_code
                            ));
                            if (! $role_availability) {
                                $error_array[$row_no][] = 'Role code is not available';
                            }
                        }

                        if ($zone_code == '') {
                            $error_array[$row_no][] = 'Zone Code is empty';
                        } else {
                            $zone_array = explode(",", $zone_code);
                            foreach ($zone_array as $zone) {
                                $zone_availaility = $this->getOne('SELECT COUNT(1) FROM zones WHERE ZoneCode = ? ', array(
                                    trim($zone)
                                ));
                                if (! $zone_availaility) {
                                    $error_array[$row_no][] = 'Zone code "' . $zone . '" is not available';
                                }
                            }
                        }

                        $row_no ++;

                        $error_array = array_filter($error_array);

                        if (empty($error_array)) {

                            $role_id = $this->getOne('SELECT RoleID FROM roles WHERE RoleCode = ?', array(
                                $role_code
                            ));

                            $params = array(
                                'Username' => $username,
                                'Password' => $this->hashPassword($default_password),
                                'FirstName' => $first_name,
                                'LastName' => $last_name,
                                'EmailID' => $email,
                                'ContactNumber' => $phone,
                                'RoleID' => $role_id,
                                'Status' => 'Active',
                                'CreatedBy' => $user_id,
                                'CreatedOn' => $now
                            );
                            $this->insertRecord('users', $params);
                            $created_user_id = $this->getRecordID();

                            $zones = explode(",", $zone_code);
                            foreach ($zones as $zone) {

                                $zone_id = $this->getOne('SELECT ZoneID FROM zones WHERE ZoneCode = ?', array(
                                    trim($zone)
                                ));

                                $insert_params = array(
                                    'UserID' => $created_user_id,
                                    'ZoneID' => $zone_id,
                                    'Status' => 'Active',
                                    'CreatedOn' => $now,
                                    'CreatedBy' => $user_id
                                );
                                $this->insertRecord('user_zones', $insert_params);
                            }
                        }
                    }
                }
            }
            if (! empty($error_array)) {
                $error_string = '';
                foreach ($error_array as $key => $error) {
                    $error_string .= '<b>Row No : ' . $key . '</b><br/>';
                    foreach ($error as $msg) {
                        $error_string .= $msg . ' <br/>';
                    }
                    $error_string .= '<br/>';
                }
                if ($error_string) {
                    $error_string .= '<b>Please correct the data and upload again.</b>';
                    throw new Exception($error_string);
                }
            } else {
                $this->save();
            }
        } catch (Exception $e) {
            $this->undo();
            throw $e;
        }
    }
}