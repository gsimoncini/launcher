<?php

/*
 * Campo tipo ComboBox.
 */

/**
 * Description of DropdownField
 *
 * @author Mirco Bombieri
 */
class DropdownField extends TextField {

    var $values = array();
    var $selected;
    var $sort = true;
    var $multiselect = false;
    var $search = true;
    var $ci;

    function DropdownField($pName, $pValues, $pSelected, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'dropdown';
        $this->setSelected($pSelected);
        $this->setValues($pValues);

        $this->ci = &get_instance();
    }

    public function generate() {
        if ($this->isStatic()) {
            $selected = null;

            foreach ($this->getValues() as $element)
                if ($this->getSelected() == $element->id) {
                    $selected = $element;
                    break;
                }

            if ($selected == null) {
                $value = (is_array($this->values) && $this->values != null) ? $this->values[0]->value : '';
                $id = (is_array($this->values) && $this->values != null) ? $this->values[0]->id : '';
            } else {
                $value = $selected->value;
                $id = $selected->id;
            }

            $hidden_field = new HiddenField($this->getName(), $id);

            return '<p class="form-control-static' . ($value == '' ? ' form-control-static-empty' : '') . '">' . $value . '</p>' . $hidden_field->generate();
        }

        $defaults = array('name' => $this->getName() . ($this->isMultiSelect() ? '[]' : ''), 'id' => $this->getName());

        if ($this->isMultiSelect()) {
            $this->addClass('multi-select');
            $defaults['multiple'] = 'MULTIPLE';
        }

        if (!$this->isVisible()) {
            $this->setLabelClass('hide');
            $this->addClass('hide');
        }

        $defaults['class'] = $this->getClass();

        if (count($this->getValues()) == 0)
            $defaults['disabled'] = 'DISABLED';

        $result = '<select ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' ' . ($this->getSearch() && $this->isMultiSelect() == false ? 'data-live-search="true"' : '') . ' title="">';

        //Cargo los elementos en el select
        foreach ($this->getValues() as $element) {
            if (is_array($this->getSelected()))
                $selected = array_search($element->id, $this->getSelected()) !== false;
            else
                $selected = ($this->getSelected() === $element->id);

            $result .= '<option value="' . $element->id . '" ' . ($selected ? 'SELECTED' : '') . ' >' . $element->value . '</option>';
        }

        $result .= '</select>';

        if ($this->isMultiSelect() == false)
            $result .= "<script>
                            $(document).ready(function() {
                                $('#" . $this->getName() . "').selectpicker({
                                    noneResultsText: 'Ningún resultado coincide con',
                                    noneSelectedText: 'Nada seleccionado',
                                    style: 'btn-default btn-sm " . $this->class . "'
                                });
                            });
                        </script>";

        if (!$this->isEnabled())
            $result .= "<script>
                            $(document).ready(function() {
                                $('button[data-id=" . $this->getName() . "]').attr('disabled', 'disabled');
                            });
                        </script>";

        return $result;
    }

//   Función de comparacíon utilizada en el getValues para poder ordenar array valores (multidimensional)
    function cmp($x, $y) {
        if (strtoupper($x->value) == strtoupper($y->value))
            return 0;
        else if (strtoupper($x->value) < strtoupper($y->value))
            return -1;
        else
            return 1;
    }

    /*
     * Métodos accesores
     */

    public function getValues() {
        //Ordeno y devuelvo el vector de valores
        if ($this->sort)
            uasort($this->values, array($this, "cmp"));
        return $this->values;
    }

    public function getSelected() {
        return $this->selected;
    }

    public function setValues($pValues) {
        $this->values = $pValues;
    }

    public function setSelected($pSelected) {
        $this->selected = $pSelected;
    }

    //Permite agregar un elemento
    public function addElement($pElement) {
        $this->values[] = $pElement;
    }

    public function getSort() {
        return $this->sort;
    }

    public function setSort($sort) {
        $this->sort = $sort;
    }

    function setMultiSelect($pValue) {
        $this->multiselect = $pValue;
    }

    public function isMultiSelect() {
        return $this->multiselect;
    }

    function getSearch() {
        return $this->search;
    }

    function setSearch($search) {
        $this->search = $search;
    }

}
