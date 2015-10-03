<?php

/*
 *  Campo de numeros ordinario.
 */

/**
 * Description of NumericField
 *
 * @author Mirco Bombieri
 */
class NumericField extends TextField {

    var $decimalSeparator = '.';
    var $thousandsSeparator = ',';

    function NumericField($pName, $pValue, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {

        parent::TextField($pName, $pValue, $pLabel, $pClass . ' text-right', $pAttributes, $pEnabled, $pVisible);
    }

    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static">' . $this->getValue() . '</p>' . $hidden_field->generate();
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

        return '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' />'
                . '<script>'
                . '$("input[name=' . $this->name . ']").inputmask("decimal",{
                            radixPoint:"' . $this->decimalSeparator . '",
                            groupSeparator: "' . $this->thousandsSeparator . '",
                            digits: 4,
                            autoGroup: true
                }); '
                . '$("form").submit(function(){'
                . ' var inputField = $("input[name=' . $this->name . ']"); '
                . ' if(typeof(inputField) != \'undefined\') { '
                . '  var value = inputField.inputmask(\'unmaskedvalue\');'
                . ' if(typeof(value) != \'undefined\') {'
                . '  inputField.inputmask("remove");'
                . '  if("' . $this->decimalSeparator . '" != ".")'
                . '      value = value.replace("' . $this->decimalSeparator . '",".");'
                . '   '
                . '  inputField.val(value);'
                . '  } '
                . '  } '
                . '});'
                . '</script>';
    }

    public function getDecimalSeparator() {
        return $this->decimalSeparator;
    }

    public function getThousandsSeparator() {
        return $this->thousandsSeparator;
    }

    public function setDecimalSeparator($decimalSeparator) {
        $this->decimalSeparator = $decimalSeparator;
    }

    public function setThousandsSeparator($thousands_separator) {
        $this->thousandsSeparator = $thousands_separator;
    }

}
