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
class DropdownItem {

    var $id;
    var $value;

    function DropdownItem($pId, $pValue) {
        $this->setId($pId);
        $this->setValue($pValue);
    }

    /*
     * Métodos Accesores
     */

    public function getId() {
        return $this->id;
    }

    public function getValue() {
        return $this->value;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function setValue($pValue) {
        $this->value = $pValue;
    }

}

?>
