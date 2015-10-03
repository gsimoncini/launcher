<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultTable
 *
 * @author Mirco Bombieri
 */
class DefaultTable {

    var $items = array();
    var $border = 0;
    var $cellpadding = 0;
    var $cellspacing = 0;
    var $class = 'table';
    var $visible = true;
    var $attributes = '';
    var $heading = array();
    var $id;
    //Variable privada
    var $CI_controller;

    function DefaultTable($pId, $pHeading, $pItems = array(), $pClass = 'table', $pAttributes = '') {
        $this->id = $pId;
        $this->heading = $pHeading;
        $this->items = $pItems;
        $this->class = $pClass;
        $this->attributes = $pAttributes;
        //Levanto la libreria de Codeigniter para tablas HTML.
        $this->CI_controller = & get_instance();
        $this->CI_controller->load->library('table');
        $this->CI_controller->table->clear();
    }

    //Devuelve el codigo HTML de una tabla
    public function generate() {

        $defaults = array('border' => $this->getBorder(), 'cellpadding' => $this->getCellpadding(), 'cellspacing' => $this->getCellspacing(), 'class' => $this->getClass(), 'id' => $this->getId());
        //Defino el template
        $tmpl = array(
            'table_open' => '<table ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' >',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr class="alt">',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );
        $this->CI_controller->table->set_template($tmpl);

        //Coloco el encabezado
        $this->CI_controller->table->set_heading($this->getHeading());

        //Agrego cada item
        $count = 0;
        foreach ($this->items as $item) {
            //Recorremos las celdas, para determinar si llevan estilos particulares o no.
            $newItem = array();
            foreach ($item AS $cell) {
                //Si la celda es un objeto, lo convierto en array, sino coloco el texto
                if (is_object($cell))
                    $newCell = $cell->asArray();
                else
                    $newCell = $cell;
                $newItem[] = $newCell;
            }
            $this->CI_controller->table->add_row($newItem);
            $count++;
        }
        //Si no hay elementos lo notifico.
        if ($count == 0) {
            $cell = new DefaultTableCell('No hay informaci&oacute;n.', 'center', null, count($this->getHeading()));
            $this->CI_controller->table->add_row($cell->asArray());
        }
        //Devuelvo la tabla completa.
        return $this->CI_controller->table->generate();
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
        $this->class .= ' ' . $pClass;
    }

    //Agrega un item.
    //Un item puede ser un array de textos, o bien un array de DefaultTableCell
    public function addItem($pItem) {
        $this->items[] = $pItem;
    }

    /*
     * Metodos Accesores
     */

    public function getItems() {
        return $this->items;
    }

    public function getBorder() {
        return $this->border;
    }

    public function getCellpadding() {
        return $this->cellpadding;
    }

    public function getCellspacing() {
        return $this->cellspacing;
    }

    public function getClass() {
        return $this->class;
    }

    public function isVisible() {
        return $this->visible;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getHeading() {
        return $this->heading;
    }

    public function getId() {
        return $this->id;
    }

    public function setItems($pItems) {
        $this->items = $pItems;
    }

    public function setBorder($pBorder) {
        $this->borde = $pBorder;
    }

    public function setCellspacing($pSpacing) {
        $this->cellspacing = $pSpacing;
    }

    public function setCellpadding($pPadding) {
        $this->cellpadding = $pPadding;
    }

    public function setVisible($pVisible) {
        $this->visible = $pVisible;
    }

    public function setAttributes($pAttributes) {
        $this->attributes = $pAttributes;
    }

    public function setHeading($pHeading) {
        $this->heading = $pHeading;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

}

?>
