<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');



/*
 * Clases Default a Incluir
 */
require_once APPPATH . 'controllers/base/class/Button.php';
require_once APPPATH . 'controllers/base/class/JSButton.php';
require_once APPPATH . 'controllers/base/class/PopupButton.php';
require_once APPPATH . 'controllers/base/class/SubmitButton.php';
require_once APPPATH . 'controllers/base/class/DefaultInput.php';
require_once APPPATH . 'controllers/base/class/TextField.php';
require_once APPPATH . 'controllers/base/class/PasswordField.php';
require_once APPPATH . 'controllers/base/class/HiddenField.php';
require_once APPPATH . 'controllers/base/class/TimeField.php';
require_once APPPATH . 'controllers/base/class/DateField.php';
require_once APPPATH . 'controllers/base/class/FileField.php';
require_once APPPATH . 'controllers/base/class/FileUploadField.php';
require_once APPPATH . 'controllers/base/class/CheckField.php';
require_once APPPATH . 'controllers/base/class/DropdownField.php';
require_once APPPATH . 'controllers/base/class/DropdownItem.php';
require_once APPPATH . 'controllers/base/class/BooleanField.php';
require_once APPPATH . 'controllers/base/class/Textarea.php';
require_once APPPATH . 'controllers/base/class/RichTextarea.php';
require_once APPPATH . 'controllers/base/class/DefaultTable.php';
require_once APPPATH . 'controllers/base/class/DefaultTableCell.php';
require_once APPPATH . 'controllers/base/class/TreeTable.php';
require_once APPPATH . 'controllers/base/class/AutocompleteField.php';
require_once APPPATH . 'controllers/base/class/AmountField.php';
require_once APPPATH . 'controllers/base/class/RadioField.php';
require_once APPPATH . 'controllers/base/class/TouchField.php';
require_once APPPATH . 'controllers/base/class/IntField.php';
require_once APPPATH . 'controllers/base/class/TabItem.php';
require_once APPPATH . 'controllers/base/class/TabContainer.php';
require_once APPPATH . 'controllers/base/class/SortTableDropdown.php';
require_once APPPATH . 'controllers/base/class/SortTableDropdownItem.php';
require_once APPPATH . 'controllers/base/class/DateDropdownField.php';
require_once APPPATH . 'controllers/base/class/NumericField.php';
require_once APPPATH . 'controllers/base/class/DropdownDetailed.php';
require_once APPPATH . 'controllers/base/class/DropdownItemDetailed.php';
require_once APPPATH . 'controllers/base/class/ToggleButton.php';
require_once APPPATH . 'components/base/ExportTableView.php';


/*
 * Es la clase madre de todo el sitema. En ella se implementan mecanismos básicos
 * para la creación simple de ventanas, formularios, vistas, etc.
 *
 * @author Mirco Bombieri
 */

class BaseController extends CI_Controller {

    //Identificador del Controlador
    var $controllerId = -1;
    //Titulo del Controlador
    var $title = null;
    //URL del Controlador
    var $url;
    //URL de las vistas
    var $viewPath;
    //Usuario que está ejecutando el controlador
    var $userId;
    //Permisos del usuario sobre el controlador
    var $userRights = array();
    //Vista de estructura base
    var $baseView = 'base/base_view';
    //Array con datos para mostrar
    var $viewData = array();

    /* #######################
     *  El siguiente atributo permite definir si el controlador se comporta como
     *  una clase para el frontend o para el backend. En otras palabras, si
     *  exige autenticación o no.
     * #######################
     */
    var $requireLogin = true;
    //Nombre del controlador javascript
    var $javascriptName = null;

    //Constructor
    public function BaseController($pControllerId) {
        parent::__construct();
		var_dump($pControllerId);
        ini_set('memory_limit', '2048M'); // 2GB de memoria nunca son suficientes..
        //seteo la zona horaria por defecto
        date_default_timezone_set($this->config->item('default_timezone'));

        //Definimos el encoding.
        $this->setEncodingHeader();
        //Definimos si utiliza cache de navegador o no.
        if ($this->config->item('enable_nav_cache'))
            $this->setNoCacheHeader();


        //Verifico si exige autenticacion o no
        if ($this->requireLogin) {
            //Cargo el modelo de menu.
            $this->load->model('base/Menu_Model');

            //Obtengo el usuario de session
            $this->userId = $this->session->userdata('user_id');
            //Verifico si está identificado aún.
            if ($this->userId == null) {
                if ($this->input->is_ajax_request())
                    $this->ajax_session_expire();
                else
                    redirect('login/logout');
            }

            //Cargamos los permisos del usuario sobre el controlador
            if ($pControllerId != null) {
                $this->userRights = $this->User_Model->permission_for($pControllerId, $this->userId);
                //Si no posee permisos
                if ($this->userRights == null & $pControllerId != 1)
                //redirecciono a página en blanco (inicio)
                    redirect('back/home');
            }
        }
    }

    //Verifica que la sesión se encuentre activa
    function ajax_session_expire() {
        if ($this->userId == null) {
            exit(json_encode(array('status' => false, 'message' => 'La sesión caducó.', 'redirect' => site_url())));
        } else
            exit(json_encode(array('status' => true, 'message' => 'Ok')));
    }

    //Coloca una cabecera por el encoding de la página
    function setEncodingHeader() {
        header('Content-type: text/html; charset=' . $this->config->item('charset') . '');
    }

    //Coloca cabecera para no utilizar cache de navegador
    function setNoCacheHeader() {
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    //Carga una pagina completa
    function loadView($pCustomView, $pData, $pOperation = '', $pCancel = null, $pBaseView = null) {

        //Titulo de la página
        $this->viewData['view_title'] = $this->title;
        $this->viewData['view_operation'] = $pOperation;

        //Defino la vista personalizada a cargar
        $this->viewData['customView'] = $pCustomView;

        //Defino el link o boton de cancelar
        $this->viewData['cancel_button'] = $pCancel;

        //Mezclo los datos a incluir en la vista con los demas
        $this->viewData = array_merge($this->viewData, $pData);

        if ($pBaseView == null)
            $pBaseView = $this->baseView;
        //Cargo la página sobre la vista BASE
        $this->load->view($pBaseView, $this->viewData);
    }

    //Permite definir codigo javascript extra
    function setExtraJavascript($pCode) {
        $this->viewData['extra_js'] = $pCode;
    }

    //Permite definir un array de archivos javascript a cargar
    function setJavascriptToLoad($pFilenameArrays) {
        $this->viewData['extra_js_array'] = $pFilenameArrays;
    }

    //Permite definir codigo javascript a ejecutar en OnLoad del documento
    function setOnLoadJavascript($pCode) {
        $this->viewData['on_load_js'] = $pCode;
    }

    //Carga una vista de un formulario
    /*
     * pSubmit es un objeto que tiene le URL, el texto y el estado del boton ENVIAR.
     * pCancel es un objeto que tiene la URL, el texto y el estado del boton CANCELAR.
     */
    function loadFormView($pCustomView, $pData, $pOperation, $pSubmit, $pCancel, $pTabs = null) {
        //Accion para confirmar el formulario
        $this->viewData['form_url'] = $pSubmit->getUrl();
        $this->viewData['form_submit'] = $pSubmit;
        //Accion para cancelar el formulario
        $this->viewData['form_cancel'] = $pCancel;
        //Tabs
        $this->viewData['tabs'] = $pTabs != null ? new TabContainer($pTabs) : null;
        //Indico que vista contiene la estructura de panel
        $this->viewData['form_content_view'] = 'base/base_panel_view';
        //Indico que vista contiene los items del formulario
        $this->viewData['form_content_panel_view'] = $pCustomView;

        //Cargo la pagina
        $this->loadView('base/base_form_view', $pData, $pOperation);
    }

    //Carga una vista de un formulario limpia
    /*
     * pSubmit es un objeto que tiene le URL, el texto y el estado del boton ENVIAR.
     * pCancel es un objeto que tiene la URL, el texto y el estado del boton CANCELAR.
     */
    function loadFormCleanView($pCustomView, $pData, $pOperation, $pUrl) {
        //Accion para confirmar el formulario
        $this->viewData['form_url'] = $pUrl;
        //Indico que vista contiene los items del formulario
        $this->viewData['form_content_view'] = $pCustomView;

        //Cargo la pagina
        $this->loadView('base/base_form_view', $pData, $pOperation);
    }

    //Carga una vista de formulario en un popup modal javascript
    /*
     * Ambos botones deben ser del tipo JSButton.
     * pSubmit es un objeto que tiene le URL, el texto y el estado del boton ENVIAR.
     * pCancel es un objeto que tiene la URL, el texto y el estado del boton CANCELAR.
     */
    function loadFormPopupView($pCustomView, $pData, $pTitle, $pSubmit, $pCancel, $pPopupClass = 'modal-lg', $pTabs = null) {
        //Accion para confirmar el formulario
        $this->viewData['form_submit'] = $pSubmit;
        //Accion para cancelar el formulario
        $this->viewData['form_cancel'] = $pCancel;
        //Defino el titulo del popup
        $this->viewData['modal_title'] = $pTitle;
        //Indico que vista contiene los items del formulario
        $this->viewData['form_content_view'] = $pCustomView;
        //Clase sobre el modal
        $this->viewData['modal_class'] = $pPopupClass;
        //Tabs
        $this->viewData['tabs'] = $pTabs != null ? new TabContainer($pTabs) : null;

        //Cargo la vista del popup con la vista de formulario dentro, y dentro de ella la vista de los inputs
        $this->loadView($pCustomView, $pData, null, null, $pTabs != null ? 'base/base_panel_popup_view' : 'base/base_popup_view');
    }

    function accessControl($pFunction = 'controller') {
        if (!isset($this->userRights[$pFunction]) || !$this->userRights[$pFunction]) {
            $this->messages->set('error', $this->lang->line('message_no_permission'));
            redirect('back/home');
        }
    }

    function actionButton($pName, $pIcon, $pUrl, $pPermission, $pWithParameters = false, $pValidationMethod = '', $pTooltip = '') {
        if ($this->userRights[$pPermission]) {
            $button = new Button($pName, $pUrl == null ? '#' : site_url($pUrl));

            if ($pWithParameters && $this->javascriptName != null)
                $button->setAttributes('onclick="baseController.prepareActionUrl(event, this, ' . $this->javascriptName . '.tableTarget' . ($pValidationMethod != '' ? ', ' . $this->javascriptName . '.' . $pValidationMethod : '') . ');"');

            $button->setIcon($pIcon);

            if ($pTooltip != '')
                $button->setTooltip($pTooltip);

            $button_html = $button->generate();
        } else
            $button_html = '';

        return $button_html;
    }

    function javascriptActionButton($pName, $pIcon, $pPermission, $pAttributes, $pUrl = null, $pTooltip = '') {
        if ($this->userRights[$pPermission]) {
            $button = new Button($pName, $pUrl == null ? '#' : site_url($pUrl));

            $button->setAttributes($pAttributes);
            $button->setIcon($pIcon);

            if ($pTooltip != '')
                $button->setTooltip($pTooltip);

            $button_html = $button->generate();
        } else
            $button_html = '';

        return $button_html;
    }

    function popupActionButton($pName, $pIcon, $pUrl, $pPermission) {
        if ($this->userRights[$pPermission]) {
            $button = new PopupButton($pName, site_url($pUrl), 'baseController.popup(event, this);');

            $button->setIcon($pIcon);

            $button_html = $button->generate();
        } else
            $button_html = '';

        return $button_html;
    }

}
