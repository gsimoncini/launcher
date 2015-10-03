<?php

/**
 * Botón que abre una ventana popup.
 *
 * @author Cristian Da Silva
 */
class PopupButton extends Button {

    var $callback = '';

    function PopupButton($pText, $pUrl, $pCallback, $pClass = 'btn-default', $pAttributes = '') {
        $this->setText($pText);
        $this->setUrl($pUrl);
        $this->setCallback($pCallback);
        $this->setAttributes($pAttributes);
        $this->addClass($pClass);
    }

    public function generate() {
        if (!$this->isVisible())
            $this->addClass('hide');

        //$onclick = $this->getCallback() . '; baseController.popup(event, this);';
        $onclick = $this->getCallback();

        $defaults = array(
            'class' => $this->getClass(),
            'href' => $this->getUrl(),
            'onclick' => $onclick,
            'data-toggle' => 'modal',
            'data-target' => '#popup'
        );

        if (!$this->isEnabled())
            $defaults['disabled'] = 'DISABLED';

        $result = '<a ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>' . ($this->icon != '' ? '<i class="fa ' . $this->icon . ($this->text != '' ? ' btn-icon-margin' : '') . '"></i>' : '') . $this->text . '</a>';

        return $result;
    }

    public function getCallback() {
        return $this->callback;
    }

    public function setCallback($pCallback) {
        $this->callback = $pCallback;
    }

}
