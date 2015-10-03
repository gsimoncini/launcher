<?php

/*
 * Se trata de un boton que tiene asociada una accion en javascript. Se le pasa
 * el codigo JS a ejecutar, y este se configura que cuando se presione click o la tecla enter sobre él
 * ejecute el script indicado.
 */

/**
 * Description of JSButton
 *
 * @author Mirco Bombieri
 */
class JSButton extends Button {

    //Constructor
    function JSButton($pText, $pAction, $pClass = 'default_button', $pTitle = '', $pAttributes = '', $pVisible = true, $pEnabled = true) {
        $this->setText($pText);
        $this->setUrl($pAction);
        $this->setEnabled($pEnabled);
        $this->setVisible($pVisible);
        $this->setAttributes($pAttributes);
        $this->setTitle($pTitle);
        $this->setClass($pClass);
    }

    //Funcion que genera el html correspondiente al boton
    public function generate() {
        $this->addClass('pointer');
        if ($this->isVisible()) {
            if (!$this->isEnabled()) {
                $this->class .= ' disabled';
            }
        } else
            $this->class .= ' invisible';


        $this->addClass('center');
        $onClick = $this->getUrl();
        $onKeyUp = " if(event.keycode==13) " . $this->getUrl();

        $defaults = array('class' => $this->getClass(), 'onclick' => $onClick, 'onkeyup' => $onKeyUp, 'title' => $this->getTitle());
        return '<a ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>' . $this->text . '</a>';
    }

}

?>
