<?php

require_once APPPATH . 'controllers/base/BaseController.php';

/**
 * Envio de emails
 *
 * @author Jaime
 */
class Emails extends BaseController {

    var $controllerId = 20;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->load->model('/custom/People_Model', 'People_Model');
        $this->load->model('/custom/States_Model', 'States_Model');
        $this->load->model('/custom/Countries_Model', 'Countries_Model');
        $this->load->model('/custom/Deliveries_Model', 'Deliveries_Model');
        $this->load->model('/custom/Person_Categories_Model', 'Person_Categories_Model');
        $this->load->model('/custom/Products_Model', 'Products_Model');
        $this->load->model('/custom/Export_Model', 'Export_Model');
        $this->load->model('/custom/Notifications_Model', 'Notifications_Model');
        $this->load->model('/custom/Afip_Model', 'Afip_Model');
        $this->load->model('base/Session_Model', 'Session_Model');
    }

    /**
     * Envía por mail las notificaciones por vencer
     *
     */
    function notifications_email() {

        $this->load->library('email');
        $this->email->from($this->config->item('email_from'));
        $this->email->to($this->config->item('email_to'));
        $titulo = $this->lang->line('title_expiry_observations');
        $this->email->subject($titulo);

        $today = '<br><h3>' . $this->lang->line('label_expiry_observations_today') . '</h3>';
        $next = '<br><h3>' . $this->lang->line('label_expiry_observations_next_days') . '</h3>';

        $notifications = $this->Notifications_Model->notifications_5_days();

        foreach ($notifications as $n) {
            $person = $this->People_Model->get_by_id($n->person);
            if ($n->expiration_date == date('Y-m-d')) {
                $today.= $this->lang->line('label_customer') . ': <b>' . $person->surname . ', ' . $person->name . '</b><br>';
                $today.= $this->lang->line('label_observations') . ':<br>' . $n->observation . '<br><br>';
            } else {
                $next.= $this->lang->line('label_date') . ': <b>' . date('d-m-Y', strtotime($n->expiration_date)) . '</b><br>';
                $next.= $this->lang->line('label_customer') . ': <b>' . $person->surname . ', ' . $person->name . '</b><br>';
                $next.= $this->lang->line('label_observations') . ':<br>' . $n->observation . '<br><br>';
            }
        }

        $contenido = "<font face=\"arial\" size=\"2\"> ";
        $contenido .= $today;
        $contenido .= $next;
        $contenido .= "<br/></font>";
        $CI = & get_instance();
        $mensaje = $CI->load->view('custom/back/base/email_view', array(), true);
        $mensaje = str_replace('{TITULO}', $titulo, $mensaje);
        $mensaje = str_replace('{CONTENIDO}', $contenido, $mensaje);
        $mensaje = str_replace('{IMAGEN}', img('img/base/logo.png'), $mensaje);
        $this->email->message($mensaje);

        $this->email->send();
    }

}

?>