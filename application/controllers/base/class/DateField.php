<?php

/*
 * Campo de fecha (DatePicker)
 */

/**
 * Description of DateField
 *
 * @author Mirco Bombieri
 */
class DateField extends TextField {

    var $mask = 'dd/mm/yyyy';
    var $separator = '/';
    var $allow_manual_input = false;

    function DateField($pName, $pValue, $pLabel, $pClass = 'date-input', $pAttributes = '', $pEnabled = true, $pVisible = true, $pMask = 'dd/mm/yyyy') {
        parent::TextField($pName, $pValue, $pLabel, null, $pAttributes, $pEnabled, $pVisible);

        $this->_type = 'text';

        $this->addClass($pClass);
        $this->addClass('text-center');

        $this->setMask($pMask);
    }

    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static">' . $this->getValue() . '</p>' . $hidden_field->generate();
        }

        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());

        //Es solo lectura siempre, ya que para ingresar una fecha debe utilizarse el datepicker
        if (!$this->allow_manual_input)
            $defaults['readonly'] = 'READONLY';

        if (!$this->isVisible())
            $this->addClass('hide');

        if ($this->isEnabled())
            $this->addClass('date-enabled');

        $defaults['class'] = $this->getClass();

        //Define el input y el boton lateral para que abra el calendario
        $result = '';
        if ($this->isVisible()) {
            $result .= '<div class="input-group input-group-sm input-group-date">';
            $result .= '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>';
            $result .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
            $result .= '</div>';

            if ($this->isEnabled()) {
                $result .= '<script>';
                $result .= '$("#' . $this->getName() . '").datepicker({format: "' . $this->getMask() . '"});';
                $result .= '$("#' . $this->getName() . '").on("changeDate", function() {
                            $(this).datepicker("hide");
                        });';
                $result .='</script>';
            }
        }
        return $result;
    }

    /*
     * Métodos Accesores
     */

    function getValue() {
        if (!valid_date($this->value, $this->separator))
            return '';
        else
            return $this->value;
    }

    public function setMask($pMask) {
        $this->mask = $pMask;
    }

    public function getMask() {
        return $this->mask;
    }

    public function setManual($pAllow) {
        $this->allow_manual_input = $pAllow;
    }

    public function setFormat($pMask, $pSeparator) {
        $this->mask = $pMask;
        $this->separator = $pSeparator;
    }

}
