<?php

/**
 * Genera un radio.
 *
 * @author Mirco Bombieri
 */
class RadioField extends TextField {

    var $color = 'grey';
    var $value_radio = false;
    var $state_radio = false;

    function RadioField($pName, $pLabel, $pValue, $pDefault = false, $pClass = 'radiofield', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);

        $this->_type = 'radio';

        $this->value_radio = $pValue;
        $this->state_radio = $pDefault;
    }

    public function generate() {
        $defaults = array('name' => $this->getName(), 'id' => $this->getPublicId());
        $check = '';

        if (!$this->isVisible())
            $this->addClass('hide');

        if (!$this->isEnabled())
            $defaults['disabled'] = 'disabled';

        if ($this->state_radio)
            $check = 'checked';

        $radio_html = '<div class="radio"><label><input  type="' . $this->_type . '" value="' . $this->value_radio . '" ' . $check . '  ' . $this->_parse_form_attributes($this->attributes, $defaults) . '/>' . $this->label . '</label></div>';

        $radio_html .= '<script>
                            $("input[name=' . $this->getName() . ']").iCheck({
                                radioClass: "iradio_flat-' . $this->getColor() . '",
                                inheritID: true
                            });
                            $("input[name=' . $this->getName() . ']").on("ifChecked", function(event) {
                                $(this).click();
                            });
                            $("input[name=' . $this->getName() . ']").on("ifChanged", function(event) {
                                $(this).click();
                            });
                        </script>';

        return $radio_html;
    }

    public function isChecked() {
        return $this->state_radio;
    }

    public function setChecked($pChecked) {
        $this->state_radio = $pChecked;
    }

    public function getLabel() {
        return '';
    }

    public function getColor() {
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

}
