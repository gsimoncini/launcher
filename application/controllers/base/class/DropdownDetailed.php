<?php

/*
 * Campo tipo ComboBox.
 */

/**
 * Description of DropdownField
 *
 * @author Mauricio Besson
 */
//versión beta

class DropdownDetailed extends TextField {

    var $values = array();
    var $selected;
    var $enabled = true;
    var $icon = '';

    //$pValues tiene que ser un array de elementos tipo DropDownItemsDetailed
    //$pselected: espera id del elemento
    function DropdownDetailed($pName, $pValues, $pSelected, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'dropdown';
        $this->setValues($pValues);
        $this->setSelected($pSelected);
    }

    public function generate() {
        if ($this->isStatic()) {
            $selected = null;

            foreach ($this->values as $element)
                if ($this->selected == $element->id) {
                    $selected = $element;
                    break;
                }

            if ($selected == null)
                $value = (is_array($this->values) && $this->values != null) ? $this->values[0]->value : '';
            else
                $value = $selected->value;

            if ($value != null)
                $text = $value['value_1'] . ($value['value_2'] !== '' ? ' - ' . $value['value_2'] : '') . ($value['value_3'] !== '' ? ' - ' . $value['value_3'] : '');
            else
                $text = '';

            $hidden_field = new HiddenField($this->getName(), $value != null ? $value['id'] : null);

            return '<p class="form-control-static' . ($text == '' ? ' form-control-static-empty' : '') . '">' . $text . '</p>' . $hidden_field->generate();
        }

        //trata elementos del dropdown
        $array_elements = array();

        foreach ($this->values as $element) {
            array_push($array_elements, $element->getValues());
        }

        $SelectedPosition = 0;
        if ($this->selected !== -1) {
            for ($index = 0; $index < count($array_elements); $index++) {
                foreach ($array_elements[$index] as $element) {
                    if ($element == $this->selected) {
                        $SelectedPosition = $index;
                    }
                }
            }
        }

        $json_elements = json_encode($array_elements);

        if ($this->selected == null)
            $this->selected = -1;

        $defaults = array('name' => $this->getName(), 'id' => $this->getName());

        if (!$this->isVisible()) {
            $this->setLabelClass('hide');
            $this->addClass('hide');
        }

        $defaults['class'] = $this->getClass();

        if (!$this->isEnabled())
            $defaults['readonly'] = 'READONLY';

        if (count($this->getValues()) == 0)
            $defaults['disabled'] = 'DISABLED';

        $result = '<select ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>';


        $result .= '</select>';

        $result .= "<script>
                        $(document).ready(function() {
                                var data = " . $json_elements . ";
                                var enabled = " . ($this->getEnabled() ? 'true' : 'false') . ";
                                var selectDropDown = $('#" . $this->getName() . "').selectize({
                                create: false,
                                valueField: 'id',
                                labelField: 'value_1',
                                sortField: 'value_1',
                                searchField: ['value_1', 'value_2', 'value_3', 'value_4', 'value_5'],
                                options: data,
                                render: {
                                    item: function(item) {
                                        var html = '';
                                        html += '<div style=\"margin-left: 4px;\">' + item.value_1 + (item.value_2 != '' ? ' - ': '')  + item.value_2 + (item.value_3 != '' ? ' - ': '')  + item.value_3 + (item.value_3 != '' ? ' ... ': '')+'</div>';
                                        return html;
                                    },
                                    option: function(item) {
                                        var html = '';
                                        
                                        html += '<div class=\"detailed-dropdown-item\">';
                                        html += '<span class=\"title\"><span class=\"name\"><i class=\"fa " . $this->getIcon() . "\"></i>'+ (typeof(item.value_1)=='undefined' ? '' : item.value_1) +'</span>';
                                        html += '<span class=\"subtitle\">'+(typeof(item.value_2)=='undefined' ? '' : item.value_2 ) +'</span></span>';
                                        
                                        html += '<span class=\"description\">'+(typeof(item.value_3)=='undefined' ? '' : item.value_3 )+'</span>';
                                        
                                        if(item.value_4 != '' || item.value_5 != '') { 
                                            html += '<ul class=\"meta\">';
                                            html += '<li class=\"meta-1\">'+ (typeof(item.value_4)=='undefined' ? '' : item.value_4) +'</li>';
                                            html += '<li class=\"meta-2\">'+(typeof(item.value_5)=='undefined' ? '' : item.value_5) +'</li>';
                                            html += '</ul>';
                                        }

                                        html += '</div>';

                                        return html;
                                    }
                                }
                            });

                            var select = selectDropDown[0].selectize;


                            if(" . $this->selected . " != -1){
                                if (data.length > 0) {
                                    select.enable();
                                    select.setValue(data[" . $SelectedPosition . "].id);
                                } else
                                select.disable();
                            }else{
                            if (data.length > 0) {
                                select.enable();
                                select.setValue(data[0].id);
                            } else
                                select.disable();
                            }

                            if(enabled == false)
                                select.disable();

                            $('#".$defaults['id']."').keyup(function(e) {
                                if (e.keyCode == 8) {
                                    select.clear();
                                }
                            });
                       });
                    </script>";

        return $result;
    }

    /*
     * Métodos accesores
     */

    public function getValues() {
        return $this->values;
    }

    public function setValues($pValues) {
        $this->values = $pValues;
    }

    public function getSelected() {
        return $this->selected;
    }

    public function setSelected($selected) {
        $this->selected = $selected;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    function getIcon() {
        return $this->icon;
    }

    function setIcon($icon) {
        $this->icon = $icon;
    }

}
