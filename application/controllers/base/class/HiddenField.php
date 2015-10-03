<?php

/*
 * Campo oculto.
 */

/**
 * Description of HiddenField
 *
 * @author Mirco Bombieri
 */
class HiddenField extends TextField {

    function HiddenField($pName, $pValue, $pAttributes = '') {
        parent::TextField($pName, $pValue, '', '', $pAttributes, true, true);
        $this->_type = 'hidden';
    }

    //Genera HTML de un campo oculto
    public function generate() {
        //NO implementa clases de CSS dado que siempre está oculto
        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());
        return '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . '  />';
    }

}

?>
