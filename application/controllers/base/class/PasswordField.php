<?php

/*
 * Campo de ingreso de claves.
 */

/**
 * Description of PasswordField
 *
 * @author Mirco Bombieri
 */
class PasswordField extends TextField {

    function PasswordField($pName, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'password';
    }

}

?>
