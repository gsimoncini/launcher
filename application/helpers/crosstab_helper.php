<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('crosstab_meta')) {

    /**
     * Obtiene información de meta data para crosstab
     */
    function crosstab_meta($pKey) {

        if ($pKey == null)
            return false;

        $meta['product_request_quantity'] = array('text' => 'Qty'
            , 'calc' => array('sum', 'request_quantity'));
        $meta['order_id'] = array('text' => 'OrderId'
            , 'calc' => array('MAX', 'order_id'));

        return $meta[$pKey];
    }

}

if (!function_exists('crosstab_formatter')) {

    /**
     * Formatea el contenido
     */
    function crosstab_formatter($pVal, $pFormat, $settings) {

        if ($pVal == null)
            return 0;

        switch ($pFormat) {
            case '#calls':
                return number_format($pVal, 0, $settings['decimalseparator'], $settings['thousandsseparator']);
                break;

            default:
                return $pVal;
                break;
        }
    }

}


if (!function_exists('crosstab_title_description')) {

    /**
     * Formatea el contenido
     */
    function crosstab_title_description($pKey) {

        if ($pKey == null)
            return $pKey;

        switch ($pKey) {
            //###REPORTING
            case 'client_name':
                return 'Cliente';
                break;
            case 'client_group_name':
                return 'Grupo';
                break;
            default:
                return $pKey;
                break;
        }
    }

}

function cross_create_where($data, $period, $type = '', $alias = '') {
    if ($alias != '')
        $alias = $alias . '.';
    $sql = '';
    //Comportamiento normal
    $count_item = 0;
    $flag = true;
    $flag_where = true;
    $notdata = true;
    foreach ($data as $key => $item) {
        if ($key != 'desde' && $key != 'hasta') {
            $notdata = false;
            if ($count_item > 0) {
                if (!empty($data[$key])) {
                    $sql .= ' AND ';
                }
            } else {
                if ($flag) {
                    $sql .= ' where ';
                    $flag = false;
                }
            }
            if (count($data[$key]) > 1) {
                $flag_where = false;
                $count_item++;
                $extrac = $data[$key];
                $in = '';
                $in .= '(';
                for ($a = 0; $a < count($extrac); $a++) {
                    $in .= '\'' . $extrac[$a] . '\'';
                    if ($a != (count($extrac) - 1 )) {
                        $in .= ',';
                    }
                }
                $in .= ')';
                $sql .= $alias . $key . ' in ' . $in;
            } else {
                if (!empty($data[$key][0]) && (is_array($data[$key]))) {
                    $flag_where = false;
                    $count_item++;
                    $sql .= '(' . $alias . $key . ' = \'' . $data[$key][0] . '\')';
                } else {
                    if (!empty($data[$key])) {
                        $flag_where = false;
                        $count_item++;
                        $sql .= '(' . $alias . $key . ' = \'' . $data[$key] . '\')';
                    }
                }
            }
        }
    }
    /* Filtro fecha desde - hasta.. Sumarle 1 día a la fecha hasta asi funciona bien.. Hacerlo en capa de datos */
    /*  $data['hasta'] = calculaFecha("days", +1, $data['hasta']);
      $data['hasta'] = explode(' ', $data['hasta']);
      $data['hasta'] = $data['hasta'][0];

      $data['desde'] = str_replace('/', '-', $data['desde']);
      $data['hasta'] = str_replace('/', '-', $data['hasta']);
      if ($notdata) {
      $sql .= ' WHERE ' . $period . ' between \'' . $data['desde'] . '\' AND \'' . $data['hasta'] . '\'';
      } else {
      if ($flag || (!$flag_where)) {
      $sql .= ' AND ' . $period . ' between \'' . $data['desde'] . '\' AND \'' . $data['hasta'] . '\'';
      } else {
      $sql .= ' ' . $period . ' between \'' . $data['desde'] . '\' AND \'' . $data['hasta'] . '\'';
      }
      }
     */
    return $sql;
}

function getTypePeriod($data) {
    if (!empty($data['typePeriod'])) {
        $typeperiod = $data['typePeriod'];
        unset($data['typePeriod']);
    }
    return $typeperiod;
}
