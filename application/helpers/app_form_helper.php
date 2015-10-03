<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('form_group')) {

    /**
     * Genera un grupo de formulario estándar para un componente en una vista
     * @param type $pField El componente. Implemementa generate().
     * @param type $pWidth El tamaño de la fila
     * @return string
     */
    function form_group($pField, $pWidth = 4, $pMandatory = false) {
        $html = '';

        $html .= '<div class="col-md-' . $pWidth . ($pField->isStatic() ? ' form-group-horizontal' : '') . '">';
        $html .= '<div class="form-group form-group-sm">';

        if ($pField->_type == 'checkbox') {
            if ($pField->isStatic() == false)
                $html .= '<label></label>';

            $html .= $pField->generate(true);
        } else {
            if (method_exists($pField, 'printLabel'))
                $html .= $pField->printLabel($pMandatory);

            $html .= $pField->generate();
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

}

if (!function_exists('table_search')) {

    /**
     * Genera un grupo de formulario estándar para un buscar en una tabla
     * @param type $pField El componente. Implemementa generate().
     * @param type $pWidth El tamaño de la fila
     * @return string
     */
    function table_search($pField, $pWidth = 4) {
        $html = '';

        $html .= '<div class="col-md-' . $pWidth . '">';
        $html .= '<div class="input-group input-group-sm table-search">';

        $html .= $pField->generate();
        $html .= '<span class="input-group-addon"><i class="fa fa-search"></i></span>';

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

}

if (!function_exists('table_filter')) {

    /**
     * Genera un panel para poder visualizar los filtros aplicados
     * @param type $pWidth El tamaño de la fila
     * @return string
     */
    function table_filter($pWidth = 8) {
        $CI = &get_instance();
        $html = '';

        $html .= '<div class="col-md-' . $pWidth . '">';
        $html .= '<div class="filter-box well well-sm">';
        $html .= '<div class="filter">' . $CI->lang->line('system_filter') . ': <span class="filter-types">' . $CI->lang->line('label_no') . '</span></div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

}

if (!function_exists('form_group_addon')) {

    /**
     * Genera un grupo de formulario estándar con un addon, para un componente en una vista
     * @param type $pField El componente. Implemementa generate().
     * @param type $pAddon El addon. Implemementa generate().
     * @param type $pWidth El tamaño de la fila
     * @param type $pMandatory Marca de obligatorio
     * @return string
     */
    function form_group_addon($pField, $pAddon, $pWidth = 4, $pMandatory = false) {
        $html = '';

        $html .= '<div class="col-md-' . $pWidth . '">';

        if (method_exists($pField, 'printLabel'))
            $html .= $pField->printLabel($pMandatory);

        $html .= '<div class="input-group input-group-sm">';
        $html .= $pField->generate();

        $html .= '<span class="input-group-btn">' . $pAddon->generate() . '</span>';

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

}

if (!function_exists('special_json_encode')) {

    function special_json_encode($pText) {
        $json = json_encode($pText);

        $text = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $json);

        return htmlspecialchars($text);
    }

}

if (!function_exists('prefix')) {

    function prefix($pWord, $pPrefix) {
        if (strlen($pWord) < strlen($pPrefix)) {
            $tmp = $pPrefix;
            $pPrefix = $pWord;
            $pWord = $tmp;
        }

        $pWord = substr($pWord, 0, strlen($pPrefix));

        if (strtolower($pPrefix) == strtolower($pWord) || $pPrefix == '' || $pPrefix == NULL) {
            return TRUE;
        }

        return FALSE;
    }

}

if (!function_exists('subfix')) {

    function subfix($pWord, $pSuffix) {

        if (strlen($pWord) < strlen($pSuffix)) {
            $tmp = $pPrefix;
            $pPrefix = $pWord;
            $pWord = $tmp;
        }

        $length_word = strlen($pWord);
        $length_suffix = strlen($pSuffix);

        $dif = $length_word - $length_suffix;

        $pWord = substr($pWord, $dif, $length_word);

        if (strtolower($pSuffix) == strtolower($pWord) || $pSuffix == '' || $pSuffix == NULL) {
            return TRUE;
        }

        return FALSE;
    }

}

if (!function_exists('file_extension')) {

    function file_extension($pfileUploadExtension, $pExtensionCondition) {

        if (substr($pExtensionCondition, 0, 1) != '.' && $pExtensionCondition != '' && $pExtensionCondition != NULL)
            $pExtensionCondition = '.' . $pExtensionCondition;

        if (strtolower($pfileUploadExtension) == strtolower($pExtensionCondition) || $pExtensionCondition == '' || $pExtensionCondition == NULL)
            return TRUE;
        else
            return FALSE;
    }

}


if (!function_exists('list_files')) {

    function list_files($carpeta, $url) {
        $return = '';
        if (is_dir($carpeta)) {
            if ($dir = opendir($carpeta)) {
                while (($archivo = readdir($dir)) !== false) {
                    if ($archivo != '.' && $archivo != '..' && $archivo != '.htaccess') {
                        $return .= '<li  class="list-group-item"><a target="_blank" href="' . $url . $archivo . '">' . $archivo . '</a></li>';
                    }
                }
                closedir($dir);
            }
        }
        return $return;
    }

}

