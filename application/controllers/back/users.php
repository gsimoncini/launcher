<?php

require_once APPPATH . 'controllers/base/BaseController.php';
require_once APPPATH . 'controllers/base/CRUDController.php';

/**
 * Description of home
 *
 * @author Mirco Bombieri
 */
class Users extends CRUDController {

    var $controllerId = 1;
    var $operation;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->title = $this->lang->line('title_users');
        $this->singularName = $this->lang->line('entity_user');
        $this->javascriptName = 'userController';
        $this->url = 'back/users';
        $this->viewPath = 'custom/back/base/users';

        //cargo los modelos
        $this->load->model('base/user_model', 'User_Model');
        $this->entity_model = $this->User_Model;

        $this->load->model('client_model', 'Client_Model'); 
        $this->load->model('custom/doc_type_model', 'Doc_Type_Model');
        $this->load->model('custom/user_role_model', 'User_Role_Model');

        //valido si tiene permiso de lectura del controlador
        $this->accessControl();

        //cargo el javascript con las funciones para el controlador
        $user_js = file_get_contents(base_url('js/custom/users.js'));
        $client_js = file_get_contents(base_url('js/custom/client.js'));

        $this->setExtraJavascript($user_js . $client_js);
    }

    function index() {
        $this->table();
    }

    //Muestra la tabla de usuarios
    public function table() {
        //controlo los permisos sobre la tabla
        $this->accessControl('user_table');

        //botones de acción
        $this->data['add_button'] = $this->actionButton($this->lang->line('action_new'), 'fa-plus', $this->url . '/add', 'user_new');
        $this->data['edit_button'] = $this->actionButton($this->lang->line('action_edit'), 'fa-pencil', $this->url . '/edit/{username}', 'user_update', true);
        $this->data['change_password_button'] = $this->actionButton($this->lang->line('action_change_password'), 'fa-key', $this->url . '/change_password/{username}', 'user_change_password', true);
        $this->data['activate_button'] = $this->actionButton($this->lang->line('action_activate'), 'fa-check', $this->url . '/change_status/{username}/1', 'user_lock', true);
        $this->data['deactivate_button'] = $this->actionButton($this->lang->line('action_lock'), 'fa-ban', $this->url . '/change_status/{username}/0', 'user_lock', true);
        //Boton filtros
        $this->data['view_filter_button'] = new Button('', '#', 'btn-inverse btn-filter');
        $this->data['view_filter_button']->setAttributes('onclick="baseController.toggleFilterPanel();"');
        $this->data['view_filter_button']->setIcon('fa-filter');
        $this->data['view_filter_button']->setTooltip($this->lang->line('action_filter'));

        $sort_items[] = new SortTableDropdownItem('last_name', $this->lang->line('sort_list_surname'));
        $sort_items[] = new SortTableDropdownItem('name', $this->lang->line('sort_list_name'));
        $sort_items[] = new SortTableDropdownItem('profile_name', $this->lang->line('sort_list_profile'));
        $sort_items[] = new SortTableDropdownItem('phone', $this->lang->line('sort_list_phone'));
        $sort_items[] = new SortTableDropdownItem('doc_type_name', $this->lang->line('sort_list_document_type'));
        $sort_items[] = new SortTableDropdownItem('doc_number', $this->lang->line('sort_list_document'));
        $sort_items[] = new SortTableDropdownItem('email', $this->lang->line('sort_list_email'));
        $sort_items[] = new SortTableDropdownItem('client', $this->lang->line('sort_list_client'));
        $sort_items[] = new SortTableDropdownItem('status_name', $this->lang->line('sort_list_status'));
        $sort_items[] = new SortTableDropdownItem('username', $this->lang->line('sort_list_username'));

        $this->data['sort_table_button'] = new SortTableDropdown($sort_items, $this->javascriptName);
        $this->data['sort_table_button']->setTooltip($this->lang->line('action_order'));

        //formulario de filtro 
        $status_elements = $this->User_Model->status_for_dropdown();
        $role_elemnts = $this->User_Role_Model->elements_for_dropdown();

        $filters = $this->get_filters(); 

        $this->data['client_filter'] = new DropdownField('client_filter', $this->Client_Model->all_client_for_dropdown(), $filters['client_filter'], $this->lang->line('label_service'));
        $this->data['client_filter']->setMultiSelect(true);

        $this->data['role_filter'] = new DropdownField('role_filter', $role_elemnts, $filters['role_filter'], $this->lang->line('label_role'));
        $this->data['role_filter']->setMultiSelect(true);

        $this->data['status_filter'] = new DropdownField('status_filter', $status_elements, $filters['status_filter'], $this->lang->line('label_status'));
        $this->data['status_filter']->setMultiSelect(true);

        $this->data['search_filter'] = new TextField('search_filter', null, $this->lang->line('label_enter_your_search'));
        $this->data['search_filter']->setInlineLabel(true);
        $this->data['search_filter']->setAttributes('onkeyup="baseController.searchOnEnter(event, userController);"');

        $this->data['filter_button'] = new Button('Filtrar', '#', 'btn-primary');
        $this->data['filter_button']->setAttributes('onclick="userController.filterTable();"');
        $this->data['filter_button']->setIcon('fa-check');

        $this->data['remove_filter_button'] = new Button('Remover filtro');
        $this->data['remove_filter_button']->setAttributes('onclick="userController.removeFilterTable();"');
        $this->data['remove_filter_button']->setIcon('fa-close');
        $this->data['remove_filter_button']->addClass('btn-danger');

        $this->loadView($this->viewPath . '/user_table_view', $this->data, $this->lang->line('subtitle_list_users'));
    }

    //prepara la pantalla de alta de servicios (Segun el Mockup, segunda pantalla de ABM servicios - tab informacion).
    function add() {
        $this->accessControl('user_new');

        parent::add();
    }

    function edit($pUsername) {
        $this->accessControl('user_update');

        $user = (object) $this->User_Model->_user_by_username($pUsername);

        //Obtengo los clientes asociados
        $this->setUserClients($user);

        parent::edit($user);
    }

    //prepara la pantalla de change status
    function change_status($pUsername, $pStatusId) {
        $this->accessControl('user_lock');

        $user = (object) $this->User_Model->_user_by_username($pUsername);
        $user->active = $pStatusId;
        //Obtengo los clientes asociados
        $this->setUserClients($user);
        $this->define_form_components($user, true);
        $this->data['allow_assign_client'] = false;

        parent::change_status($user, $pStatusId);
    }

    //Setea todos los catalogos asignados para mandarlos al input HIDDEN que mantiene los catalgos
    function setUserClients($pUser) {

        $user_clients = $this->User_Model->get_client_list_by_username($pUser->username);
        $sep = '';
        $pUser->user_clients = '[';
        //Armo listado de catalogos asociados para comparar
        foreach ($user_clients AS $client) {
            $pUser->user_clients .= $sep . '{"id":' . $client->id . ',"role_id":' . $client->user_role_id . '}';
            $sep = ',';
        }
        $pUser->user_clients .= ']';
        return $pUser;
    }

    //Almacena el estado del cliente (NUEVO/EdITAR/COPIAR)
    function save($pOperation) {
        $this->operation = $pOperation;

        if ($pOperation == 1) {
            //NEW
            $this->accessControl('user_new');
        } else if ($pOperation == 2) {
            //UPDATE
            $this->accessControl('user_update');
        } else {
            redirect($this->url);
        }
        parent::save($pOperation);
    }

    //Almacena el cambio de estado
    function save_status() {

        $this->accessControl('user_lock');
        parent::save_status();
    }

    //Define los elementos a partir de un objeto
    function define_form_components($pUser, $pStatic = false) {

        $this->data['user_clients'] = new HiddenField('user_clients', $this->get_item_value($pUser, 'user_clients', '[]'));
        $this->data['user_active'] = new HiddenField('active', $pUser ? $pUser->active : 1);
        $this->data['id'] = new HiddenField('id', $pUser ? $pUser->username : null);
        $this->data['multimedia_object_id'] = new HiddenField('multimedia_object_id', $pUser ? $pUser->multimedia_object_id : null);

        $this->data['profile'] = new DropdownField('profile', $this->Profile_Model->_profiles_for_dropdown(), $this->get_item_value($pUser, 'profile', -1), $this->lang->line('label_profile'), '', 'onchange="userController.refreshProfileFunctions();"');
        $this->data['profile']->setEnabled(!$pStatic);

        $this->data['name'] = new TextField('name', $this->get_item_value($pUser, 'name', ''), $this->lang->line('label_name'));
        $this->data['name']->setEnabled(!$pStatic);
        $this->data['last_name'] = new TextField('last_name', $this->get_item_value($pUser, 'last_name', ''), $this->lang->line('label_surname'));
        $this->data['last_name']->setEnabled(!$pStatic);
        $this->data['username'] = new TextField('username', $this->get_item_value($pUser, 'username', ''), $this->lang->line('label_username'));
        $this->data['username']->setEnabled(!$pUser && !$pStatic);

        $this->data['doc_type'] = new DropdownField('doc_type', $this->Doc_Type_Model->elements_for_dropdown(), $this->get_item_value($pUser, 'doc_type', -1), $this->lang->line('label_document_type'));
        $this->data['doc_type']->setEnabled(!$pStatic);
        $this->data['doc_number'] = new TextField('doc_number', $this->get_item_value($pUser, 'doc_number', ''), $this->lang->line('label_document_number'));
        $this->data['doc_number']->setEnabled(!$pStatic);
        $this->data['phone'] = new TextField('phone', $this->get_item_value($pUser, 'phone', ''), $this->lang->line('label_phone'));
        $this->data['phone']->setEnabled(!$pStatic);
        $this->data['birth_date'] = new DateDropdownField('birth_date', $this->get_item_value($pUser, 'birth_date'), $this->lang->line('label_birth_date'));
        $this->data['birth_date']->setEnabled(!$pStatic);

        $this->data['password'] = new PasswordField('password', $this->lang->line('label_password'));
        $this->data['password']->setEnabled(!$pStatic);
        $this->data['password_confirmation'] = new PasswordField('password_confirmation', $this->lang->line('label_confirm_password'));
        $this->data['password_confirmation']->setEnabled(!$pStatic);

        $this->data['email'] = new TextField('email', $this->get_item_value($pUser, 'email', ''), $this->lang->line('label_email'));
        $this->data['email']->setEnabled(!$pStatic);
        $this->data['email_confirmation'] = new TextField('email_confirmation', set_value('email_confirmation', $pUser ? $pUser->email : ''), $this->lang->line('label_confirm_email'));
        $this->data['email_confirmation']->setEnabled(!$pStatic);

        $this->data['receive_emails'] = new BooleanField('receive_emails', $this->get_item_value($pUser, 'receive_emails', false), $this->lang->line('label_receive_email_notifications'));
        $this->data['receive_emails']->setEnabled(!$pStatic);

        //FOTO
        $this->data['photo'] = new FileUploadField('photo', $this->lang->line('action_select_picture'), set_value('photo', $pUser ? base_url($pUser->photo) : null));
        $this->data['photo']->setStatic($pStatic);

        //Duplicados para otras pestañas
        $this->data['name_duplicated'] = new TextField('name_duplicated', $this->get_item_value($pUser, 'name', ''), $this->lang->line('label_name'), 'name-duplicated');
        $this->data['name_duplicated']->setEnabled(false);
        $this->data['last_name_duplicated'] = new TextField('last_name_duplicated', $this->get_item_value($pUser, 'last_name', ''), $this->lang->line('label_surname'), 'last-name-duplicated');
        $this->data['last_name_duplicated']->setEnabled(false);

        //Filtros de client
        $this->data['client_search'] = new TextField('client_search', '', $this->lang->line('label_search'));
        $this->data['client_search']->setInlineLabel(true);
        $this->data['client_search']->setAttributes('onkeyup="userController.searchOnEnterClient(event);"');

        //Filtro radiobutton
        $this->data['client_assign_yes'] = new RadioField('client_assign', $this->lang->line('label_assigned'), 'assigned', '', '', 'onclick="userController.filterOnClientTable();"');
        $this->data['client_assign_no'] = new RadioField('client_assign', $this->lang->line('label_unassigned'), 'not_assigned', '', '', 'onclick="userController.filterOnClientTable();"');
        $this->data['client_assign_all'] = new RadioField('client_assign', $this->lang->line('label_all'), 'all', true, '', 'onclick="userController.filterOnClientTable();"');

        $filters = $this->get_filters();
            
    }

    //Devuelve los tabs a conformar
    function get_tabs() {
        $tab_active = set_value('tab_active', 'info');
        $tabs = array();

        $this->data['tab_active'] = $tab_active;

        $tabs[] = new TabItem('info', $this->lang->line('label_information'), $tab_active);
        $tabs[] = new TabItem('client-groups', $this->lang->line('label_services'), $tab_active);
        $tabs[] = new TabItem('rights', $this->lang->line('label_permissions'), $tab_active);

        return $tabs;
    }

    function set_validation_rules() {
        // Reglas de validacion
        $this->form_validation->set_rules('tab_active', '', '');
        $this->form_validation->set_rules('photo', '', '');

        // info-tab
        $this->form_validation->set_rules('profile', $this->lang->line('label_profile'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('label_name'), $this->config->item('required_name') == true ? 'required' : '');
        $this->form_validation->set_rules('last_name', $this->lang->line('label_surname'), $this->config->item('required_last_name') == true ? 'required' : '');
        $this->form_validation->set_rules('username', $this->lang->line('label_username'), ($this->config->item('required_username') == true ? 'required' : '') . '|callback_check_username');

        if ($this->operation == 1) {
            $this->form_validation->set_rules('password', $this->lang->line('label_password'), 'required');
            $this->form_validation->set_rules('password_confirmation', $this->lang->line('label_confirm_password'), 'required|matches[password]');
        }

        $this->form_validation->set_rules('email', $this->lang->line('label_email'), ($this->config->item('required_email') == true ? 'required' : '') . '|valid_email');
        $this->form_validation->set_rules('email_confirmation', $this->lang->line('label_confirm_email'), ($this->config->item('required_email') == true ? 'required' : '') . '|matches[email]');

        $this->form_validation->set_rules('birth_date', $this->lang->line('label_birth_date'), $this->config->item('required_birth_date') == true ? 'required' : '');
        $this->form_validation->set_rules('doc_type', $this->lang->line('label_document_type'), $this->config->item('required_doc_type') == true ? 'required' : '');
        $this->form_validation->set_rules('doc_number', $this->lang->line('label_document_number'), $this->config->item('required_doc_number') == true ? 'required' : '');
        $this->form_validation->set_rules('phone', $this->lang->line('label_phone'), $this->config->item('required_phone') == true ? 'required' : '');
        $this->form_validation->set_rules('user_clients', $this->lang->line('label_services'), $this->config->item('required_user_clients') == true ? 'required' : '');
        $this->form_validation->set_rules('receive_emails', $this->lang->line('label_receive_email_notifications'), '');
    }

    //valida el username
    function check_username($pUsername) {
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]+$/', $pUsername)) {
            return true;
        } else {
            $this->form_validation->set_message('check_username', $this->lang->line('message_invalid_field'));
            return false;
        }
    }

    /* Devuelve todos los clients indicando si esta o no asociado al usuario */

    function clients_elements() {

        $result = array();
        $count = 0;

        $pUsername = $this->input->post('username');

        //Todos los clients
        $all_clients = $this->Client_Model->all_elements(array('status_filter' => array(1)));

        if ($pUsername != null) {
            //Catalogos asociados al cliente
            $user_clients = $this->User_Model->get_client_list_by_username($pUsername);
            $user_clients_id = array();
            $user_clients_data = array();

            //Armo listado de catalogos asociados para comparar
            foreach ($user_clients AS $user_client) {
                $user_clients_id[] = $user_client->id;
                $user_clients_data[$user_client->id] = $user_client;
            }
        } else {
            $user_clients = $this->Client_Model->all_elements(array('status_filter' => array(1)));
            $user_clients_id = array();
            $user_clients_data = array();
        }

        //Levanto los roles
        $roles_option = array();
        $roles = $this->User_Model->system_user_role_list();
        $roles_option[] = new DropdownItem(-1, $this->lang->line('label_no'));
        foreach ($roles AS $rol) {
            $roles_option[] = new DropdownItem($rol->id, $rol->name);
        }

        //Preparo la respuesta
        foreach ($all_clients AS $client) {
            $element = array();

            $element['id'] = $client->id;
            $element['name'] = $client->name;
            $element['center'] = '';
            $element['center_id'] = '';
            $element['assign_user'] = '';
            $element['assign_date'] = '';

            if (in_array($client->id, $user_clients_id)) {
                $element['assign'] = true;
                $element['role_id'] = $user_clients_data[$client->id]->user_role_id;
                $element['role_name'] = $user_clients_data[$client->id]->role_name;
                $element['assign_user'] = $user_clients_data[$client->id]->assign_user;
                $element['assign_date'] = $user_clients_data[$client->id]->assign_date;
            } else {
                $element['assign'] = false;
                $element['role_id'] = -1;
                $element['role_name'] = '';
            }

            $control = new DropdownField('role_' . $client->id, $roles_option, $element['role_id'], '', '', 'onchange="userController.checkClient(' . $client->id . ',$(\'#role_' . $client->id . ' option:selected\').val());"');
            $element['assign_control'] = $control->generate();

            $result[] = $element;
            $count++;
        }

        $response['Result'] = 'OK';
        $response['Records'] = $result;
        $response['TotalRecordCount'] = $count;

        echo json_encode($response);
    }

    function define_form_components_for_change_password($pUser, $pStatic) {

        $this->data['profile'] = new DropdownField('profile', $this->Profile_Model->_profiles_for_dropdown(), $this->get_item_value($pUser, 'profile', -1), $this->lang->line('label_profile'), '', 'onchange="userController.refreshProfileFunctions();"');
        $this->data['profile']->setEnabled(!$pStatic);
        $this->data['name'] = new TextField('name', $this->get_item_value($pUser, 'name', ''), $this->lang->line('label_name'));
        $this->data['name']->setEnabled(!$pStatic);
        $this->data['last_name'] = new TextField('last_name', $this->get_item_value($pUser, 'last_name', ''), $this->lang->line('label_surname'));
        $this->data['last_name']->setEnabled(!$pStatic);
        $this->data['username'] = new TextField('username', $this->get_item_value($pUser, 'username', ''), $this->lang->line('label_username'));
        $this->data['username']->setEnabled(!$pStatic);
        $this->data['password_original'] = new PasswordField('password_original', $this->lang->line('label_current_password'));
        $this->data['password_original']->setEnabled(true);
        $this->data['password'] = new PasswordField('password', $this->lang->line('label_new_password'));
        $this->data['password']->setEnabled(true);
        $this->data['password_confirmation'] = new PasswordField('password_confirmation', $this->lang->line('label_confirm_new_password'));
        $this->data['password_confirmation']->setEnabled(true);
        $this->data['photo'] = new FileUploadField('photo', $this->lang->line('action_select_picture'), $this->get_item_value($pUser, 'photo', ''));
        $this->data['photo']->setStatic($pStatic);
    }

    function change_password($pUsername = null) {
        $this->accessControl('user_change_password');

        if ($pUsername == null)
            $pUsername = $this->userId;

        $user = (object) $this->User_Model->_user_by_username($pUsername);

        $this->define_form_components_for_change_password($user, true);

        $pSubmit = new SubmitButton('Aceptar', site_url($this->url . '/save_password'));
        $pSubmit->setIcon('fa-check');

        $pCancel = new Button('Cancelar', site_url($this->url . '/table'));
        $pCancel->setIcon('fa-close');
        $pCancel->addClass('btn-danger');
        $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');

        $this->get_tabs();
        $this->loadFormView($this->viewPath . '/change_password_view', $this->data, $this->lang->line('subtitle_change_password'), $pSubmit, $pCancel, array());
    }

    public function save_password() {
        $this->accessControl('user_change_password');

        $object = (object) $this->input->post();
        $user = (object) $this->User_Model->_user_by_username($object->username);

        $this->form_validation->set_rules('password_original', $this->lang->line('label_current_password'), 'required|md5');
        $this->form_validation->set_rules('password', $this->lang->line('label_new_password'), 'required|callback_validate_password[' . $user->profile . ']');
        $this->form_validation->set_rules('password_confirmation', $this->lang->line('label_confirm_new_password'), 'required|matches[password]');

        if ($this->form_validation->run() == false) {
            $this->change_password($object->username);
        } else {
            if ($this->entity_model->_update_password($object->username, $object->password_original, $object->password)) {
                $this->messages->set('success', $this->lang->line('message_update_password'));
            } else
                $this->messages->set('warning', $this->lang->line('message_update_password_error'));
            redirect($this->url);
        }
    }

    //Valida la clave ingresada por el usuario
    function validate_password($pEntry, $pProfileId) {
        $profile = $this->Profile_Model->_profile_by_id($pProfileId);

        //Validamos la longitud de la clave
        if ($profile->pass_length > strlen($pEntry)) {
            $this->form_validation->set_message('validate_password', $this->lang->line('message_password_length_first_part') . ' ' . $profile->pass_length . ' ' . $this->lang->line('message_password_length_second_part'));
            return FALSE;
        }

        //Preparo una cadena de caracteres especiales para las expresiones regulares
        $string_chars = '[!"\.\$%&\/\(\)\=\?_\-\\\<\>\+]';

        //Valido segun la complejidad minima
        switch ($profile->pass_composition) {
            case 'standard':
                if (!preg_match("/.*(?=.*[[:alpha:]]+)(?=.*\d+).*$/", $pEntry)) {
                    $this->form_validation->set_message('validate_password', $this->lang->line('message_password_contain'));
                    return FALSE;
                }
                break;
            case 'alphanumeric':
                if (!preg_match("/.*(?=.*[[:alpha:]]+)(?=.*\d+)(?=.*" . $string_chars . "+).*$/", $pEntry)) {
                    $this->form_validation->set_message('validate_password', $this->lang->line('message_password_contain_alphanumeric'));
                    return FALSE;
                }
                break;
            case 'anup':
                if (!preg_match("/.*(?=.*[[:alpha:]]+)(?=.*\d+)(?=.*" . $string_chars . "+)(?=.*[A-Z]+).*$/", $pEntry)) {
                    $this->form_validation->set_message('validate_password', $this->lang->line('message_password_contain_uppercase'));
                    return FALSE;
                }
                break;
            case 'anupnoc':
                if (!preg_match("/.*(?=.*[[:alpha:]]+)(?=.*\d+)(?=.*" . $string_chars . "+)(?=.*[A-Z]+)/", $pEntry) || $this->_has_contiguous_chars($pEntry)) {
                    $this->form_validation->set_message('validate_password', $this->lang->line('message_password_contain_character_no_contiguous'));
                    return FALSE;
                }
                break;
            case 'anupnoci':
                if (!preg_match("/.*(?=.*[[:alpha:]]+)(?=.*\d+)(?=.*" . $string_chars . "+)(?=.*[A-Z]+)/", $pEntry) || $this->_has_contiguous_chars($pEntry) || $this->_has_equal_chars($pEntry)) {
                    $this->form_validation->set_message('validate_password', $this->lang->line('message_password_contain_two_identical_character'));
                    return FALSE;
                }
                break;
        }

        return TRUE;
    }

    //Determiona si una contraseña posee caracteres contiguos.
    function _has_contiguous_chars($pPassword) {
        for ($index = 0; $index < strlen($pPassword); $index++) {
            //Obtengo los caracteres contiguos de un caracter de la contraseña.
            $contiguous = $this->_contiguous($pPassword[$index]);
            if ($contiguous != null) {
                //Si el caracter tiene contiguos (no es un caracter especial),
                //tomo la subcadena desde el caracter en adelante.
                $substr = substr($pPassword, $index + 1);
                if ((substr_count($substr, $contiguous[0]) != 0) || (substr_count($substr, $contiguous[1]) != 0)) {
                    //Si la contraseña contiene alguno de los caracteres contiguos.
                    return true;
                }
            }
        }
        return false;
    }

    //Devuelve los caracteres contiguos de un caracter
    function _contiguous($pChar) {

        //Armo el abecedario y los numeros del 0 al 9 en orden para calcular los contiguos.
        //Agrego espacios al inicio y al final para no validar por el ultimo y
        //el primer caracter, por el indice.
        $alphabet = " abcdefghijklmnñopqrstuvwxyz ";
        $numbers = " 0123456789 ";

        if (stripos($alphabet, $pChar)) {
            //Si está en el abecedario.
            $index = stripos($alphabet, $pChar);
            if ($pChar == strtoupper($pChar)) {
                //Si es una mayuscula.
                $contiguous = array(strtoupper($alphabet[$index - 1]), strtoupper($alphabet[$index + 1]));
            } else {
                $contiguous = array($alphabet[$index - 1], $alphabet[$index + 1]);
            }
        } elseif (strpos($numbers, $pChar)) {
            //Si no, si es un número.
            $index = strpos($numbers, $pChar);
            $contiguous = array($numbers[$index - 1], $numbers[$index + 1]);
        } else {
            $contiguous = null;
        }
        return $contiguous;
    }

    //Determina si una contraseña tiene más de 2 caracteres identicos
    function _has_equal_chars($pPassword) {
        for ($index = 0; $index < strlen($pPassword); $index++) {
            if (substr_count($pPassword, $pPassword[$index]) > 2) {
                //Si tiene mas de 2 caracteres identicos
                return true;
            }
        }
        return false;
    }

}
