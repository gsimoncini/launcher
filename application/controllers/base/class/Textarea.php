<?php

/*
 * Campo de texto.
 */

/**
 * Description of Textarea
 *
 * @author Mirco Bombieri
 */
class Textarea extends DefaultInput {

    function Textarea($pName, $pValue, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::DefaultInput($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'textarea';
    }

    //Genera el codigo HTML para mostrar el elemento.
    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static' . ($this->getValue() == '' ? ' form-control-static-empty' : '') . '">' . $this->getValue() . '</p>' . $hidden_field->generate();
        }

        $defaults = array('name' => $this->getName(), 'id' => $this->getName());

        if (!$this->isEnabled())
            $defaults['readonly'] = 'READONLY';

        if (!$this->isVisible())
            $this->addClass('hide');

        $defaults['class'] = $this->getClass();

        return '<textarea ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' >' . $this->getValue() . '</textarea>';
    }

}
