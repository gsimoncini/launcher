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
class TreeTable {

    var $items = array();
    var $action_table = array();
    var $id_father = null;
    var $class = 'default_tabletree';
    var $class_child = 'default_child_tabletree';
    var $class_row = 'default_child_tabletree';
    var $class_column = 'default_column_tabletree';
    var $class_column_head = 'default_headcolumn_tabletree';
    var $class_child_head = 'default_headchild_tabletree';
    var $visible = true;
    var $attributes = '';
    var $heading = array();
    var $remove = true;
    var $id;
    var $urls;
    var $namefathercolumnHead = 'Nombre de Padre';
    var $namecolumnHead = 'Nombre';
    var $actioncolumn = 'Acciones';

    function TreeTable($pId, $pHeading, $pItems = array(), $pClass = 'default_tabletree') {
        $this->id = $pId;
        $this->heading = $pHeading;
        $this->items = $pItems;
        $this->class = $pClass;
    }

    //Devuelve el codigo HTML de una tabla
    public function generate() {
        $defaults = array('class' => $this->getClass(), 'id' => $this->getId(), 'style' => 'display:table');

        //Defino el la tabla
        $element = json_encode($this->action_table);
//        $element = substr($element, 1, ((strlen($element)) - 2));
        $table = "<script> var element=" . $element . ";";

        foreach ($this->urls AS $array) {
            foreach ($array as $name => $url) {
                $table.=" var " . $name . "='" . $url . "';";
            }
        }
        $table.="</script> ";
        $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' >';

        //Coloco el encabezado
        $defaults_child = array('class' => $this->getClassChildHead());
        $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_child) . ' >';
        //cargo cada columna 

        $defaults_column = array('class' => $this->getClassColumnHead());
        $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >';
        $table.= $this->getNameColumnHead() . '</div>';
        $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >';
        $table.= $this->getNameFatherColumnHead() . '</div>';
        $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >';
        $table.= $this->getActionColumn() . '</div>';
        $table.='<div class="clear"></div>';
        $table.='</div>';

        //Agrego cada item
        $count = 0;

        foreach ($this->items as $item) {

            //Si la celda es un objeto, lo convierto en array, sino coloco el texto
            if (is_object($item))
                $newCell = $item->asArray();
            else
                $newCell = $item;

            $defaults_child = array('class' => $this->getClassChild(), 'id' => 'id_' . $newCell['id']);
            $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_child) . ' >';
            $defaults_column = array('class' => $this->getClassColumn());
            $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >';
            $table.= $newCell['name'] . '</div>';
            $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >-</div>';

            $defaults_column = array('class' => 'default_column_tabletree_action');
            $table.= '<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >';
            foreach ($this->action_table AS $action)
                $table.= '<a href="#" id="id_' . $action['className'] . '_' . $newCell['id'] . '" title="' . $action['title'] . '" class="' . $action['className'] . '" onclick="' . $action['js'] . '(' . $newCell['id'] . ');">' . nbs(6) . '</a>';
            $table.= '</div>';

            $table.='<div class="clear"></div>';
            $table.='</div>';
            $count++;
        }
        $table.='</div>';

        //Si no hay elementos lo notifico.
        if ($count == 0) {
            $table.='<div ' . $this->_parse_form_attributes($this->attributes, $defaults_column) . ' >No hay elementos.</div>';
        }
        //Devuelvo la tabla completa.
        return $table;
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

    public function getClassChild() {
        return $this->class_child;
    }

    public function getClass() {
        return $this->class;
    }

    public function getHeading() {
        return $this->heading;
    }

    public function getClassColumn() {
        return $this->class_column;
    }

    public function getRemove() {
        return $this->remove;
    }

    public function getId() {
        return $this->id;
    }

    public function getClassChildHead() {
        return $this->class_child_head;
    }

    public function getClassColumnHead() {
        return $this->class_column_head;
    }

    public function setItems($pItems) {
        $this->items = $pItems;
    }

    public function setClass($pClass) {
        $this->class = $pClass;
    }

    public function setClassChild($pClass) {
        $this->class_child = $pClass;
    }

    public function setClassChildHead($pClass) {
        $this->class_child_head = $pClass;
    }

    public function setClassColumnHead($pClass) {
        $this->class_column_head = $pClass;
    }

    public function setHeading($pHeading) {
        $this->heading = $pHeading;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function setNameColumnHead($pName) {
        $this->namecolumnHead = $pName;
    }

    public function setNameFatherColumnHead($pName) {
        $this->namefathercolumnHead = $pName;
    }

    public function setActionColumn($pName) {
        $this->actioncolumn = $pName;
    }

    public function getActionColumn() {
        return $this->actioncolumn;
    }

    public function getNameColumnHead() {
        return $this->namecolumnHead;
    }

    public function getNameFatherColumnHead() {
        return $this->namefathercolumnHead;
    }

    //pAction = array(name=>'nombre','title'=>'titulo','js'=>'js a ejecuar','type'=>'tipo');
    public function addAction($tClass, $tTitle, $tJS) {
        $this->action_table[] = array('id' => 'id_' . $tClass . '_', 'className' => $tClass, 'title' => $tTitle, 'js' => $tJS);
    }

    public function addUrl($name, $value) {
        $this->urls[] = array($name => $value);
    }

}

?>