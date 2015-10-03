<?php

require_once APPPATH . 'controllers/base/BaseController.php';

/**
 * Controlador que brinda la funcionalidad para identificar un usuario.
 *
 * @author Mirco Bombieri
 */
class Login extends BaseController {

    var $requireLogin = false;

    function Login() {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        parent::BaseController(-1);
        $this->baseView = 'base/base_login_view';
    }

    //formulario de login
    function index() {
        //Elementos del formulario
        $this->data['inButton'] = new SubmitButton('Ingresar', site_url('login/access'));
        $this->data['inButton']->setIcon('fa-sign-in');
        $this->data['inButton']->setConfirm(false);

        $this->data['user'] = new TextField('user', $this->form_validation->get('user'), $this->lang->line('system_user'));
        $this->data['pass'] = new PasswordField('password', $this->lang->line('system_password'));

        $this->title = $this->lang->line('system_identify');

        $js = "function setFocus(){document.getElementById('user').focus();}";
        $this->setExtraJavascript($js);

        $this->loadView('custom/login/login_view', $this->data, $this->lang->line('system_identify'), null, 'base/base_login_view');
    }

  //Cierra la sesión activa.
    function logout() {
        //se previene el warning de php cuando la session caduco
        $this->User_Model->make_logout();
        $this->messages->set('warning', $this->lang->line('message_application_closed'));
        redirect(base_url(), 'refresh');
    }

  //Funcion que valida los datos de acceso.
    function access() {
        $this->form_validation->set_rules('user', $this->lang->line('system_user'), 'required');
        $this->form_validation->set_rules('password', $this->lang->line('system_password'), 'required|md5');
        if ($this->form_validation->run() == false)
            $this->login($this->input->post('ref_url'));
        else {
            $username = $this->input->post('user');
            $pass = $this->input->post('password');
            $ref_url = $this->input->post('ref_url');
            $result = $this->User_Model->make_login($username, $pass, $ref_url);

            if ($result['status']) {
                //Verifico la rotación de clave.
                if ($this->User_Model->_must_rotate_pass($result['user'])) {
                    redirect('/back/users/change_password/' . $result['user']->username . '/null/' . true);
                }
            } else {
                $this->messages->set($result['message_type'], $result['message']);
            }
            redirect($result['redirect']);
        }
    }

    //Permite recuperar la clave del usuario
    function password_recovery() {
        //Elementos del formulario

        $this->data['okButton'] = new SubmitButton($this->lang->line('action_recover_password'), site_url('login/get_new_password'));
        $this->data['okButton']->setIcon('fa-key');
        $this->data['okButton']->setConfirm(false);

        $this->data['cancelButton'] = new Button($this->lang->line('action_back'), site_url('login'));
        $this->data['cancelButton']->setIcon('fa-arrow-left');
        $this->data['cancelButton']->addClass('btn-danger');

        $this->data['user'] = new TextField('user', $this->form_validation->get('user'), $this->lang->line('system_user'));
        $this->data['email'] = new TextField('email', null, $this->lang->line('system_email'));

        $this->title = $this->lang->line('subtitle_recovery_password');

        $js = "function setFocus(){document.getElementById('user').focus();}";
        $this->setExtraJavascript($js);

        $this->loadView('custom/login/password_recovery_view', $this->data, $this->lang->line('subtitle_recovery_password'), null, 'base/base_login_view');
    }

    //Genera y envia una nueva contraseña
    function get_new_password() {

        $this->form_validation->set_rules('user', $this->lang->line('system_user'), 'required');
        $this->form_validation->set_rules('email', $this->lang->line('system_email'), 'required|valid_email');

        if ($this->form_validation->run() == FALSE)
            $this->password_recovery();
        else {
            $username = $this->input->post('user');
            $email = $this->input->post('email');
            $newPassword = $this->User_Model->_generate_new_password($username, $email);
            if ($newPassword == null) {
                //Los datos ingresados son incorrectos
                $this->messages->set('error', $this->lang->line('message_incorrect_data'));
                redirect('login/password_recovery');
            }

            //Envia la nueva clave al usuario por correo electrónico.
            $this->send_new_password($email, $newPassword);

            $this->messages->set('success', $this->lang->line('message_send_new_password'));
            redirect('login');
        }
    }

    //Envia un email
    function send_email($pTo, $pFromName, $pTitle, $pImage, $pBody) {
        //cargo la libreria de Email
        $this->load->library('email');

        //Armos el encabezado del correo
        $this->email->from($this->config->item('email_from'), $pFromName);
        $this->email->to($pTo);
        $this->email->subject($pTitle);

        //Armo el cuerpo del correo
        $email = $this->load->view('custom/back/base/email_view', '', true);
        $email = str_replace('{IMAGEN}', $pImage, $email);
        $email = str_replace('{TITULO}', $pTitle, $email);
        $email = str_replace('{CONTENIDO}', $pBody, $email);
        $this->email->message($email);
        $this->email->send();
    }

    //Envia un mail de Cuenta Amenazada a aquellas cuentas que no deban bloquearse
    //en caso de superar el máximo número de intentos de acceso fallidos.
    function send_email_to_threatened_account($pTo) {

        $title = $this->lang->line('label_message_thread_count');
        $image = img(array('src' => base_url() . $this->config->item('icon'), 'border' => '0'), TRUE);
        $body = "<font face=\"arial\" size=\"2\">";
        $body .= "<br/>" . $this->lang->line('label_message_thread_count_text_first_part') . ' ' . anchor(base_url()) . ' ' . $this->lang->line('label_message_thread_count_text_second_part') . "<br/>";
        $body .= "<br/>" . $this->lang->line('label_message_thread_count_text_third_part') . "<br/>";
        $body .= "<br/> </font>";


        $this->send_email($pTo, $this->lang->line('label_thread_count'), $title, $image, $body);
    }

    //Envia un mail ante un cambio de clave
    function send_new_password($pTo, $pNewPassword) {

        $title = $this->lang->line('label_message_recovery_password');
        $image = img(array('src' => base_url() . $this->config->item('icon'), 'border' => '0'), TRUE);
        $body = "<font face=\"arial\" size=\"2\"> ";
        $body .= "<br/>" . $this->lang->line('label_message_recovery_password_text_first_part') . ' ' . anchor(base_url()) . " <br/>";
        $body .= "<br/>" . $this->lang->line('label_message_recovery_password_text_second_part') . ' ' . " <b>" . $pNewPassword . "</b><br/>";
        $body .= "<br/></font>";


        $this->send_email($pTo, $this->lang->line('label_recovery_password'), $title, $image, $body);
    }

}

?>
