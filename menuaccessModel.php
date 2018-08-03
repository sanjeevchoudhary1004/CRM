<?php

class MenuAccessControlModel extends SaralModel
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * to fetch access modules & roles
     *
     * @return array
     */
    function getAccessModules()
    {
        $modules = $this->getRecords("SELECT * FROM menus");
        $access_modules = array();
        $i = 0;
        $roles = $this->getRecords("SELECT * FROM roles WHERE RoleID <> 1 ");
        foreach ($modules as $module) {
            $access_modules[$i] = array(
                'ID' => $module->MenuID,
                'Name' => $module->MenuItemTitle
            );
            foreach ($roles as $role) {
                // $access = $this->getOne("SELECT COUNT(1) FROM acn_user_access_control WHERE RoleID = ? AND ModuleID = ?", array(
                // $role->RoleID,
                // $module->ModuleID
                // ));
                $access_modules[$i]['Role'][$role->RoleID] = array(
                    'RoleName' => $role->Title,
                    'Access' => 'No'
                    // 'Access' => ($access ? 'Yes' : 'No')
                );
            }
            $i ++;
        }
        return array(
            'roles' => $roles,
            'access_modules' => $access_modules
        );
    }

    function saveAll($post)
    {
        try {
            if ($post['Checked'] == 'No') {
                $this->deleteRecord("acn_user_access_control", array(
                    "RoleID" => $post['RoleID']
                ));
            } else {
                $modules = $this->getRecords("SELECT * FROM acn_access_modules");
                foreach ($modules as $module) {
                    if (! $this->getOne("SELECT COUNT(1) FROM acn_user_access_control WHERE RoleID = ? AND ModuleID = ?", array(
                        $post['RoleID'],
                        $module->ModuleID
                    ))) {
                        $this->insertRecord("acn_user_access_control", array(
                            'RoleID' => $post['RoleID'],
                            'ModuleID' => $module->ModuleID
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    function saveOne($post)
    {
        try {
            if ($post['Checked'] == 'No') {
                $this->deleteRecord("acn_user_access_control", array(
                    'RoleID' => $post['RoleID'],
                    'ModuleID' => $post['ModuleID']
                ));
            } else {
                $this->insertRecord("acn_user_access_control", array(
                    'RoleID' => $post['RoleID'],
                    'ModuleID' => $post['ModuleID']
                ));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}