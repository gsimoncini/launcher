<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DropdownItem
 *
 * @author Mirco Bombieri
 */
class DropdownItemDetailed {

    var $id;
    var $values = array();

//    function DropdownItemDetailed($pId, $pValue1 = '', $pValue2 = '', $pValue3 = '', $pValue4 = '', $pValue5 = '') {
//        $this->setId($pId);
//        $pValues = array('id' => $pId, 'value_1' => $pValue1, 'value_2' => $pValue2, 'value_3' => $pValue3, 'value_4' => $pValue4, 'value_5' => $pValue5);
//        $this->setValues($pValues);
//    }
    function DropdownItemDetailed($pId, $pArray) {
        $this->setId($pId);
        $values = array();
        $values['id'] = $pId;
        $i = 1;
        foreach ($pArray AS $item) {
            $values['value_' . $i] = $item;
            $i++;
        }
        $this->setValues($values);
    }

    /*
     * Métodos Accesores
     */

    public function getId() {
        return $this->id;
    }

    public function getValues() {
        return $this->value;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function setValues($pValue) {
        $this->value = $pValue;
    }

}

?>
