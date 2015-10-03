<?php

/*
 * Boton modelo para el proyecto base.
 * Puede ser utilizado en formularios, cancelación o cualquier otro caso.
 */

/*
 * Description of Button
 *
 * @author Mirco Bombieri
 */

class Button {

    var $text;
    var $icon;
    var $url;
    var $enabled = true;
    var $visible = true;
    var $attributes = '';
    var $title = '';
    var $target = '_self';
    var $class = 'btn btn-sm';
    var $id = 'btn';
    var $tooltip = '';
    public static $counterInstance = 0;

    //Constructor
    function Button($pText, $pUrl = '#', $pClass = 'btn-default', $pTitle = '', $pTarget = '_self', $pAttributes = '', $pVisible = true, $pEnabled = true) {
        $this->setText($pText);
        $this->setUrl($pUrl);
        $this->setEnabled($pEnabled);
        $this->setVisible($pVisible);
        $this->setAttributes($pAttributes);
        $this->setTitle($pTitle);
        $this->setTarget($pTarget);
        $this->addClass($pClass);

        //Genera id dinamico
        self::$counterInstance++;
        $id = 'btn_' . self::$counterInstance;
        $this->setId($id);
    }

    //Funcion que genera el html correspondiente al boton
    public function generate() {
        if (!$this->isVisible())
            $this->addClass('hide');

        $defaults = array('class' => $this->getClass());

        if ($this->getTitle() != '')
            $defaults['title'] = $this->getTitle();

        if (!$this->isEnabled())
            $defaults['disabled'] = 'DISABLED';

        if ($this->getUrl() == '#') {
            $defaults['type'] = 'button';

            $component = 'button';
        } else {
            $defaults['href'] = $this->getUrl();
            $defaults['target'] = $this->getTarget();

            $component = 'a';
        }

        $html = '<' . $component . ' id="' . $this->id . '"' . ' ' . $this->_parse_form_attributes($this->attributes, $defaults) . '>';

        if ($this->icon != '')
            $html .= '<i class="fa ' . $this->icon . ($this->text != '' ? ' btn-icon-margin' : '') . '"></i>';

        $html .= $this->text;

        if ($this->tooltip != '')
            $html .= '<script>
                          $(document).ready(function(){
                              $("#' . $this->id . '").tooltip({title:"' . ($this->text != '' ? $this->text : $this->tooltip) . '", placement: "bottom", container: "body"});
                          });
                      </script>';

        $html .= '</' . $component . '>';

        return $html;
    }

    //Funcion privada para parsear los atributos extra
    function _parse_form_attributes($attributes, $default) {
        if (is_array($attributes)) {
            foreach ($default as $key => $val) {
                if (isset($attributes[$key])) {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }

            if (count($attributes) > 0) {
                $default = array_merge($default, $attributes);
            }
            $att = '';
        } else
            $att = ' ' . $attributes;

        foreach ($default as $key => $val) {
            if ($key == 'value') {
                $val = htmlentities($val);
            }
            $att .= $key . '="' . $val . '" ';
        }
        return $att;
    }

    //Agrega una clase
    public function addClass($pClass) {
        if ($pClass != '')
            $this->class .= ' ' . $pClass;
    }

    /*
     * Métodos Accesores
     */

    public function isEnabled() {
        return $this->enabled;
    }

    public function setEnabled($state = true) {
        $this->enabled = $state;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function setVisible($state = true) {
        $this->visible = $state;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($pText) {
        $this->text = $pText;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($pIcon) {
        $this->icon = $pIcon;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($pUrl) {
        $this->url = $pUrl;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function setAttributes($pAttributes) {
        $this->attributes = $pAttributes;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($pTitle) {
        $this->title = $pTitle;
    }

    public function getTarget() {
        return $this->target;
    }

    public function setTarget($pTarget) {
        $this->target = $pTarget;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass($pClass) {
        $this->class = $pClass;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    //public function setTooltip($pText = '') {
    //}
    public function getTooltip() {
        return $this->tooltip;
    }

    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;
    }

}
