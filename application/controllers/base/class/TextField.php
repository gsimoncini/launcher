<?php

/*
 *  Campo de texto ordinario. 
 */

/**
 * Description of TextField
 *
 * @author Mirco Bombieri
 */
class TextField extends DefaultInput {

    //Define si el label se muestra dentro del input o no.
    var $inlineLabel = FALSE;

    function TextField($pName, $pValue, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::DefaultInput($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'text';
    }

    //Genera el codigo HTML para mostrar el elemento.
    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static' . ($this->getValue() == '' ? ' form-control-static-empty' : '') . '">' . $this->getValue() . '</p>' . $hidden_field->generate();
        }

        //Atributos
        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getPublicId());

        //Pregunto si tiene el label dentro del campo o no.
        if ($this->inlineLabel && $this->getValue() == null)
            $defaults['placeholder'] = $this->label;

        if (!$this->isVisible())
            $this->addClass('hide');

        if (!$this->isEnabled())
            $defaults['readonly'] = 'READONLY';

        $defaults['class'] = $this->getClass();

        return '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' />';
    }

    //Metodos accesosres
    public function setInlineLabel($pInline) {
        $this->inlineLabel = $pInline;
    }

    public function getInlineLabel() {
        return $this->inlineLabel;
    }

}
