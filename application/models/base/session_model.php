<?php

/*
 * Este modelo implementa mecanismos para administrar almacenamiento en 
 * una sesión.
 */

/**
 * Description of session_model
 *
 * @author Mirco Bombieri
 */
class Session_Model extends CI_Model {

    var $session_name = 'session_project';

    function Session_Model($session_name = null) {
        parent::__construct();
        if ($session_name != null)
            $this->session_name = $session_name;
    }

    //Devuelve todos los elementos en la tabla
    function elements() {
        $list = $this->session->userdata($this->session_name);
        if ($list == null)
            return array();
        else
            return $list;
    }

    function set_elements($list) {
        $this->session->set_userdata($this->session_name, $list);
    }

    //Agrega un elemento a la lista
    function add_element($element) {
        //Setea el proximo id
        $element['id'] = $this->nextid();

        $elements = $this->elements();
        $elements[] = $element;
        $this->session->set_userdata($this->session_name, $elements);
        return count($elements);
    }

    //Elimina todos los elementos
    function remove_all() {
        try {
            $this->session->set_userdata($this->session_name, array());
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    //Devuelve la cantidad de elementos que tiene la  lista
    function count() {
        try {
            return count($this->elements());
        } catch (Exception $e) {
            return 0;
        }
    }

    //Devuelve el próximo ID interno para la lista
    function nextid() {
        try {
            if ($this->count() == 0)
                return 1;
            $max = 0;
            $elements = $this->elements();

            for ($i = 0; $i < $this->count(); $i++) {
                $id = $elements[$i]['id'];
                if ($max < $id) {
                    $max = $id;
                }
            }
            return ($max + 1);
        } catch (Exception $e) {
            return -1;
        }
    }

    //Devuelve el elemento con id
    function get_element($pId) {
        try {
            if ($this->count() == 0)
                return null;
            foreach ($this->elements() AS $element) {
                if ($element['id'] == $pId) {
                    return $element;
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    //Devuelve el elemento con determinado valor en un campo
    function get_element_key($pValue, $pField) {
        try {
            if ($this->count() == 0)
                return null;
            foreach ($this->elements() AS $element) {
                if ($element[$pField] == $pValue) {
                    return $element;
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    //Actualiza los datos de un elemento
    function edit_element($e) {
        $this->remove($e['id']);
        $this->add_element($e);
    }

    //devuelve una coleccion de valores de un campo dado de los elementos
    function collect_field_value($pField) {
        $collected = array();
        try {
            if ($this->count() != 0) {
                foreach ($this->elements() AS $element) {
                    if (isset($element[$pField]))
                        $collected[] = $element[$pField];
                }
            }
            return $collected;
        } catch (Exception $e) {
            return null;
        }
    }

    //Elimina el elemento con id = $pId
    function remove($pId) {
        $result = array();
        $elements = $this->session->userdata($this->session_name);
        for ($i = 0; $i < count($this->session->userdata($this->session_name)); $i++) {
            if ($elements[$i]['id'] != $pId) {
                $result[] = $elements[$i];
            }
        }
        $this->session->set_userdata($this->session_name, $result);
    }

    //Elimina el elemento con $pField = $pValue
    function remove_key($pValue, $pField) {
        $result = array();
        $elements = $this->session->userdata($this->session_name);
        for ($i = 0; $i < count($this->session->userdata($this->session_name)); $i++) {
            if ($elements[$i][$pField] != $pValue) {
                $result[] = $elements[$i];
            }
        }
        $this->session->set_userdata($this->session_name, $result);
    }

    /*
     * Accesores
     */

    //Devuelve el nombre de la sesion que se está trabajando.
    public function getSessionName() {
        return $this->session_name;
    }

    //Permite definir el nombre de la sesión a trabajar
    public function setSessionName($pName) {
        $this->session_name = $pName;
    }

}

?>