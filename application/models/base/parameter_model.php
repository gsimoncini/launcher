<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mark_Model
 *
 * @author Mirco Bombieri
 */
class Parameter_Model extends CI_Model {

    function Parameter_Model() {
        parent::__construct();
        $this->load->database();
    }

    function get_by_parameter($parameter) {
        try {
          /*  //Levanto el parametro especificado
            $sql = "SELECT p.parameter, p.type, p.value FROM parameter p WHERE parameter='" . $parameter . "' LIMIT 1;";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $r = $query->row();
                settype($r->value, $r->type);
                return $r;
            } else
                return array();*/
            
            if($parameter == 'home')
                return (object) array('value'=>$this->config->item('landing_page'));
            
        } catch (Exception $e) {
            return array();
        }
    }

    function set_parameter($name, $type, $value) {
        try {
            $this->db->trans_start();
            if ($this->get_by_parameter($name) != null)
                $this->db->query("UPDATE parameter SET type='" . $type . "', value='" . $value . "' WHERE parameter='" . $name . "';");
            else
                $this->db->query("INSERT INTO parameter (parameter,type,value) VALUES('" . $name . "','" . $type . "','" . $value . "'); ");
            $this->db->trans_complete();
            if ($this->db->trans_status() === false)
                $this->messages->set('warning', 'No se pudo actualiazar el parametro. Intente nuevamente.');
            else
                $this->messages->set('success', 'El parametro fue actualizado.');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}

?>