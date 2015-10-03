<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Implementa algunas funciones que facilitan el tratamiento de fechas.-
 */

if (!function_exists('to_mysql_date')) {

    function to_mysql_date($pDate, $pSeparator = '/') {
        if ($pDate == null)
            return null;

        $parts = explode($pSeparator, $pDate);

        $day = $parts[0];
        $month = $parts[1];
        $year = $parts[2];

        return $year . '-' . $month . '-' . $day;
    }

}

if (!function_exists('to_natural_date')) {

    function to_natural_date($pDate, $pTime = false, $pSeparator = '/') {
        if ($pDate == null)
            return null;

        $parts = explode(' ', $pDate);

        $date = $parts[0];
        $date_parts = explode('-', $date);

        $day = $date_parts[2];
        $month = $date_parts[1];
        $year = $date_parts[0];

        if (isset($parts[1]) && $pTime) {
            $time = $parts[1];
            $time_parts = explode(':', $time);

            $hour = $time_parts[0];
            $minute = $time_parts[1];
        }

        return $day . $pSeparator . $month . $pSeparator . $year . (isset($parts[1]) && $pTime ? ' ' . $hour . ':' . $minute : '' );
    }

}

//Valida una fecha en formato dd-mm-yyyy
if (!function_exists('valid_date')) {

    function valid_date($pDate, $pSeparator = '/') {
        $dates = explode($pSeparator, $pDate);

        if (sizeof($dates) < 3)
            return false;

        return checkdate((int) $dates[1], (int) $dates[0], (int) $dates[2]);
    }

}


//Devuelve una fecha escrita en palabras en español
if (!function_exists('to_text_date')) {

    function to_text_date($pDate, $pLocale = 'es_ES') {
        $date = explode('-', $pDate);

        setlocale(LC_TIME, $pLocale);
        $string = ucwords(strftime('%d de %B de %Y', mktime(0, 0, 0, $date[1], $date[2], $date[0])));

        return $string;
    }

}

function calculaFecha($modo, $valor, $fecha_inicio = false) {

    if ($fecha_inicio != false) {
        $fecha_base = strtotime($fecha_inicio);
    } else {
        $time = time();
        $fecha_actual = date("Y-m-d", $time);
        $fecha_base = strtotime($fecha_actual);
    }
    $calculo = strtotime("$valor $modo", "$fecha_base");
    return date("Y-m-d h:i:s", $calculo);
}

/* End of file date_helper.php */
/* Location: ./system/helpers/date_helper.php */