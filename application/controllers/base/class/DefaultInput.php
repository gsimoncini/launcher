<?php

/*
 * Clase Abstracta para definir la estructura de los campos de entrada de datos
 * para formularios.
 */

/**
 * Description of DefaultInput
 *
 * @author Mirco Bombieri
 */
class DefaultInput {
    /* Variables Privadas */

    var $_type = null; //Debe definirse en cada hijo.

    /* Variables Publicas */
    var $name;
    var $id;
    var $value;
    var $label;
    var $labelClass;
    var $class = 'form-control input-sm';
    var $attributes = '';
    var $enabled = true;
    var $visible = true;
    var $static = false;

    //Constructor
    function DefaultInput($pName, $pValue, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        $this->setName($pName);
        $this->setValue(str_replace('\\', '', $pValue));
        $this->setLabel($pLabel);
        $this->addClass($pClass);
        $this->setAttributes($pAttributes);
        $this->setEnabled($pEnabled);
        $this->setVisible($pVisible);
    }

    public function generate() {
        //TODO: lanzar error para que se implemente el generate de la clase hija.
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
                $val = $val;
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

    //Imprime el label del objeto
    public function printLabel($pMandatory = false) {
        $extra_class = '';

        if ($this->getLabelClass() != '')
            $extra_class .= ' ' . $this->getLabelClass();

        if (!$this->isVisible())
            $extra_class .= ' hide';

        if ($pMandatory)
            $extra_class .= ' mandatory';

        return '<label class="control-label' . $extra_class . '" for="' . $this->getPublicId() . '">' . $this->getLabel() . '</label>';
    }

    /*
     * Métodos Accesores
     */

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getLabelClass() {
        return $this->labelClass;
    }

    public function getClass() {
        return $this->class;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function isStatic() {
        return $this->static;
    }

    public function setName($pName) {
        $this->name = $pName;
    }

    public function setValue($pValue) {
        $this->value = $pValue;
    }

    public function setLabel($pLabel) {
        $this->label = $pLabel;
    }

    public function setLabelClass($pLabelClass) {
        $this->labelClass = $pLabelClass;
    }

    public function setClass($pClass) {
        $this->class = $pClass;
    }

    public function setAttributes($pAttributes) {
        $this->attributes = $pAttributes;
    }

    public function setEnabled($pEnabled) {
        $this->enabled = $pEnabled;
    }

    public function setVisible($pVisible) {
        $this->visible = $pVisible;
    }

    public function setStatic($pStatic) {
        $this->static = $pStatic;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function getPublicId() {
        if ($this->getId() != null)
            return $this->getId();
        else
            return $this->getName();
    }

}
