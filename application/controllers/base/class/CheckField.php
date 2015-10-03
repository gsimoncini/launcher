<?php

/**
 * Campo boolean. Genera un checkbox.
 *
 * @author Mirco Bombieri
 */
class CheckField extends TextField {

    var $color = 'grey';
    var $checked = false;

    function CheckField($pName, $pValue, $pLabel, $pChecked = false, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);

        $this->_type = 'checkbox';

        $this->checked = $pChecked;
        $this->class = $pClass;
    }

    //Genera HTML de un checkbox
    public function generate($withLabel = false) {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());
            $static_html = '';

            if ($withLabel)
                $static_html = $this->printLabel();

            $static_html .= '<p class="form-control-static">' . ($this->getValue() == 1 ? 'Sí' : 'No') . '</p>' . $hidden_field->generate();

            return $static_html;
        }

        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());

        if (!$this->isVisible())
            $this->addClass('hide');

        if (!$this->isEnabled())
            $defaults['disabled'] = 'disabled';

        if ($this->getClass() != '')
            $defaults['class'] = $this->getClass();

        $checkbox_html = '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' ' . ($this->checked ? 'checked' : '') . '/>';

        if ($withLabel)
            $checkbox_html = '<div class="checkbox"><label>' . $checkbox_html . $this->label . '</label></div>';

        $checkbox_html .= '<script>
                               $("#' . $this->getName() . '").iCheck({
                                   checkboxClass: "icheckbox_square-' . $this->getColor() . '",
                                   checkedClass: "checked"
                               });
                           </script>';

        return $checkbox_html;
    }

    public function isChecked() {
        return $this->checked;
    }

    public function setChecked($pChecked) {
        $this->checked = $pChecked;
    }

    public function getColor() {
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

}
