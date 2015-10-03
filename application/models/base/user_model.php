<?php

/**
 * Modelo que administra consultas del usuarios.
 *
 * @author Mirco Bombieri
 */
class User_Model extends BaseModel {

    function User_Model() {
        parent::__construct();

        $this->initialize('system_user', 'username');
    }

    
    
    function make_logout() {
        if ($this->config->item('persistent_filters')) {
            $this->load->model('base/persistent_filters_model', 'Persistent_Filters');
            $this->Persistent_Filters->save_persistent_filters();
        }

        $this->session->destroy();
        $this->session->set_userdata('user_id', null);
    }

    function make_login($pUsername, $pPassword, $pRefUrl = null) {
        $user = $this->_user_by_username_and_password($pUsername, $pPassword);
        if ($user == null) {
            //El usuario no se ecuentra
            //Aumento el numero de intento fallidos, siempre que el usuario existe, y en tal caso, lo obtengo.
            $user = $this->_increment_failed_attempts($pUsername);
            if ($user != null) {
                //Determino si se bloquea la cuenta o no.
                if ($user->max_failed_attempts <= $user->failed_attempts) {
                    if ($user->lock_account) {
                        //Bloqueo la cuenta
                        $this->_block_account($user);
                        return array('status' => false, 'message_type' => 'error', 'message' => $this->lang->line('message_account_blocked'), 'redirect' => 'login');
                    } else {
                        //Notifico de la amenaza de la cuenta.
                        $this->send_email_to_threatened_account($user->email);
                    }
                }
            }
            return array('status' => false, 'message_type' => 'warning', 'message' => $this->lang->line('message_incorrect_login'), 'redirect' => 'login');
        }
        //Los datos son correctos
        else {
            //se verifica que la cuenta no este bloqueada
            if ($user->active == FALSE) {
                return array('status' => false, 'message_type' => 'warning', 'message' => $this->lang->line('message_access_blocked'), 'redirect' => 'login');
            } else {
                $this->_success_login($user->username);
                //Determino los datos en sesión.
                $session_array = array('user_id' => $user->username, 'name' => $user->name, 'email' => $user->email);
                $this->session->set_userdata($session_array);

                //Obtine los filtros persistentes
                if ($this->config->item('persistent_filters')) {
                    $this->load->model('base/persistent_filters_model', 'Persistent_Filters');
                    $this->Persistent_Filters->assign_persistent_filters_to_session();
                }

                if ($pRefUrl == null)
                    $url_destiny = (count($this->Parameter_Model->get_by_parameter('home')) == 0) ? '/back/home' : $this->Parameter_Model->get_by_parameter('home')->value;
                else
                    $url_destiny = str_replace(':', '/', $pRefUrl);
                return array('status' => true, 'message_type' => '', 'message' => '', 'redirect' => $url_destiny, 'user' => $user);
            }
        }
    }
    //Devuelve los datos de un usuario a partir de su nombre de usuario y su clave.
    function _user_by_username_and_password($pUser, $pPass) {
        $sql = "SELECT username, name, email, failed_attempts, last_pass_update, profile, active
                FROM system_user
                WHERE username = '" . $pUser . "'
                AND password = '" . $pPass . "'
                LIMIT 1;";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return null;
    }

    //Devuelve los datos de un usuario a partir de su nombre de usuario.
    function _user_by_username($pUser) {
        $sql = "SELECT
                    u.username
                    , u.name
                    , u.last_name
                    , u.doc_number
                    , u.doc_type
                    , u.phone
                    , u.birth_date
                    , u.multimedia_object_id
                    , mo.url AS photo
                    , u.email
                    , u.failed_attempts
                    , u.last_pass_update
                    , u.profile
                    , u.receive_emails
                    , p.id AS profile
                    , u.active
                FROM system_user u
                    LEFT JOIN multimedia_object mo ON u.multimedia_object_id = mo.id
                    JOIN profile p ON p.id = u.profile
                WHERE username = '" . $pUser . "'
                LIMIT 1;";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            return $query->row();
        else
            return null;
    }

    //Busca el usuario por su nombre de usuario, si lo encuentra aumenta su numero
    //de intentos fallidos y lo devuelve.
    function _increment_failed_attempts($pUser) {
        //Actualizo la cantidad de intentos fallidos.
        $sql = "UPDATE system_user SET failed_attempts = failed_attempts + 1 WHERE username = '" . $pUser . "';";
        $this->db->query($sql);

        //Busco el usuario
        $sql = "SELECT u.username, u.name, u.email, u.failed_attempts, u.last_pass_update, u.profile, p.pass_length, p.pass_rotation, p.max_failed_attempts, p.lock_account
                FROM system_user u, profile p
                WHERE u.username = '" . $pUser . "'
                AND u.profile = p.id
                LIMIT 1;";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
            $user = $query->row();
        else
            $user = null;

        if ($user != null) {
            //Insertamos el LOG de error al ingresar
            $sql = "INSERT INTO userdata_fail_log (username, date, time) VALUES('" . $pUser . "', NOW(), '" . date('G:i:s') . "');";
            $this->db->query($sql);
        }

        return $user;
    }

    //Bloquea la cuenta
    function _block_account($pUser) {
        $sql = "UPDATE system_user SET failed_attempts = 0, active = FALSE WHERE username = '" . $pUser . "';";
        $this->db->query($sql);
    }

    //Reinicia los intentos fallidos, y registra el acceso del usuario en el sistema
    function _success_login($pUser) {
        //Registro el Acceso.
        $sql = "INSERT INTO access_log (date, time, username) VALUES(NOW(), '" . date('G:i:s') . "', '" . $pUser . "');";
        $this->db->query($sql);
        //Reinicio los intentos fallidos.
        $sql = "UPDATE system_user SET failed_attempts = 0 WHERE username = '" . $pUser . "';";
        $this->db->query($sql);
    }

    //Genera y devuelve una nueva contraseña para un usuario
    function _generate_new_password($pUser, $pEmail) {
        try {
            $sql = "SELECT u.username, p.pass_length, p.pass_composition
                FROM system_user u, profile p
                WHERE u.username ='" . $pUser . "'
                AND u.email = '" . $pEmail . "'
                AND u.active
                AND u.profile = p.id
                LIMIT 1;";

            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $user = $query->row();
                $new_password = $this->_new_password($user->pass_length);
                //Actualizo el Usuario
                $sql = "UPDATE system_user SET password= md5('" . $new_password . "'), last_pass_update = null WHERE username = '" . $pUser . "' AND email = '" . $pEmail . "';";
                $query = $this->db->query($sql);
                if ($this->db->affected_rows() == 0)
                    return null;
                else
                    return $new_password;
            } else
                return null;
        } catch (Exception $e) {
            return null;
        }
    }

    //Devuelve una nueva semilla para generar una clave random.
    function new_seed() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

    //Genera una nueva clave con una longitud y composicion determinada
    function _new_password($pLong) {
        srand($this->new_seed());
        $pass = "";
        $chars = array();
        for ($i = "a"; $i < "z"; $i++)
            $chars[] = $i;
        $chars[] = "z";
        for ($i = 0; $i < 10; $i++)
            $chars[] = $i;
        $special_chars = $this->config->item('password_chars');
        for ($i = 0; $i < $pLong; $i++) {
            $type = round(rand(0, 2));
            switch ($type) {
                case 0:
                    $pass .=strtoupper($chars[round(rand(0, count($chars) - 1))]);
                    break;
                case 1:
                    $pass .= $chars[round(rand(0, count($chars) - 1))];
                    break;
                case 2:
                    $pass .= $special_chars[round(rand(0, count($special_chars) - 1))];
                    break;
                default:
                    $pass .=strtoupper($chars[round(rand(0, count($chars) - 1))]);
                    break;
            }
        }
        return $pass;
    }

    //Actualiza los datos de un usuario (nombre, email y si los recibe o no.
    function _update_userdata($pUser, $pName, $pEmail, $pPhone, $pReceiveEmails, $pLastName, $pDocNumber, $pBirthDate) {
        try {
            if ($pReceiveEmails == NULL)
                $pReceiveEmails = FALSE;
            $sql = "UPDATE system_user SET name = '" . $pName . "', email = '" . $pEmail . "', phone = '" . $pPhone . "', last_name = '" . $pLastName . "', doc_number = '" . $pDocNumber . "', birth_date = '" . $pBirthDate . "', receive_emails = " . $pReceiveEmails . " WHERE username = '" . $pUser . "'; ";
            $this->db->query($sql);
            if ($this->db->affected_rows() == 0)
                $this->messages->set('warning', 'No se actualizaron sus datos. Intente nuevamente.');
            else
                $this->messages->set('success', 'Se actualizaron sus datos con &eacute;xito.');
        } catch (Exception $e) {
            $this->messages->set('error', $e->message);
        }
    }

    //Actualiza la clave de un usuario
    function _update_password($pUser, $pCurrentPass, $pNewPass) {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        try {
            $user = $this->_user_by_username_and_password($pUser, md5($pCurrentPass));
            if ($user != null) {
                $sql = "UPDATE system_user SET password = '" . md5($pNewPass) . "', last_pass_update = '" . date('Y-m-d') . "' WHERE username = '" . $pUser . "' AND password = '" . md5($pCurrentPass) . "'; ";
                $this->db->query($sql);
                if ($this->db->affected_rows() == 0)
                    $this->messages->set('warning', 'Su clave no fué actualizada. Intente nuevamente.');
                else
                    $this->messages->set('success', 'Se cambió su clave de acceso al programa.');
            } else {
                $this->messages->set('warning', 'La clave actual no es correcta.');
            }
        } catch (Exception $e) {
            die($e->message);
            $this->messages->set('error', $e->message);
        }
        return $user != null;
    }

    //Actualiza la clave de un usuario
    function _update_other_user_password($pUser, $pNewPass) {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        try {
            $user = $this->_user_by_username($pUser);
            if ($user != null) {
                $sql = "UPDATE system_user SET password = '" . md5($pNewPass) . "', last_pass_update = '" . date('Y-m-d') . "' WHERE username = '" . $pUser . "'; ";
                $this->db->query($sql);
                if ($this->db->affected_rows() == 0)
                    $this->messages->set('warning', 'La clave de ' . $pUser . ' no fué actualizada. Intente nuevamente.');
                else
                    $this->messages->set('success', 'Se cambió la clave de acceso al programa de ' . $pUser . '.');
            }
        } catch (Exception $e) {
            $this->messages->set('error', $e->message);
        }
        return $user != null;
    }

    //Devuelve la lista de usuarios del sistema
    function all_elements($pFilters = null) {
        if ($pFilters == null)
            $pFilters = array();
        try {
            //Buscamos el usuario logueado
            $userId = $this->userId;
            $user = $this->User_Model->_user_by_username($userId);
            //Generamos la consulta de listado
            $sql = "SELECT u.username"
                    . ", u.name"
                    . ", u.last_name"
                    . ", u.doc_type"
                    . ", u.doc_number"
                    . ", u.phone"
                    . ", u.email"
                    . ", u.reg_date"
                    . ", u.birth_date"
                    . ", u.active"
                    . ", u.receive_emails"
                    . ", u.doc_type"
                    . ", u.doc_number  "
                    . ", u.multimedia_object_id  "
                    . ", mo.url AS photo"
                    . ", p.name AS profile_name "
                    . ", dt_lkp.name AS doc_type_name "
                    . ", CASE WHEN u.active = 1 THEN 'Activo' ELSE 'Bloqueado' END AS status_name "
                    . ", array_to_string(array_agg(DISTINCT c.first_name ORDER BY c.first_name ), ', ') AS client "
                    . " FROM system_user u"
                    . " LEFT JOIN multimedia_object mo ON u.multimedia_object_id = mo.id"
                    . " JOIN profile_has_profile pp ON pp.administered = u.profile  "
                    . " JOIN profile p ON p.id = u.profile  "
                    . " LEFT JOIN system_user_client suc ON suc.username=u.username "
                    . " LEFT JOIN client_group cg ON cg.client_id = suc.client_id "
                    . " LEFT JOIN client c ON c.id = suc.client_id "
                    . " LEFT JOIN client_group_lkp cg_lkp ON cg_lkp.id = cg.client_group_id "
                    . " LEFT JOIN doc_type_lkp dt_lkp ON dt_lkp.id = u.doc_type "
                    . " WHERE pp.administrator = " . $user->profile . " ";

            $filters_sql = '';

            if (isset($pFilters['client_group_filter']) && $pFilters['client_group_filter'] != null)
                $filters_sql .= " AND cg_lkp.id IN " . $this->_array_to_sql($pFilters['client_group_filter']);

            if (isset($pFilters['client_filter']) && $pFilters['client_filter'] != null)
                $filters_sql .= " AND cg.client_id IN " . $this->_array_to_sql($pFilters['client_filter']);

            if (isset($pFilters['role_filter']) && $pFilters['role_filter'] != null)
                $filters_sql .= " AND suc.user_role_id IN " . $this->_array_to_sql($pFilters['role_filter']);

            if (isset($pFilters['status_filter']) && $pFilters['status_filter'] != null)
                $filters_sql .= " AND u.active IN " . $this->_array_to_sql($pFilters['status_filter']);

            if (isset($pFilters['search_filter']) && $pFilters['search_filter'] != null) {
                $filters_sql .= " AND (" . $this->_find_by_text('u.username', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.last_name', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.name', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.email', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('c.first_name', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.phone', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.doc_type', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.doc_number', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.reg_date', $pFilters['search_filter']);
                $filters_sql .= " OR " . $this->_find_by_text('u.birth_date', $pFilters['search_filter']) . ')';
            }

            $sql .= $filters_sql . " GROUP BY u.username, dt_lkp.id, mo.url, p.id ORDER BY u.name;";
            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve los permisos para el usuario identificado en el controlador dado
    function permission_for($pControllerId, $pUserId) {
        //Busco las funciones a las que tiene permisos
        $sql = "SELECT f.name
            FROM profile p, system_user u, profile_has_function pf, system_function f
            WHERE p.id = u.profile
            AND p.id = pf.profile_id
            AND f.name = pf.function_name " .
                /* AND f.controller = " . $pControllerId . " */
                " AND u.username = '" . $pUserId . "';";
        $query = $this->db->query($sql);
        $allowed = array();
        foreach ($query->result() AS $function) {
            $allowed[$function->name] = TRUE;
        }

        //Busco todas las funciones del controlador
        $sql = "SELECT f.name FROM system_function f WHERE f.controller = " . $pControllerId . ";";
        $query = $this->db->query($sql);
        $all = $query->result();
        if (count($allowed) > 0)
            $result['controller'] = TRUE;
        else
            $result['controller'] = FALSE;

        foreach ($all AS $function) {
            $result[$function->name] = 0;
        }
        return array_merge($result, $allowed);
    }

    //Indica si el usuario debe rotar su clave
    function _must_rotate_pass($user) {
        $pass_rotation = $this->Profile_Model->_profile_by_id($user->profile)->pass_rotation;

        //Convierto las fechas a un timestamp
        $date_now = strtotime(date('Y-m-d'));
        $last_pass_update = strtotime($user->last_pass_update);

        $date_difference = $date_now - $last_pass_update;

        //Convierto el timestamp a dias y lo comparo con la cantidad de dias
        //para rotar la clave
        return ($pass_rotation != 0) && (($date_difference / (60 * 60 * 24)) >= $pass_rotation);
    }

    function _users_for_dropdown($pAllOption = false) {
        $users = array();

        if ($pAllOption)
            $users[] = new DropdownItem('', '- Sin seleccionar - ');

        foreach ($this->User_Model->all_elements() as $user)
            $users[] = new DropdownItem($user->username, $user->last_name . ', ' . $user->name . ' (' . $user->username . ')');

        return $users;
    }

    //Devuelve la lista de permisos de usuarios del sistema
    function _user_rights_list($pUser = '*') {
        try {
            //Generamos la consulta de listado
            //Campos devueltos: username (id), group (grupo), right(privilegios), right_description (descripcion)
            $sql = "SELECT usg.username, usglkp.name as group, urlkp.name as right, urlkp.description as right_description
							FROM user_security_group as usg,user_security_group_lkp as usglkp, user_right_lkp as urlkp, user_security_group_right as usgr
							WHERE usgr.user_right_id = urlkp.user_right_id
							AND usglkp.user_security_group_id = usgr.user_security_group_id
							AND usg.user_security_group_id=usgr.user_security_group_id
							AND usg.username='" . $pUser . "';";

            $query = $this->db->query($sql);
            return $query->result();
        } catch (Exception $e) {
            return array();
        }
    }

    function set_filters($pFilterList) {
        $this->Session_Model->setSessionName('user_filters');
        $this->Session_Model->set_elements($pFilterList);
    }

    function unset_filters() {
        $this->Session_Model->setSessionName('user_filters');
        $this->Session_Model->remove_all();
    }

    function get_filters() {
        $this->Session_Model->setSessionName('user_filters');
        return $this->Session_Model->elements();
    }

    function get_default_filters() {
        $filters['role_filter'] = array();
        $filters['client_group_filter'] = array();
        $filters['client_filter'] = array();
        $filters['status_filter'] = array();

        return $filters;
    }

    //Estados para dropdown
    function status_for_dropdown() {
        try {
            $array = array((object) array('id' => 1, 'name' => 'Activo'), (object) array('id' => 0, 'name' => 'Bloqueado'));
            $result = array();
            foreach ($array AS $status)
                $result[] = new DropdownItem($status->id, $status->name);
            return $result;
        } catch (Exception $e) {
            return array();
        }
    }

    //Estado de usuario por ID
    function status_by_id($id) {
        if ($id == null)
            return null;
        $result = array(1 => 'Activo', 2 => 'Bloqueado');
        return $result[$id];
    }

    //Almacena el estado de un usuario en base de datos.
    function save($pUser) {
        $this->db->trans_start();

        if (isset($pUser->password))
            $pUser->password = md5($pUser->password);

        $this->load->model('custom/multimedia_model', 'Multimedia_Model');

        $multimedia = $this->Multimedia_Model->get_by_id($this->config->item('default_image_id'));

        if ($pUser->photo == null || $pUser->photo == base_url($multimedia->url))
            $pUser->multimedia_object_id = $this->config->item('default_image_id');
        else
            $pUser->multimedia_object_id = $this->Multimedia_Model->save($pUser->photo, $pUser->multimedia_object_id != $this->config->item('default_image_id') ? $pUser->multimedia_object_id : null );

        //INSERTA
        if ($pUser->id == null)
            $username = $this->insert($pUser);
        else
        //ACTUALIZA
            $username = $this->update($pUser);
        //Asignar/Quitar Centros
        if (isset($pUser->user_clients))
            $this->validate_user_client($pUser, $username);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false)
            return false;
        else {
            return $username;
        }
    }

    function insert($pUser) {
        parent::insert($pUser);

        return $pUser->username;
    }

    //Verifica la relación entre client y system_user
    function validate_user_client($pUser, $username) {

        $pUser->user_clients = str_replace('\\', '', $pUser->user_clients);
        $pUser->user_clients = json_decode($pUser->user_clients);

        $users_clients_ids_from = array();
        foreach ($pUser->user_clients AS $uc) {
            $users_clients_ids_from[] = $uc->id;
        }

        //Obtengo todas las relaciones del cliente con usuarios
        $user_clients = $this->User_Model->get_client_list_by_username($pUser->username);

        //Proceso la eliminacion
        $sql_where_delete = array();
        $user_clients_ids = array();
        foreach ($user_clients AS $client) {
            if ($client != null) {
                //por cada client verifico si está en el nuevo array,

                if (!in_array($client->id, $users_clients_ids_from)) {
                    //Si NO ESTA en la nueva seleccion, lo incluyo para eliminar
                    $sql_where_delete[] = $client->id;
                }
                $user_clients_ids[] = $client->id;
            }
        }

        //Ejecuto la eliminacion de los que ya no estan
        if (count($sql_where_delete) > 0) {
            $sql_delete = 'DELETE FROM system_user_client WHERE username = ' . prep_field_null($pUser->username) . ' AND client_id IN ' . $this->_array_to_sql($sql_where_delete) . ';';
            $this->db->query($sql_delete);
        }

        //Preparo la insersion de las nuevas relaciones
        $sql_insert = array();
        foreach ($pUser->user_clients AS $new_client) {
            if ($new_client != null) {
                //por cada client seleccionado verifico si esta o no entre los existentes
                if (!in_array($new_client->id, $user_clients_ids)) {
                    //SI NO ESTA ENTRE LOS EXISTENTES LO MANDO A INSERTAR
                    $sql_insert[] = 'INSERT INTO system_user_client (client_id, username, user_role_id, assign_date, assign_user) '
                            . 'VALUES (' . $new_client->id . ', ' . prep_field_null($pUser->username) . ', ' . $new_client->role_id . ', NOW(), ' . prep_field_null($username) . '  );';
                }
            }
        }
        //Ejecuto la insersion delos nuevos
        foreach ($sql_insert AS $sql) {
            $this->db->query($sql);
        }

        return $pUser->user_clients;
    }

    //  Devuelve lista de client con filtro por usuario */
    function get_client_list_by_username($username) {
        $sql = "SELECT 
                    c.id,
                    (su.last_name||', '||su.name) AS assign_user , 
                    TO_CHAR(suc.assign_date,'DD/MM/YYYY HH:MM') AS assign_date,
                    suc.user_role_id,
                    r.name AS role_name
                FROM 
                    client c
                    JOIN system_user_client suc ON suc.client_id = c.id
                    JOIN system_user su ON su.username = suc.assign_user
                    JOIN system_user_role_lkp r ON r.id = suc.user_role_id
                WHERE suc.username = " . prep_field_null($username) . "
                ORDER BY c.description;";
        $query = $this->db->query($sql);
        return $query->result();
    }

    //  Devuelve lista de roles
    function system_user_role_list() {
        $sql = "SELECT r.id AS id, r.*
                FROM system_user_role_lkp r
                WHERE r.active = 1
                ORDER BY r.name; ";

        $query = $this->db->query($sql);
        return $query->result();
    }

    //Almacena el cambio de estado de un usuario en base de datos.
    function save_status($pUser) {
        $this->db->trans_start();
        $sql = "UPDATE system_user
      SET active=" . $pUser->active . "
      WHERE username='" . $pUser->username . "';";

        $this->db->query($sql);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false)
            return false;
        else {
            return $pUser->username;
        }
    }

}
