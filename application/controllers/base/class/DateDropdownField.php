<?php

/**
 * Campo de fecha con dropdowns.
 *
 * @author Cristian Da Silva
 */
class DateDropdownField extends DefaultInput {

    var $required = false;
    var $ci;

    function DateDropdownField($pName, $pValue, $pLabel, $pRequired = false) {
        $this->setName($pName);
        $this->setValue($pValue);
        $this->setLabel($pLabel);
        $this->setRequired($pRequired);

        $this->ci = &get_instance();
    }

    public function generate() {
        if ($this->static) {
            $hidden_field = new HiddenField($this->name, $this->value);

            return '<p class="form-control-static">' . ($this->value != null ? to_natural_date($this->value) : '') . '</p>' . $hidden_field->generate();
        }

        $html = '<div id="' . $this->name . '-birthdaypicker"' . (!$this->visible ? ' class="hide"' : '') . '></div>';

        $html .= '<script>';
        $html .= "$('#" . $this->name . "-birthdaypicker').birthdaypicker({
                        futureDates: true,
                        dateFormat: 'littleEndian',
                        monthFormat: 'long',
                        placeholder: " . ($this->required ? 'false' : 'true') . ",
                        defaultDate: " . ($this->name == null ? 'false' : "'" . $this->value . "'") . ",
                        fieldName: '" . $this->name . "',
                        fieldId: '" . $this->name . "'
                    }); ";

        $html .= "$('.birth-day').selectpicker({
                                    noneResultsText: '" . $this->ci->lang->line('message_no_results_match') . "',
                                    noneSelectedText: '" . $this->ci->lang->line('message_nothing_selected') . "',
                                    style: 'btn-default btn-sm " . $this->class . "'
                                });";


        $html .= "$('.birth-month').selectpicker({
                                    noneResultsText: '" . $this->ci->lang->line('message_no_results_match') . "',
                                    noneSelectedText: '" . $this->ci->lang->line('message_nothing_selected') . "',
                                    style: 'btn-default btn-sm " . $this->class . "'
                                });";


        $html .= "$('.birth-year').selectpicker({
                                    noneResultsText: '" . $this->ci->lang->line('message_no_results_match') . "',
                                    noneSelectedText: '" . $this->ci->lang->line('message_nothing_selected') . "',
                                    style: 'btn-default btn-sm " . $this->class . "'
                                });";


        if (!$this->enabled)
            $html .= "$('#" . $this->name . "-birthdaypicker select').attr('disabled', 'disabled');";

        if ($this->class != '')
            $html .= "$('#" . $this->name . "-birthdaypicker select').addClass('" . $this->class . "');";

        $html .= '</script>';

        return $html;
    }

    public function printLabel($pMandatory = false) {
        return '<label class="control-label' . (!$this->visible ? ' hide' : '') . ($this->labelClass != '' ? ' ' . $this->labelClass : '') . ($pMandatory ? ' mandatory' : ' ' ) . '">' . $this->label . '</label>';
    }

    public function setRequired($pRequired) {
        $this->required = $pRequired;
    }

    public function isRequired() {
        return $this->required;
    }

}
