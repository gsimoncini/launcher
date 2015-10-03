<?php

Class Client_Model extends CI_Model {

    function Client_Model() {

        parent::__construct();
    }

    function all_elements($filters) {
        $sql = "SELECT * FROM client WHERE status_id = 1;";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function all_client_for_dropdown($pClientGroups = null) {
        if ($pClientGroups !== null && $pClientGroups !== '')
            $filters['client_group_filter'] = is_array($pClientGroups) ? $pClientGroups : array($pClientGroups);
        else
            $filters = null;

        $result = array();

        foreach ($this->all_elements($filters) as $element)
            $result[] = new DropdownItem($element->id, $element->name);

        return $result;
    }

    function get_client_by_key($pKey) {
        $sql = "SELECT * FROM client WHERE client_key = '" . $pKey . "' AND status_id = 1 AND due_date > current_timestamp";


        $query = $this->db->query($sql);

        if ($query->row() != '') {
            return $query->row();
        } else {
            return NULL;
        }
    }

    //Devuelve la descripcion segun el tipo de regimen de afip
    function get_name_by_afip_type($afip_type) {
        $result = '';
        switch ($afip_type) {
            case '1':
                $result = 'Responsable inscripto';
                break;
            case '2':
                $result = 'Responsable monotributo';
                break;
            case '3':
                $result = 'Consumidor final';
                break;
            case '4':
                $result = 'Monotributo social';
                break;
            case '5':
                $result = 'Exento';
                break;

            default:
                $result = 'Responsable inscripto';
                break;
        }
        return $result;
    }

    //Devuelve la descripcion segun el tipo de documento
    function get_name_by_doc_type($doc_type) {
        switch ($doc_type) {
            case '80':
                return 'CUIT';
                break;
            case '86':
                return 'CUIL';
                break;
            case '96':
                return 'DNI';
                break;
            default:
                return 'Doc. (otro)';
                break;
        }
    }

    //Devuelvelos las condiciones de venta segun codigo

    function get_name_sale_condition($sale_condition_id) {
        switch ($sale_condition_id) {
            case 1:
                return 'CUENTA CORRIENTE';
                break;
            case 2:
                return 'CONTADO';
                break;
            default:
                return 'CONTADO';
                break;
        }
    }

}
