<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Es la clase madre para los modelos. Implementa métodos genéricos para la creación de consultas.
 *
 * @author Cristian Da Silva
 */

class BaseModel extends CI_Model {

    var $table;
    var $columns;
    var $primaryKey;
    var $filterName;

    function __construct() {
        parent::__construct();

        $this->load->database();
    }

    //Initializa el modelo
    function initialize($pTable, $pPrimaryKey = 'id') {
        $this->table = $pTable;
        $this->columns = $this->_get_columns();
        $this->primaryKey = $pPrimaryKey;
        $this->filterName = $pTable . '_filters';
    }

    //Renombra una columna
    function rename_column($pColumn, $pName) {
        $this->columns[$pName] = $pColumn;

        unset($this->columns[$pColumn]);
    }

    //Inserta o actualiza un registro dependiendo del caso
    function save($pObject) {
        $this->db->trans_start();

        $primary_key = $this->primaryKey;

        $this->_save($pObject);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false)
            return false;
        else
            return $pObject->$primary_key;
    }

    //Contiene las operaciones extras a realizar en una operación de guardado
    function save_operations($pObject) {
        return null;
    }

    //Almacena sin uso de la transacción
    function _save($pObject) {
        $primary_key = $this->primaryKey;

        if (isset($pObject->$primary_key) == false || $pObject->$primary_key == null)
            $pObject->$primary_key = $this->insert($pObject);
        else
            $pObject->$primary_key = $this->update($pObject);

        $this->save_operations($pObject);

        return $pObject->$primary_key;
    }

    //Inserta un registro en la tabla del modelo
    function insert($pObject) {
        $data = $this->_parse_object_attributes($pObject);
        $primary_key = $this->primaryKey;

        if (empty($data[$this->columns[$primary_key]])) {
            unset($pObject->$primary_key);
            unset($data[$this->columns[$primary_key]]);
        }

        $this->db->insert($this->table, $data);

        return isset($pObject->$primary_key) ? $pObject->$primary_key : $this->db->insert_id();
    }

    //Actualiza un registro de la tabla del modelo
    function update($pObject) {
        $data = $this->_parse_object_attributes($pObject);
        $primary_key = $this->primaryKey;

        $this->db->where($this->columns[$primary_key], $pObject->$primary_key);
        $this->db->update($this->table, $data);

        return $pObject->$primary_key;
    }

    //Elimina un registro de la tabla del modelo
    function delete($pObject) {
        $primary_key = $this->primaryKey;

        $this->db->where($this->columns[$primary_key], $pObject->$primary_key);
        $this->db->delete($this->table);
    }

    //Devuelve un elemento por su clave primaria
    function get($pValue) {
        $primary_key = $this->primaryKey;

        $this->db->select($this->_select_columns());

        $query = $this->db->get_where($this->table, array($this->columns[$primary_key] => $pValue));

        return $query->row();
    }

    //Devuelve los elementos de la tabla
    function elements() {
        $condition = in_array('active', $this->columns) ? array('active' => 1) : array();
        $query = $this->db->get_where($this->table, $condition);

        return $query->result();
    }

    //Devuelve los elementos de la tabla para poder utilizar en un dropdown
    function elements_for_dropdown($pText, $pFilters = null) {
        $result = array();
        $primary_key = $this->primaryKey;

        foreach ($this->elements($pFilters) as $element) {
            $text = $pText;

            $this->_parse_dropdown_item($element, $text);

            $result[] = new DropdownItem($element->$primary_key, $text);
        }

        return $result;
    }

    //Devuelve el primer elemento de la tabla (usado para los dropdowns)
    function dropdown_first_element($pFilters = null) {
        $elements = $this->elements_for_dropdown($pFilters);
        $first_element = $this->first_element($elements);

        return $this->get($first_element->id);
    }

    //Devuelve el primer elemento de una lista dada, aplicando ordenamiento.
    function first_element($pElements) {
        uasort($pElements, array($this, '_compare'));

        $first_element = reset($pElements);

        return $first_element;
    }

    //Setea los filtros en sesión
    function set_filters($pFilterList) {
        $this->Session_Model->setSessionName($this->filterName);
        $this->Session_Model->set_elements($pFilterList);
    }

    //Quita los filtros en sesión
    function unset_filters() {
        $this->Session_Model->setSessionName($this->filterName);
        $this->Session_Model->remove_all();
    }

    //Obtiene los filtros
    function get_filters() {
        $this->Session_Model->setSessionName($this->filterName);

        return $this->Session_Model->elements();
    }

    //Devuelve verdadero si los filtros normales estan en NULL.
    function is_filter_null() {
        return BaseModel::get_filters() == null;
    }

    //Devuelve las columnas de la tabla del modelo
    function _get_columns() {
        $columns = array();

        foreach ($this->db->list_fields($this->table) as $value)
            $columns[$value] = $value;

        return $columns;
    }

    //Convierte los atributos de un objeto a sus respectivas columnas
    function _parse_object_attributes($pObject) {
        $data = array();
        $attributes = get_object_vars($pObject);

        foreach ($attributes as $attribute => $value) {
            if ($value === '')
                $value = null;

            if (isset($this->columns[$attribute]))
                $data[$this->columns[$attribute]] = $value;
        }

        return $data;
    }

    //Convierte una cadena con un formato específico en un texto para usarse en los dropdowns.
    function _parse_dropdown_item($pElement, &$pText, $pStartDelimeter = '{', $pEndDelimeter = '}') {
        $start_index = strpos($pText, $pStartDelimeter);
        $end_index = strpos($pText, $pEndDelimeter);

        if ($start_index === false || $end_index === false)
            return $pText;

        $length = $end_index - $start_index + 1;

        $string_replace = substr($pText, $start_index, $length);
        $string_attribute = substr($pText, $start_index + 1, $length - 2);

        $pText = str_replace($string_replace, isset($pElement->$string_attribute) ? $pElement->$string_attribute : '', $pText);

        $this->_parse_dropdown_item($pElement, $pText, $pStartDelimeter, $pEndDelimeter);

        return $pText;
    }

    //Devuelve una cadena con los nombres de las columnas para usar en los select
    function _select_columns() {
        $select = '';
        $separator = '';

        foreach ($this->columns as $name => $column) {
            $select .= $separator . $column . ' AS ' . $name;
            $separator = ', ';
        }

        return $select;
    }

    //Convierte un array en una condición IN
    function _array_to_sql($pArray, $pStartDelimiter = '(', $pEndDelimiter = ')') {
        if (is_array($pArray) == false)
            $pArray = array($pArray);

        $sql = $pStartDelimiter;

        foreach ($pArray as $index => $value) {
            if ($value == null)
                $value = 'NULL';

            $sql .= ($index == 0 ? '' : ', ') . $value;
        }

        $sql .= $pEndDelimiter;

        return $sql;
    }

    //Convierte un texto en una condición LIKE
    function _find_by_text($pColumn, $pText) {
        $sql = "UPPER(" . $pColumn . "::character varying) LIKE UPPER('%" . $pText . "%')";

        return $sql;
    }

    //Compara dos valores
    function _compare($x, $y) {
        if (strtoupper($x->value) == strtoupper($y->value))
            return 0;

        if (strtoupper($x->value) < strtoupper($y->value))
            return -1;

        return 1;
    }

    //Comprueba si existe una tabla
    function table_exists($pTableName) {
        $sql = "select * from pg_tables where tablename='" . $pTableName . "'; ";
        $query = $this->db->query($sql);
        return $query->row() != null;
    }

}
