<?php

/*
 * Campo para la carga de archivos.
 */

/**
 * Description of FileField
 *
 * @author Mirco Bombieri
 */
class FileField extends TextField {

    function FileField($pName, $pLabel, $pClass = 'filefield', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'file';
    }

    public function generate() {
        //TODO: Generar código de input file.
        //return '<label class="cabinet">'. .'</label>';        
        $defaults = array('name' => $this->getName(), 'id' => $this->getName());

        if ($this->isVisible()) {
            if (!$this->isEnabled()) {
                $this->addClass('disabled');
                $defaults['readonly'] = 'READONLY';
            }
        } else
            $this->addClass('invisible');
        $defaults['class'] = $this->getClass();
        return '<label class="' . $this->getClass() . '"><input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' /></label>';
    }

}

?>
