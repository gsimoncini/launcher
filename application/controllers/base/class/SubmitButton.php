<?php

/*
 * Boton de SUBMIT modelo para el proyecto base.
 * Puede ser utilizado en formularios.
 */

/*
 * Description of Button
 *
 * @author Mirco Bombieri
 */

class SubmitButton extends Button {

    var $confirm;

    //Constructor
    function SubmitButton($pText, $pUrl, $pClass = 'btn-primary', $pTitle = '', $pTarget = '_self', $pAttributes = '', $pVisible = true, $pEnabled = true) {
        $this->setText($pText);
        $this->setUrl($pUrl);
        $this->setEnabled($pEnabled);
        $this->setVisible($pVisible);
        $this->setAttributes($pAttributes);
        $this->setTitle($pTitle);
        $this->setTarget($pTarget);
        $this->setConfirm(true);
        $this->addClass($pClass);
    }

    //Funcion que genera el html correspondiente al boton
    public function generate() {
        if (!$this->isVisible())
            $this->addClass('hide');

        $defaults = array('class' => $this->getClass(), 'type' => 'submit');

        if ($this->isConfirm())
            $defaults['onclick'] = 'baseController.confirmFormSubmit(event);';

        if (!$this->isEnabled())
            $defaults['disabled'] = 'DISABLED';

        return '<button ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>' . ($this->icon != '' ? '<i class="fa ' . $this->icon . ' btn-icon-margin"></i>' : '') . $this->text . '</button>';
    }

    public function setConfirm($pConfirm) {
        $this->confirm = $pConfirm;
    }

    public function isConfirm() {
        return $this->confirm;
    }

}
