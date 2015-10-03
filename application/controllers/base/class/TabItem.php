<?php

/**
 * Description of TabItem
 *
 * @author Cristian Da Silva
 */
class TabItem {

    var $id;
    var $name;
    var $active;

    function TabItem($pId, $pName, $pTabActive = '') {
        $this->setId($pId);
        $this->setName($pName);
        $this->setActive($pTabActive == $pId ? true : false);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getActive() {
        return $this->active;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function setName($pName) {
        $this->name = $pName;
    }

    public function setActive($pActive) {
        $this->active = $pActive;
    }

}
