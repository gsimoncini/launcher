<?php

/*
 * Crea un item para poder crear un SortTableDropdown.
 */

class SortTableDropdownItem {

    var $id;
    var $name;

    function SortTableDropdownItem($pId, $pName) {
        $this->setId($pId);
        $this->setName($pName);
    }

    function setId($pId) {
        $this->id = $pId;
    }

    function setName($pName) {
        $this->name = $pName;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

}
