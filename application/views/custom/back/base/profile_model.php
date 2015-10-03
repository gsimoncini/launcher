<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of profile_model
 *
 * @author Mirco Bombieri
 */
class Profile_Model extends CI_Model {

    function Profile_Model() {
        parent::__construct();
        $this->load->database();
    }

    //Devuelve una colección de DropdownItems para formar un dropdown de Perfiles
    function _profiles_for_dropdown() {
        try {
            //Buscamos el usuario logueado
            $userId = $this->userId;
            $user = $this->User_Model->_user_by_username($userId);
            $sql = "SELECT p.id, p.name FROM profile p, profile_has_profile pp WHERE pp.administered = p.id AND pp.administrator = " . $user->profile . " ORDER BY p.name;";
            $query = $this->db->query($sql);
            $result = array();
            foreach ($query->result() AS $profile)
                $result[] = new DropdownItem($profile->id, $profile->name);
            return $result;
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve los perfiles permitidos para el usuario identificado
    function _profiles_list() {
        try {
            //Buscamos el usuario logueado
            $userId = $this->userId;
            $user = $this->User_Model->_user_by_username($userId);
            //Generamos la consulta de listado
            $sql = "SELECT p.id, p.name, p.pass_length, p.pass_composition, p.pass_rotation, p.max_failed_attempts, p.lock_account, p.is_administrator 
                    FROM profile p, profile_has_profile pp WHERE pp.administered = p.id AND pp.administrator = " . $user->profile . " ORDER BY p.name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve los perfiles
    function all_profiles_list() {
        try {
            $sql = "SELECT p.id, p.name, p.pass_length, p.pass_composition, p.pass_rotation, p.max_failed_attempts, p.lock_account, p.is_administrator 
                    FROM profile p ORDER BY p.name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve todas las funciones de un controlador
    function _functions_of_controller($pControllerId) {
        try {
            //Generamos la consulta de listado
            $sql = "SELECT f.name, f.visual_name 
                    FROM system_function f WHERE f.controller = " . $pControllerId . " ORDER BY f.visual_name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve todas las funciones a las que puede acceder el perfil
    function _functions_of_profile($pProfileId) {
        try {
            //Generamos la consulta de listado
            $sql = "SELECT f.name, f.visual_name 
                    FROM system_function f, profile_has_function pf WHERE pf.profile_id = " . $pProfileId . " 
                    AND pf.function_name = f.name 
                    ORDER BY f.visual_name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve un listado de controladores
    function _controllers_list() {
        try {
            //Generamos la consulta de listado
            $sql = "SELECT c.id, c.name
                    FROM controller c
                    ORDER BY c.name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Elimina completamente un perfil de usuario
    function _remove_profile($pId) {
        try {
            $this->db->trans_start();
            $sql = "DELETE FROM profile WHERE id = " . $pId . ";";
            $this->db->query($sql);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false)
                $this->messages->set('warning', 'No se eliminó el perfil de usuario. Intente nuevamente.');
            else
                $this->messages->set('success', 'El perfil de usuario fue eliminado completamente.');
        } catch (Exception $e) {
            $this->messages->set('error', $e->message);
        }
    }

    //Registra los datos de un nuevo perfil de usuario
    function _new_profile($pName, $pPassLength, $pPassComposition, $pPassRotation, $pLockAccount, $pMaxFailedAttempts, $pIsAdministrator, $pPropagateClient) {
        if ($pIsAdministrator == NULL)
            $pIsAdministrator = FALSE;

        //Inicio la transacción en la base de datos
        $this->db->trans_start();
        $sql = "INSERT INTO profile (name, pass_length, pass_composition, pass_rotation, lock_account, max_failed_attempts, is_administrator, propagate_system_user_client) 
                    VALUES('" . $pName . "', " . $pPassLength . ", '" . $pPassComposition . "', " . $pPassRotation . ", " . $pLockAccount . ", " . $pMaxFailedAttempts . ", " . prep_field_null($pIsAdministrator, 'numeric', false) . ", " . prep_field_null($pPropagateClient, 'numeric', false) . " ); ";
        $this->db->query($sql);

        //Finalizo la transacción
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $this->messages->set('warning', 'No se registró el nuevo perfil de usuario. Intente nuevamente.');
        else {
            //Registramos el vinculo entre el perfil del usuario y el registrado
            $user = $this->User_Model->_user_by_username($this->userId);
            $insert_id = $this->db->insert_id();
            $sql = "INSERT INTO profile_has_profile (administered, administrator) VALUES(" . $insert_id . ", " . $user->profile . ");";
            $this->db->query($sql);
            $this->messages->set('success', 'El nuevo perfil de usuario fué registrado con &eacute;xito.');
        }
    }

    //Actualiza los datos de un perfil
    function _update_profile($pId, $pName, $pPassLength, $pPassComposition, $pPassRotation, $pLockAccount, $pMaxFailedAttempts, $pIsAdministrator, $pPropagateClient) {
        try {
            if ($pIsAdministrator == NULL)
                $pIsAdministrator = FALSE;
            $sql = "UPDATE profile SET name = '" . $pName . "', pass_length = " . $pPassLength . ", pass_composition= '" . $pPassComposition . "', pass_rotation= " . $pPassRotation . ", lock_account=" . $pLockAccount . ", max_failed_attempts = " . $pMaxFailedAttempts . ", is_administrator = " . prep_field_null($pIsAdministrator, 'numeric', false) . ", propagate_system_user_client= " . prep_field_null($pPropagateClient, 'numeric', false) . " WHERE id = " . $pId . "; ";
            $this->db->query($sql);
            if ($this->db->affected_rows() == 0)
                $this->messages->set('warning', 'No se actualizaron los datos del usuario. Intente nuevamente.');
            else
                $this->messages->set('success', 'Los datos del usuario fueron actualizados con &eacute;xito.');
        } catch (Exception $e) {
            $this->messages->set('error', $e->message);
        }
    }

    //Devuelve un perfil de usuario segun ID
    function _profile_by_id($pId) {
        $sql = "SELECT id, name, pass_length, pass_composition, pass_rotation, lock_account, max_failed_attempts, is_administrator, propagate_system_user_client
                FROM profile
                WHERE id = " . $pId . "
                LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return null;
    }

    //Devuelve todos los tipos de composiciones de clave permitidos en un array de DropdownItems
    function _password_compositions() {
        $compositions[] = new DropdownItem('standard', '1. Estandar');
        $compositions[] = new DropdownItem('alphanumeric', '2. Alfanumérico');
        $compositions[] = new DropdownItem('anup', '3. (2) + al menos una mayúscula');
        $compositions[] = new DropdownItem('anupnoc', '4. (3) + caracteres no contiguos');
        $compositions[] = new DropdownItem('anupnoci', '5. (4) + no más de dos caracteres idénticos');
        return $compositions;
    }

    //Devuelve un array con DropdownItems correspondientes a la posibilidades de rotación de claves permitidas
    function _password_rotation_options() {
        $rotations[] = new DropdownItem(0, 'Nunca');
        $rotations[] = new DropdownItem(30, 'Cada 30 días');
        $rotations[] = new DropdownItem(60, 'Cada 60 días');
        $rotations[] = new DropdownItem(90, 'Cada 90 días');
        return $rotations;
    }

    //Devuelve los perfiles administrables para el usuario identificado
    function _profiles_has_profiles($pId) {
        try {
            $sql = "SELECT pp.administered FROM profile_has_profile pp WHERE pp.administrator = " . $pId . "; ";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Actualiza los perfiles administrables de u perfil dado
    function _update_allowed_profiles($pProfileId, $pValues) {
        //Inicio la transacción en la base de datos
        $this->db->trans_start();
        foreach ($pValues as $value) {
            $sql = "SELECT * FROM profile_has_profile pp WHERE pp.administrator = " . $pProfileId . " AND pp.administered = " . $value[0] . ";";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0 && $value[1] == false) {
                //Tengo que eliminar la relación
                $sql = "DELETE FROM profile_has_profile WHERE administrator = " . $pProfileId . " AND administered = " . $value[0] . ";";
                $this->db->query($sql);
            } else if ($query->num_rows() == 0 && $value[1] == true) {
                //Inserto una relación   
                $sql = "INSERT INTO profile_has_profile (administrator, administered) VALUES (" . $pProfileId . ", " . $value[0] . ");";
                $this->db->query($sql);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $this->messages->set('warning', 'No se registraron los cambios en los perfiles permitidos. Intente Nuevamente.');
        else
            $this->messages->set('success', 'Los perfiles permitidos fueron actualizados con éxito.');
    }

    //Actualiza los permisos de un perfil
    function _update_access_profiles($pProfileId, $pValues) {
        //Inicio la transacción en la base de datos
        $this->db->trans_start();
        foreach ($pValues as $value) {
            //Consulto la existencia del permiso
            $sql = "SELECT * FROM profile_has_function pf WHERE pf.profile_id = " . $pProfileId . " AND pf.function_name= '" . $value[0] . "';";
            $query = $this->db->query($sql);
            //Si existe y está marcado como falso
            if ($query->num_rows() > 0 && $value[1] == false) {
                //Tengo que eliminar la relación
                $sql = "DELETE FROM profile_has_function WHERE profile_id = " . $pProfileId . " AND function_name = '" . $value[0] . "';";
                $this->db->query($sql);
            } else if ($query->num_rows() == 0 && $value[1] == true) {
                //Inserto una relación   
                $sql = "INSERT INTO profile_has_function (profile_id, function_name) VALUES (" . $pProfileId . ", '" . $value[0] . "');";
                $this->db->query($sql);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            $this->messages->set('warning', 'No se registraron los cambios en los permisos. Intente Nuevamente.');
        else
            $this->messages->set('success', 'Los permisos fueron actualizados con éxito.');
    }

    //Devuelve un perfil de usuario segun ID de un usuario
    function _profile_by_user_id($pId) {
        $sql = "SELECT p.id, p.name, p.pass_length, p.pass_composition, p.pass_rotation, p.lock_account, p.max_failed_attempts, p.is_administrator,
            p.propagate_system_user_client
                FROM profile p
                JOIN system_user s ON s.profile = p.id
                WHERE s.username = '" . $pId . "'
                LIMIT 1;";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return null;
    }

}

?>
