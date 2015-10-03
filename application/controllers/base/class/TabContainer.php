<?php

/**
 * Description of TabContainer
 *
 * @author Cristian Da Silva
 */
class TabContainer {

    var $elements = array();
    var $name;

    function TabContainer($pElements, $pName = 'tab') {
        $this->setElements($pElements);
        $this->setName($pName);
    }

    public function generate() {
        $input = new HiddenField($this->name . '_active', null);
        $result = '';

        $result .= $input->generate();
        $result .= '<script>';
        $result .= "$(document).ready(function() {
                        $('a[data-toggle=tab]').on('shown.bs.tab', function(event) {
                            $('#" . $this->name . "_active').val($(event.target).attr('href').substring(1));
                        });
                        $('#" . $this->name . "_active').val($('ul[data-name=tab]').find('li.active a').attr('href').substring(1));
                        $('a[data-toggle=tab]').click(function(event) {
                            if($(this).parent('li').hasClass('disabled'))
                                return false;
                        });
                    });";
        $result .= '</script>';
        $result .= '<ul class="nav nav-tabs nav-tabs-panel" role="tablist" data-name="' . $this->name . '">';

        foreach ($this->elements as $tab)
            $result .='<li' . ($tab->active ? ' class="active"' : '') . '><a href="#' . $tab->id . '" role="tab" data-toggle="tab">' . $tab->name . '</a></li>';

        $result .= '</ul>';

        return $result;
    }

    public function addElement($pElement) {
        $this->elements[] = $pElement;
    }

    /*
     * Métodos accesores
     */

    public function getElements() {
        return $this->elements;
    }

    public function setElements($pElements) {
        $this->elements = $pElements;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($Name) {
        $this->name = $Name;
    }

}
