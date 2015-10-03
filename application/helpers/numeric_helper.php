<?php

//Agrega ceros a la izquierda, muy util en imprecion de documentos.
if (!function_exists('app_form_helper')) {

    function add_left_zero_to_number($pNumber, $pFinalLong) {
        $newNumber = '';
        $index = strlen($pNumber);

        for ($index; $index < $pFinalLong; $index++) {
            $newNumber .='0';
        }
        $newNumber .= (string) $pNumber;

        return $newNumber;
    }

}