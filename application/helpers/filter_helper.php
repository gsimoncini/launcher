<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Implementa algunas funciones que facilitan el tratamiento de filtros.-
 */

if (!function_exists('set_filter')) {

    //Aplica el filtro
    function set_filter($pKey, $pFilter) {
        $CI = & get_instance();
        $CI->session->set_userdata('filter_' . $pKey, $pFilter);
    }

}

if (!function_exists('unset_filter')) {

    //Quita el filtro.
    function unset_filter($pKey) {
        $CI = & get_instance();
        $CI->session->set_userdata('filter_' . $pKey, null);
    }

}

if (!function_exists('get_filter')) {

    //Devuelve el filtro.

    function get_filter($pKey) {
        $CI = & get_instance();
        $filter = $CI->session->userdata('filter_' . $pKey);
        if ($filter != null)
            $rta = $filter;
        else
            $rta = '';
        return $rta;
    }

}
?>