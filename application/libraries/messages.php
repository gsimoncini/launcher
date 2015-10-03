<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Messages {

    function Messages() {
        $CI = & get_instance();
    }

    //tipo={error,success, warning} se corresponde con selectores css
    function set($pType = 'error', $pMsg = '') {
        $CI = & get_instance();
        $CI->session->set_flashdata('typeMsg', $pType);
        $CI->session->set_flashdata('msg', $pMsg);
    }

    //para mostrar errores de excepciones
    function show() {
        try {
            $CI = & get_instance();
            $type = $CI->session->flashdata('typeMsg');
            $msg = $CI->session->flashdata('msg');
            if ($msg != null) {
                if ($type == 'error')
                    $type = 'danger';

                echo '<div class="alert alert-' . $type . '">' . $msg . '</div>';
            }
        } catch (Exception $e) {
            
        }
    }

}

?>
