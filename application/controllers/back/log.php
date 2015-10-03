<?php

require_once APPPATH . 'controllers/base/BaseController.php';

/**
 * Description of log
 *
 * @author Mirco Bombieri
 */
class Log extends BaseController {

    var $controllerId = 3;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->title = $this->lang->line('title_audit');

        //cargo los modelos
        $this->load->model('base/log_model', 'Log_Model');

        //valido si tiene permiso de lectura del controlador
        $this->accessControl();
    }

    //Genera una tabla con diferente contenido dependiendo del nombre de la función pasado en pRef.
    function table($pRef) {
        $this->accessControl('log_access');

        $users_access = $pRef == 'access' ? $this->Log_Model->_users_access() : $this->Log_Model->_users_failed_access();
        $subtitle = $pRef == 'access' ? $this->lang->line('subtitle_list_access') : $this->lang->line('subtitle_list_access_failed');

        $head = array($this->lang->line('table_head_user'), $this->lang->line('table_head_date'), $this->lang->line('table_head_hour'));
        $items = array();

        foreach ($users_access AS $access) {
            $user = new DefaultTableCell($access->username, 'text_column');
            $date = new DefaultTableCell($access->date, 'text_column');
            $time = new DefaultTableCell($access->time, 'text_column');

            $items[] = array($user, $date, $time);
        }

        $table = new DefaultTable('users_access_table', $head, $items);
        $this->data['table'] = $table->generate();

        //Formulario de Filtro
        $filters = $this->get_filters();

        $this->data['filter_username'] = new DropdownField('filter_username', $this->User_Model->_users_for_dropdown(true), $filters['filter_username'], $this->lang->line('label_user'));
        $this->data['filter_date_from'] = new DateField('filter_date_from', $filters['filter_date_from'], $this->lang->line('label_date_from'));
        $this->data['filter_date_to'] = new DateField('filter_date_to', $filters['filter_date_to'], $this->lang->line('label_date_until'));

        $this->data['filter_button'] = new SubmitButton($this->lang->line('action_filter'), site_url('back/log/set_filter/' . $pRef));
        $this->data['filter_button']->setIcon('fa-filter');

        $this->data['filter_unset'] = new Button($this->lang->line('action_remove_filter'), site_url('back/log/unset_filter/' . $pRef));
        $this->data['filter_unset']->addClass('btn-danger');

        $this->loadView('custom/back/base/user_access_table_view', $this->data, $subtitle);
    }

    //Aplica el filtro por nombre, fecha desde y hasta sobre las tabla de accesos deusuarios
    function set_filter($pRef) {
        $this->Log_Model->set_filters($this->input->post());

        redirect('back/log/table/' . $pRef);
    }

    //Quita el filtro sobre la tabla de accesos de usuarios
    function unset_filter($pRef) {
        $this->Log_Model->unset_filters();

        $filters = $this->Log_Model->get_default_filters();
        $this->Log_Model->set_filters($filters);

        redirect('back/log/table/' . $pRef);
    }

    //Obtiene los filtros aplicados o los filtros predeterminados.
    function get_filters() {
        $filters = $this->Log_Model->get_filters();

        if ($filters == null)
            $filters = $this->Log_Model->get_default_filters();

        return $filters;
    }

}
