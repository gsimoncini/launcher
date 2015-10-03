<?php

class Doc_Type_Model extends CI_Model {

    //Devuelve una colección de DropdownItems para formar un dropdown de Tipos de Documento
    function elements_for_dropdown() {
        try {
            $sql = "SELECT id, name FROM doc_type_lkp ORDER BY name;";
            $query = $this->db->query($sql);
            $result = array();
            foreach ($query->result() AS $docType)
                $result[] = new DropdownItem($docType->id, $docType->name);
            return $result;
        } catch (Exception $e) {
            return array();
        }
    }

    //Devuelve el nombre del tipo de documento en funcion de su id
    public function name_by_id($pId = '0') {
        $name = "";
        if (isset($pId) and $pId > 0) {
            $sql = "SELECT name FROM doc_type_lkp WHERE id=" . $pId . " LIMIT 1;";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $name = $query->row('name');
            } else {
                $name = "";
            }
        }
        return $name;
    }

}
