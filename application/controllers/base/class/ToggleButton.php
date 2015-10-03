<?php

//fuente:http://www.bootstraptoggle.com/
class ToggleButton extends TextField {

    var $name = '';
    var $label = '';
    var $checked = FALSE;
    var $textOn = '';
    var $textOff = '';

    function ToggleButton($pName, $pLabel = '', $pValue, $pChecked = false, $pClass = '', $pTextOn = '', $pTextOff = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled);
        $this->setName($pName);
        $this->setLabel($pLabel);
        $this->setChecked($pChecked);
        $this->setTextOn($pTextOn);
        $this->setTextOff($pTextOff);
    }

    public function generate() {
        $html = '';
        $html.='<label class="checkbox-inline ' . (!$this->isVisible() ? 'hide' : '') . ' ">';
        $html.='<input id="' . $this->getName() . '"  type="checkbox" class="' . $this->getClass() . '" ' . ($this->getChecked() ? 'checked' : '') . ' ' . (!$this->isEnabled() ? 'disabled="disabled"' : '' ) . 'data-toggle="toggle" data-size="small" data-onstyle="success"> ' . $this->getLabel();
        $html.='</label>';

        $html.='<script>
                 $(\'#' . $this->getName() . '\').bootstrapToggle({
                     on:"' . $this->getTextOn() . '",
                     off:"' . $this->getTextOff() . '"
                    });
                </script>';

        return $html;
    }

    function getName() {
        return

                $this->name;
    }

    function getLabel() {
        return

                $this->label;
    }

    function getChecked() {
        return

                $this->checked;
    }

    function setName(
    $name) {
        $this->name = $name

        ;
    }

    function setLabel($label) {
        $this->label = $label

        ;
    }

    function setChecked($checked) {
        $this->checked = $checked

        ;
    }

    function getTextOn() {
        return $this->textOn;
    }

    function getTextOff() {
        return

                $this->textOff;
    }

    function setTextOn(
    $textOn) {
        $this->textOn = $textOn

        ;
    }

    function setTextOff($textOff) {
        $this->textOff = $textOff;
    }

}

?>
