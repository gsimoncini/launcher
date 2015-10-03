<?php

/*
 * Campo booleano que se muestra como lista de "Si"/"No".
 */

/**
 * Description of BooleanField
 *
 * @author Mirco Bombieri
 */
class BooleanField extends DropdownField {

    //pValue puede ser verdadero o falso.
    function BooleanField($pName, $pValue, $pLabel, $pClass = 'booleanfield', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::DropdownField($pName, array(), $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);

        $CI = &get_instance();

        $this->_type = 'boolean_dropdown';

        $iTrue = new DropdownItem(1, $CI->lang->line('label_yes'));
        $iFalse = new DropdownItem(0, $CI->lang->line('label_no'));

        $this->addElement($iTrue);
        $this->addElement($iFalse);
    }

}
