<?php

require_once APPPATH . 'controllers/base/BaseController.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home
 *
 * @author Mirco Bombieri
 */
class Profiles extends BaseController {

    var $controllerId = 2;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->title = $this->lang->line('title_permissions_and_user_profiles');

        //Valido si tiene permiso de lectura del controlador
        if (!$this->userRights['controller'])
            redirect('back/home');
    }

    function index() {
        $this->table();
    }

    //Muestra la tabla de perfiles
    function table() {
        //Controlo los permisos.
        if (!$this->userRights['profile_table']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/home');
        } else {
            $items = array();
            foreach ($this->Profile_Model->_profiles_list() AS $profile) {
                $name = new DefaultTableCell($profile->name, 'text_column');

                //Modificar
                $modify = $this->userRights['profile_update'] ? anchor('back/profiles/update/' . $profile->id, nbs(6), 'class="edit" title="' . $this->lang->line('action_modify_data') . '"') : '';
                //Eliminar
                $remove = $this->userRights['profile_remove'] ? anchor('back/profiles/remove/' . $profile->id, nbs(6), 'class="remove" title="' . $this->lang->line('action_delet') . '" onclick="return confirm(\'' . $this->lang->line('message_delet_user_question') . '\');"') : '';
                //Permisos
                $access = $this->userRights['profile_access'] ? anchor('back/profiles/permission/' . $profile->id, nbs(6), 'class="access" title="' . $this->lang->line('action_permissios') . '" ') : '';
                //Perfiles permitidos
                $profiles_allowed = $this->userRights['profile_allowed'] ? anchor('back/profiles/allowed/' . $profile->id, nbs(6), 'class="profiles" title="' . $this->lang->line('action_profiles_allowed') . '"') : '';

                $actions = $profiles_allowed . nbs(2) . $access . nbs(2) . $modify . nbs(2) . $remove;
                $items[] = array($name, $actions);
            }

            $head = array($this->lang->line('label_name'), $this->lang->line('label_actions'));
            $table = new DefaultTable('user_table', $head, $items);
            $this->data['table'] = $table->generate();

            //Boton Nuevo
            if ($this->userRights['profile_new']) {
                $button_new = new Button($this->lang->line('action_new'), site_url('back/profiles/new_profile'));
                $button_new->setIcon('fa-plus');
                $this->data['new'] = $button_new->generate();
            } else
                $this->data['new'] = '';
            $this->loadView('custom/back/base/profile_table_view', $this->data, $this->lang->line('subtitle_list_users_profile'));
        }
    }

    //Elimina un perfil de usuario
    function remove($pId) {
        //Controlo los permisos
        if (!$this->userRights['profile_remove']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $this->Profile_Model->_remove_profile($pId);
            redirect('back/profiles');
        }
    }

    //Permite cargar un nuevo perfil de usuario
    function new_profile($pValidate = false) {
        //Controlo los permisos
        if (!$this->userRights['profile_new']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $this->data['name'] = new TextField('name', $pValidate ? $this->form_validation->get('name') : '', $this->lang->line('label_name'));
            $this->data['pass_length'] = new TextField('pass_length', $pValidate ? $this->form_validation->get('pass_length') : '', $this->lang->line('label_min_length_password'));
            $this->data['pass_composition'] = new DropdownField('pass_composition', $this->Profile_Model->_password_compositions(), $pValidate ? $this->form_validation->get('pass_composition') : 'standard', $this->lang->line('label_password_complexity'));
            $this->data['pass_rotation'] = new DropdownField('pass_rotation', $this->Profile_Model->_password_rotation_options(), $pValidate ? $this->form_validation->get('pass_rotation') : 0, $this->lang->line('label_password_rotation'));
            $this->data['lock_account'] = new BooleanField('lock_account', $pValidate ? $this->form_validation->get('lock_account') : TRUE, $this->lang->line('label_allow_locking_account'));
            $this->data['max_failed_attempts'] = new TextField('max_failed_attempts', $pValidate ? $this->form_validation->get('max_failed_attempts') : 6, $this->lang->line('label_max_access_intent_failed'));
             $this->data['is_administrator'] = new CheckField('is_administrator', 1, ($this->lang->line('label_is_administrator_profile')), set_value('is_administrator', false));
            $this->data['propagate_client_relation'] = new CheckField('propagate_client_relation', 1, ($this->lang->line('label_view_all_client')), set_value('propagate_client_relation', false));
           

            $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('back/profiles/confirm_new_profile'));
            $pSubmit->setIcon('fa-check');

            $pCancel = new Button($this->lang->line('action_cancel'), site_url('back/profiles'));
            $pCancel->setIcon('fa-close');
            $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');
            $pCancel->addClass('btn-danger');

            $this->loadFormView('custom/back/base/profile_form_view', $this->data, $this->lang->line('subtitle_register_new_user_profile'), $pSubmit, $pCancel);
        }
    }

    //Confirma los datos del nuevo perfil de usuario
    function confirm_new_profile() {
        //Controlo los permisos
        if (!$this->userRights['profile_new']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $this->form_validation->set_rules('name', $this->lang->line('label_name'), 'required');
            $this->form_validation->set_rules('pass_length', $this->lang->line('label_min_length_password'), 'numeric|required');
            $this->form_validation->set_rules('pass_composition', $this->lang->line('label_password_complexity'), 'required');
            $this->form_validation->set_rules('pass_rotation', $this->lang->line('label_password_rotation'), 'required');
            $this->form_validation->set_rules('lock_account', $this->lang->line('label_allow_locking_account'), 'required');
            $this->form_validation->set_rules('max_failed_attempts', $this->lang->line('label_max_access_intent_failed'), 'numeric|required'); 
            if ($this->form_validation->run() == false)
                $this->new_profile(true);
            else {
                $name = $this->input->post('name');
                $pass_length = $this->input->post('pass_length');
                $pass_composition = $this->input->post('pass_composition');
                $pass_rotation = $this->input->post('pass_rotation');
                $lock_account = $this->input->post('lock_account');
                $max_failed_attempts = $this->input->post('max_failed_attempts');
                $is_administrator = $this->input->post('is_administrator');
                $propagate = $this->input->post('propagate_client_relation');

                //Registro el Nuevo Perfil
                $this->Profile_Model->_new_profile($name, $pass_length, $pass_composition, $pass_rotation, $lock_account, $max_failed_attempts, $is_administrator, $propagate);
                redirect('back/profiles');
            }
        }
    }

    //Permite modificar un perfil de usuario
    function update($pId, $pValidate = false) {
        //Controlo los permisos
        if (!$this->userRights['profile_update']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            //Buscamos los datos del perifl de usuario.
            $profile = $this->Profile_Model->_profile_by_id($pId);
            if ($profile == null) {
                $this->messages->set('error', $this->lang->line('message_profile_no_found'));
                redirect('back/profiles');
            }

            $this->data['id'] = new HiddenField('id', $pId);
            $this->data['name'] = new TextField('name', $pValidate ? $this->form_validation->get('name') : $profile->name, $this->lang->line('label_name'));
            $this->data['pass_length'] = new TextField('pass_length', $pValidate ? $this->form_validation->get('pass_length') : $profile->pass_length, $this->lang->line('label_min_length_password'));
            $this->data['pass_composition'] = new DropdownField('pass_composition', $this->Profile_Model->_password_compositions(), $pValidate ? $this->form_validation->get('pass_composition') : $profile->pass_composition, $this->lang->line('label_password_complexity'));
            $this->data['pass_rotation'] = new DropdownField('pass_rotation', $this->Profile_Model->_password_rotation_options(), $pValidate ? $this->form_validation->get('pass_rotation') : $profile->pass_rotation, $this->lang->line('label_password_rotation'));
            $this->data['lock_account'] = new BooleanField('lock_account', $pValidate ? $this->form_validation->get('lock_account') : $profile->lock_account, $this->lang->line('label_allow_locking_account'));
            $this->data['max_failed_attempts'] = new TextField('max_failed_attempts', $pValidate ? $this->form_validation->get('max_failed_attempts') : $profile->max_failed_attempts, $this->lang->line('label_max_access_intent_failed'));
            $this->data['is_administrator'] = new CheckField('is_administrator', 1, ($this->lang->line('label_is_administrator_profile')), set_value('is_administrator', $profile->is_administrator));
            $this->data['propagate_client_relation'] = new CheckField('propagate_client_relation', 1, ($this->lang->line('label_view_all_client')), set_value('propagate_client_relation', $profile->propagate_system_user_client));
            $this->data['is_update'] = new HiddenField('is_update', true);

            $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('back/profiles/confirm_update_profile'));
            $pSubmit->setIcon('fa-check');

            $pCancel = new Button($this->lang->line('action_cancel'), site_url('back/profiles'));
            $pCancel->setIcon('fa-close');
            $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');
            $pCancel->addClass('btn-danger');

            $this->loadFormView('custom/back/base/profile_form_view', $this->data, $this->lang->line('subtitle_modify_user_profile'), $pSubmit, $pCancel);
        }
    }

    //Confirma los cambios en los datos del perfil de usuario
    function confirm_update_profile() {
        //Controlo los permisos
        if (!$this->userRights['profile_update']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $this->form_validation->set_rules('name', $this->lang->line('label_name'), 'required');
            $this->form_validation->set_rules('pass_length', $this->lang->line('label_min_length_password'), 'numeric|required');
            $this->form_validation->set_rules('pass_composition', $this->lang->line('label_password_complexity'), 'required');
            $this->form_validation->set_rules('pass_rotation', $this->lang->line('label_password_rotation'), 'required');
            $this->form_validation->set_rules('lock_account', $this->lang->line('label_allow_locking_account'), 'required');
            $this->form_validation->set_rules('max_failed_attempts', $this->lang->line('label_max_access_intent_failed'), 'numeric|required'); 
            $id = $this->input->post('id');
            if ($this->form_validation->run() == false)
                $this->update($id, true);
            else {
                $name = $this->input->post('name');
                $pass_length = $this->input->post('pass_length');
                $pass_composition = $this->input->post('pass_composition');
                $pass_rotation = $this->input->post('pass_rotation');
                $lock_account = $this->input->post('lock_account');
                $max_failed_attempts = $this->input->post('max_failed_attempts');
                $is_administrator = $this->input->post('is_administrator');
                $propagate = $this->input->post('propagate_client_relation');

                //Registro los cambios del Perfil
                $this->Profile_Model->_update_profile($id, $name, $pass_length, $pass_composition, $pass_rotation, $lock_account, $max_failed_attempts, $is_administrator, $propagate);
                redirect('back/profiles');
            }
        }
    }

    //Permite establecer los perfiles de usuario que tiene permitidos el perfil seleccionado
    function allowed($pId) {
        //Controlo los permisos
        if (!$this->userRights['profile_allowed']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            //Buscamos los datos del perifl de usuario.
            $profile = $this->Profile_Model->_profile_by_id($pId);
            if ($profile == null) {
                $this->messages->set('error', $this->lang->line('message_profile_no_found'));
                redirect('back/profiles');
            }

            //Mostramos los datos de perfil.
            $this->data['id'] = new HiddenField('id', $pId);
            $name = new TextField('name', $profile->name, $this->lang->line('label_name'));
            $name->setEnabled(FALSE);
            $this->data['name'] = $name;

            //Buscamos los perfiles administrados
            $administered = $this->Profile_Model->_profiles_has_profiles($pId);
            //Por cada perfil, controlo si es administrado o no por el perfil pId
            $items = array();
            foreach ($this->Profile_Model->all_profiles_list() AS $profile) {
                //Controlo si es administrado o no
                $value = $this->is_administered($profile->id, $administered);
                //Creo las celdas de la tabla
                $name = new DefaultTableCell($profile->name, 'text_column');
                //Defino el campo booleano
                $boolean_field = new CheckField('allowed_' . $profile->id, 1, '', $value);
                $field = new DefaultTableCell($boolean_field->generate(), 'right');
                $items[] = array($name, $field);
            }
            //Generamos la tabla.
            $head = array($this->lang->line('table_head_profile'), $this->lang->line('table_head_can_administrate'));
            $table = new DefaultTable('profiles_allowed', $head, $items);
            $this->data['table'] = $table->generate();

            $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('back/profiles/confirm_allowed_profile'));
            $pSubmit->setIcon('fa-check');

            $pCancel = new Button($this->lang->line('action_cancel'), site_url('back/profiles'));
            $pCancel->setIcon('fa-close');
            $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');
            $pCancel->addClass('btn-danger');

            $this->loadFormView('custom/back/base/profile_allowed_form_view', $this->data, $this->lang->line('subtitle_profile_allowed'), $pSubmit, $pCancel);
        }
    }

    //Confirma los cambios de los perfiles permitidos
    function confirm_allowed_profile() {
        //Controlo los permisos
        if (!$this->userRights['profile_allowed']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $pId = $this->input->post('id');
            //Por cada perfil tomo los valores marcados por el usuario
            $values = array();
            foreach ($this->Profile_Model->all_profiles_list() AS $profile) {
                $value = $this->input->post('allowed_' . $profile->id);
                $values[] = array($profile->id, $value);
            }
            //Actualizo los cambios
            $this->Profile_Model->_update_allowed_profiles($pId, $values);
            redirect('back/profiles');
        }
    }

    //Devuelve si el perfil indicado es un se encuentra o no en el array
    function is_administered($pId, $pAdministered) {
        for ($i = 0; $i < count($pAdministered); $i++) {
            if ($pAdministered[$i]->administered == $pId) {
                return TRUE;
                break;
            }
        }
        return FALSE;
    }

    //Permite cambiar los permisos del perfil sobre las funciones del sistema.
    function permission($pId) {
        //Controlo los permisos
        if (!$this->userRights['profile_access']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            //Buscamos los datos del perifl de usuario.
            $profile = $this->Profile_Model->_profile_by_id($pId);
            if ($profile == null) {
                $this->messages->set('error', $this->lang->line('message_profile_no_found'));
                redirect('back/profiles');
            }

            //Mostramos los datos de perfil.
            $this->data['id'] = new HiddenField('id', $pId);
            $name = new TextField('name', $profile->name, $this->lang->line('label_name'));
            $name->setEnabled(FALSE);
            $this->data['name'] = $name;

            //Buscamos las funciones a la que puede acceder este perfil
            $profile_functions = $this->Profile_Model->_functions_of_profile($pId);

            //Buscamos todos los controladores registrados
            $items = array();
            foreach ($this->Profile_Model->_controllers_list() AS $controller) {
                //Agregamos el controlador a la tabla
                $controller_name = new DefaultTableCell($controller->name, '', 'font-weight: bold; background: #F5F5F5; border-bottom: 1px solid #CCC; ', 2);
                $items[] = array($controller_name);

                //Buscamos todas las funciones del controlador
                $functions = $this->Profile_Model->_functions_of_controller($controller->id);

                $functions_count = 0;
                foreach ($functions AS $function) {
                    //Controlo si el perfil tiene permisos o no
                    $value = $this->exists_function($function->name, $profile_functions);
                    //Creo las celdas de la tabla
                    $name = new DefaultTableCell($function->visual_name, 'text_column');
                    //Defino el campo booleano
                    $boolean_field = new CheckField('access_' . $function->name, 1, '', $value);
                    $field = new DefaultTableCell($boolean_field->generate(), 'right');
                    $items[] = array($name, $field);
                    $functions_count++;
                }
                if ($functions_count == 0) {
                    $cell = new DefaultTableCell($this->lang->line('label_no_functions_for_this_controller'), 'text_column', 'font-style: italic;', 2);
                    $items[] = array($cell);
                }
            }
            //Generamos la tabla.
            $head = array($this->lang->line('table_head_fuctionality'), $this->lang->line('table_head_can_use'));
            $table = new DefaultTable('profiles_access', $head, $items);
            $this->data['table'] = $table->generate();

            $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('back/profiles/confirm_access_profile'));
            $pSubmit->setIcon('fa-check');

            $pCancel = new Button($this->lang->line('action_cancel'), site_url('back/profiles'));
            $pCancel->setIcon('fa-close');
            $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');
            $pCancel->addClass('btn-danger');

            $this->loadFormView('custom/back/base/profile_access_form_view', $this->data, $this->lang->line('subtitle_permission_to_funcionality'), $pSubmit, $pCancel);
        }
    }

    //Devuelve si la funcion dada existe dentro del array
    function exists_function($pFunctionName, $pFunctions) {
        for ($i = 0; $i < count($pFunctions); $i++) {
            if ($pFunctions[$i]->name == $pFunctionName) {
                return TRUE;
                break;
            }
        }
        return FALSE;
    }

    //Confirma los cambios sobre los permisos de un perfil
    function confirm_access_profile() {
        //Controlo los permisos
        if (!$this->userRights['profile_access']) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/profiles');
        } else {
            $pId = $this->input->post('id');
            //Por cada perfil tomo los valores marcados por el usuario
            $values = array();
            foreach ($this->Profile_Model->_controllers_list() AS $controller) {
                $functions = $this->Profile_Model->_functions_of_controller($controller->id);
                foreach ($functions AS $function) {
                    $value = $this->input->post('access_' . $function->name);
                    $values[] = array($function->name, $value);
                }
            }
            //Actualizo los cambios
            $this->Profile_Model->_update_access_profiles($pId, $values);
            redirect('back/profiles');
        }
    }

    /* Devuelve todos los permisos que tiene el perfil de usuario */

    function profile_permission() {

        $profile = $this->input->post('profile');
        $functions = $this->Profile_Model->_functions_of_profile($profile);
        $result = array();
        //Preparo la respuesta
        $count = 0;
        foreach ($functions AS $fx) {
            $element = array();
            $element['name'] = $fx->visual_name;

            $result[] = $element;
            $count++;
        }

        $response['Result'] = 'OK';
        $response['Records'] = $result;
        $response['TotalRecordCount'] = $count;

        echo json_encode($response);
    }

}
