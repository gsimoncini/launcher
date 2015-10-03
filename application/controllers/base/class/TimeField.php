<?php

/**
 * Campo de ingreso de hora.
 *
 * @author Mirco Bombieri
 */
class TimeField extends TextField {

    var $show24 = true;

    function TimeField($pName, $pValue, $pLabel, $pShow24 = true, $pClass = 'text-center', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);

        $this->_type = 'text';

        $this->setShow24($pShow24);
    }

    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static">' . $this->getValue() . '</p>' . $hidden_field->generate();
        }

        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());

        if (!$this->isEnabled())
            $defaults['disabled'] = 'DISABLED';

        $defaults['class'] = $this->getClass();

        $result = '<div class="input-group input-group-sm input-group-time' . ($this->isVisible() ? '' : ' hide') . '" id="datetimepicker-' . $this->getName() . '">
                       <input type="' . $this->_type . '"' . $this->_parse_form_attributes($this->attributes, $defaults) . ' class="form-control"/>
                       <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                   </div>';

        $result .= '<script>$("#' . $this->getName() . '").datetimepicker({' . ($this->show24() ? 'format: "HH:mm", ' : '') . 'pickDate: false, pick12HourFormat: true});</script>';

        return $result;
    }

    /*
     * Métodos Accesores
     */

    public function setShow24($pShow24) {
        $this->show24 = $pShow24;
    }

    public function show24() {
        return $this->show24;
    }

}
