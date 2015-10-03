<?php

class User_Role_Model extends BaseModel {

    function User_Role_Model() {
        parent::__construct();

        $this->initialize('system_user_role_lkp');
    }

    function elements_for_dropdown() {
        return parent::elements_for_dropdown('{description}');
    }

}
