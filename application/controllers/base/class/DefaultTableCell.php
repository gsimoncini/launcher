<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultTableCell
 *
 * @author Mirco Bombieri
 */
class DefaultTableCell {

    var $data = '';
    var $class = '';
    var $colspan;
    var $style;

    function DefaultTableCell($pData, $pClass, $pStyle = null, $pColspan = null) {
        $this->data = $pData;
        $this->class = $pClass;
        $this->colspan = $pColspan;
        $this->style = $pStyle;
    }

    //Convierte el objeto en un array
    public function asArray() {
        if ($this->data == '') {
            return '-';
        }
        return $this->object2array($this);
    }

    //Convierte el objeto en array de forma recursiva
    public function object2array($valor) {//valor
        if (!(is_array($valor) || is_object($valor))) { //si no es un objeto ni un array
            $dato = $valor; //lo deja
        } else { //si es un objeto
            foreach ($valor as $key => $valor1) {
                if ($valor1 != null && $valor1 != '')
                    $dato[$key] = $this->object2array($valor1);
            }
        }
        return $dato;
    }

}

?>
