<?php

/*
 * Campo de de ingreso de texto con autocompletar sobre una lista de textos definda.
 */

/**
 *
 * @author Mirco Bombieri
 */
class AutocompleteField extends TextField {

    //$AutocompleteElements:array de dropdownItem
    var $AutocompleteElements = array();

    function AutocompleteField($pName, $pValue, $pLabel, $pAutocompleteElements) {
        parent::TextField($pName, $pValue, $pLabel);

        if (isset($pAutocompleteElements))
            $this->setAutocompleteElements($pAutocompleteElements);
    }

    public function generate() {
        if ($this->isStatic()) {
            $hidden_field = new HiddenField($this->getName(), $this->getValue());

            return '<p class="form-control-static' . ($this->getValue() == '' ? ' form-control-static-empty' : '') . '">' . $this->getValue() . '</p>' . $hidden_field->generate();
        }

        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());

        if ($this->isVisible()) {
            if (!$this->isEnabled()) {
                $this->addClass('disabled');
                $defaults['readonly'] = 'READONLY';
            }
        } else
            $this->addClass('invisible');

        $defaults['class'] = $this->getClass();

        $AutocompleteElements = $this->getAutocompleteElements();

        $array_js = '[';
        if (isset($AutocompleteElements) && is_array($AutocompleteElements))
            foreach ($AutocompleteElements as $element) {
                $array_js .= '{id:"' . $element->id . '", name:"' . $element->value . '"},';
            }
        $array_js .= ']';

        $result = '';
        $result.= '<div class="content-typeahead"><input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' data-provide="typeahead" />';

        $result .= '
                    <script>
                         $("#' . $this->getName() . '").typeahead({
                            source: ' . $array_js . '
                             });
                    </script></div>';

        return $result;
    }

    function getAutocompleteElements() {
        return $this->AutocompleteElements;
    }

    function setAutocompleteElements($AutocompleteElements) {
        $this->AutocompleteElements = $AutocompleteElements;
    }

}

?>