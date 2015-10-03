<?php

require_once APPPATH . 'controllers/base/CRUDController.php';

/**
 * Description of home
 *
 * @author Jaime
 */
class Home extends CRUDController {

    var $controllerId = 0;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->url = 'back/home';
        $this->title = '';

        //cargo los modelos
        $this->load->model('base/user_model', 'User_Model');
        $this->entity_model = $this->User_Model;

        $this->load->model('client_model', 'Client_Model'); 
        $this->load->model('custom/doc_type_model', 'Doc_Type_Model');
    }
 

    function index() {
        $this->title = '';
        $this->loadView('custom/back/base/home_view', array());
    }

    //Permite editar los datos del usuario identificado.
    function userdata() {
        $pUser = $this->User_Model->_user_by_username($this->userId);
        if ($pUser == null)
            redirect('back/home');

        $this->define_form_components();

        $this->title = $this->lang->line('title_my_data');

        $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('back/home/save/2'));

        $pCancel = new Button($this->lang->line('action_cancel'), site_url('back/home'));
        $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');

        $this->loadFormView('custom/back/base/userdata_view', $this->data, $this->lang->line('subtitle_update_information'), $pSubmit, $pCancel);
    }

    //Se define la función edit para que funcione con el CRUDController
    function edit($id) {
        $this->userdata();
    }

    function define_form_components() {
        $pUser = $this->User_Model->_user_by_username($this->userId);

        $this->data['profile'] = new DropdownField('profile', $this->Profile_Model->_profiles_for_dropdown(), $this->get_item_value($pUser, 'profile', -1), $this->lang->line('label_profile'), '', 'onchange="userController.refreshProfileFunctions();"');
        $this->data['profile']->setEnabled(false);

        $this->data['name'] = new TextField('name', $this->get_item_value($pUser, 'name', ''), $this->lang->line('label_name'));
        $this->data['last_name'] = new TextField('last_name', $this->get_item_value($pUser, 'last_name', ''), $this->lang->line('label_surname'));
        $this->data['username'] = new TextField('username', $this->get_item_value($pUser, 'username', ''), $this->lang->line('label_username'));
        $this->data['username']->setEnabled(false);
        $this->data['phone'] = new TextField('phone', $this->get_item_value($pUser, 'phone', ''), $this->lang->line('label_phone'));
        $this->data['photo'] = new FileUploadField('photo', $this->lang->line('action_select_picture'), $this->get_item_value($pUser, 'photo', ''));
        $this->data['birth_date'] = new DateDropdownField('birth_date', $this->get_item_value($pUser, 'birth_date'), $this->lang->line('label_birth_date'));
        $this->data['id'] = new HiddenField('id', $pUser->username);

        $this->data['doc_type'] = new DropdownField('doc_type', $this->Doc_Type_Model->elements_for_dropdown(), $this->get_item_value($pUser, 'doc_type', -1), $this->lang->line('label_document_type'));
        $this->data['doc_number'] = new TextField('doc_number', $this->get_item_value($pUser, 'doc_number', ''), $this->lang->line('label_document_number'));

        $this->data['email'] = new TextField('email', $this->get_item_value($pUser, 'email', ''), $this->lang->line('label_email'));
        $this->data['email_confirmation'] = new TextField('email_confirmation', set_value('email_confirmation', $pUser ? $pUser->email : ''), $this->lang->line('label_confirm_email'));
        $this->data['receive_emails'] = new BooleanField('receive_emails', $this->get_item_value($pUser, 'receive_emails', false), $this->lang->line('label_receive_email_notifications'));
    }

    function save($pOperation) {
        $this->singularName = $this->lang->line('entity_user');

        parent::save($pOperation, 'back:home:userdata');
    }

    function set_validation_rules() {
        // Reglas de validacion
        $this->form_validation->set_rules('tab_active', '', '');

        // info-tab
        $this->form_validation->set_rules('profile', $this->lang->line('label_profile'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('label_name'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('label_surname'), 'required');
        $this->form_validation->set_rules('email', $this->lang->line('label_email'), 'required|valid_email');
        $this->form_validation->set_rules('email_confirmation', $this->lang->line('label_confirm_email'), 'required|matches[email]');

        $this->form_validation->set_rules('birth_date', $this->lang->line('label_birth_date'), '');
        $this->form_validation->set_rules('doc_type', $this->lang->line('label_document_type'), '');
        $this->form_validation->set_rules('doc_number', $this->lang->line('label_document_number'), '');
        $this->form_validation->set_rules('phone', $this->lang->line('label_phone'), '');
        $this->form_validation->set_rules('receive_emails', $this->lang->line('label_receive_email_notifications'), '');
    }

    function export($pModel, $pType) {
        $this->load->model('custom/' . str_replace(':', '/', $pModel), 'generic_model');

        $filters = $this->generic_model->get_filters();

        if ($filters == null)
            $filters = $this->generic_model->get_default_filters();

        $elements = $this->generic_model->all_elements($filters);

        $data_export = new stdClass();
        $data_export = $this->generic_model->data_to_export_file();

        $data = array();

        foreach ($elements as $element) {
            $aux = (array) $element;

            //quitar
            unset($aux['photo']);
            unset($aux['multimedia_object_id']);
            unset($aux['status_id']);
            unset($aux['product_catalog']);
            unset($aux['entry_cell_color']);
            unset($aux['output_cell_color']);

            $item = array();

            foreach ($aux as $attribute)
                $item[] = $attribute;

            $data[] = $item;
        }

        $this->load->library('PHPExporter');
        $this->phpexporter->prepare();

        $this->phpexporter->set_type($pType);
        $this->phpexporter->set_file_name($data_export->file_name);
        $this->phpexporter->set_column_title($data_export->column_title);
        $this->phpexporter->set_column_type($data_export->column_type);
        $this->phpexporter->set_title($data_export->title);
        $this->phpexporter->set_style_excel(isset($data_export->style) ? $data_export->style : $array = array());

        $CI = &get_instance();
        $CI->load->model('custom/setting_model', 'Setting_Model');
        $this->phpexporter->set_decimal_separator($CI->Setting_Model->get('decimalseparator'));
        $this->phpexporter->set_thousand_separator($CI->Setting_Model->get('thousandsseparator'));

        $this->phpexporter->set_delimiter($CI->Setting_Model->get('csvdelimiter'));
        $this->phpexporter->set_enclosure($CI->Setting_Model->get('csvenclosure'));

        $this->phpexporter->set_data($data);

        $this->phpexporter->do_file();
    }

    function export_with_detail($pModel = "", $pType = "") {
        $this->load->model('custom/' . str_replace(':', '/', $pModel), 'generic_model');

        $data_export = new stdClass();

        $data_export = $this->generic_model->data_to_export_file_with_detail();

        //array de la consulta
        $array_sql_result = array();
        $array_elements = array();

        $array_sql_result = $this->generic_model->elements_with_detail();

        $isProduct = FALSE;
        $array_aux_master = array();
        $array_aux_product = array();

        foreach ($array_sql_result as $sql_object) {
            foreach ($sql_object as $key => $element) {

                if ($key != 'id' && $isProduct == FALSE) {
                    $array_aux_master[$key] = $element;
                } else {
                    $isProduct = TRUE;
                    $array_aux_product[$key] = $element;
                }
            }

            $array_elements[] = $array_aux_master;
            $array_elements[] = $array_aux_product;
            $isProduct = FALSE;
        }

        //cambio del id en el catalolgo
        $change_id = NULL;


        $array_column_titles_aux = $data_export->column_title;

        $array_column_titles = $array_column_titles_aux;

        $array_result = array();

        foreach ($array_elements as $value) {
            if (isset($value['master_id']) && $value['master_id'] != $change_id) {

                array_unshift($value, 'master');
                $array_result[] = $value;

                array_unshift($array_column_titles, 'title');
                $array_result[] = $array_column_titles;

                $array_column_titles = $array_column_titles_aux;

                $change_id = $value['master_id'];
            } else if (isset($value['id']) && $value['id'] > 0) {
                array_unshift($value, 'detail');
                $array_result[] = $value;
            }
        }

        foreach ($array_result as $element) {
            $aux = (array) $element;

            $item = array();

            foreach ($aux as $attribute)
                $item[] = $attribute;

            $data[] = $item;
        }

        //tipo de dato de producto
        $product_column_type = array(
            'text',
            'text',
            'text',
            'text',
            'text',
            'text',
            'numeric',
            'text',
            'text',
            'text',
            'numeric',
            'text',
            'text',
            'text',
            'text',
            'numeric',
            'text',
            'numeric',
            'numeric',
            'numeric',
            'numeric',
            'numeric',
            'numeric',
            'date',
            'text'
        );



        //estilo de la fila master
        $style_master_row = array(
            'alignment' => array(
                'horizontal' => 'center',
            ),
            'font' => array(
                'size' => 13,
            )
        );


        $this->load->library('PHPExporter');
        $this->phpexporter->prepare();

        $this->phpexporter->set_type($pType);
        $this->phpexporter->set_file_name($data_export->file_name);
        $this->phpexporter->set_title($data_export->title);
        $this->phpexporter->set_style_excel(isset($data_export->style) ? $data_export->style : $array = array());

        $CI = &get_instance();
        $CI->load->model('custom/setting_model', 'Setting_Model');
        $this->phpexporter->set_decimal_separator($CI->Setting_Model->get('decimalseparator'));
        $this->phpexporter->set_thousand_separator($CI->Setting_Model->get('thousandsseparator'));

        $this->phpexporter->set_delimiter($CI->Setting_Model->get('csvdelimiter'));
        $this->phpexporter->set_enclosure($CI->Setting_Model->get('csvenclosure'));

        $this->phpexporter->set_data($data);
        $this->phpexporter->set_element_with_detail(TRUE);
        $this->phpexporter->set_style_master_detail($style_master_row);
        $this->phpexporter->set_column_type($product_column_type);

        $this->phpexporter->do_file();
    }

    //Devuelve el catalogo y producto filtrado
    function get_filter() {
        $elements = $this->Disney_Model->get_context_filters();

        $catalogId = null;
        if (isset($elements['product_catalog_filter'])) {
            if (is_array($elements['product_catalog_filter']))
                $catalogId = $elements['product_catalog_filter'][0];
            else
                $catalogId = $elements['product_catalog_filter'];
        }

        $catalog = null;
        if ($catalogId != null) {
            $this->load->model('custom/Product_Catalog_Model', 'Product_Catalog_Model');
            $catalog = $this->Product_Catalog_Model->get_by_id($catalogId);
        }
        $elements['catalog'] = $catalog;

        $productId = null;
        if (isset($elements['product_filter'])) {
            if (is_array($elements['product_filter']))
                $productId = $elements['product_filter'][0];
            else
                $productId = $elements['product_filter'];
        }
        $product = null;
        if ($productId != null) {
            $this->load->model('custom/Product_Model', 'Product_Model');
            $product = $this->Product_Model->get_by_id($productId);
        }
        $elements['product'] = $product;

        echo json_encode($elements);
    }

}
