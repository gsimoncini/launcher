<?php

class Client_Model extends BaseModel {

    function Client_Model() {
        parent::__construct();
    }

    function getClientsBy($pFilters) {

        $sql = "SELECT * FROM client WHERE " . $pFilters . " = 1";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function getClientBy($pField, $pId) {
        $sql = "SELECT * FROM client WHERE $pField = $pId LIMIT 1";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    function save($pClient) {
        if (!isset($pClient['id'])) {
            $sql = "INSERT INTO client(name, active, reference, doc_number, phone, barcode) VALUES ('" . $pClient['name'] . "', " . $pClient['active'] . ", '" . $pClient['reference'] . "',"
                    . "'" . $pClient['doc_number'] . "', '" . $pClient['phone'] . "', '" . $pClient['barcode'] . "')";
            $this->db->query($sql);
            return $this->db->insert_id();
        } else {
            $sql = "UPDATE client SET"
                    . "name = " . $pClient['name'] . ","
                    . "active = " . $pClient['active'] . ","
                    . "reference = " . $pClient['reference'] . ","
                    . "doc_number = " . $pClient['doc_number'] . ", "
                    . "phone = " . $pClient['phone'] . ","
                    . "barcode = " . $pClient['barcode'] . ""
                    . "WHERE id = " . $pClient['id'] . "";
            $this->db->query($sql);
            return null;
        }
    }

    public function elements_for_dropdown() {
        $elements = $this->getClientsBy('active');

        $result = array();
        foreach ($elements as $value) {
            $result[] = new DropdownItem($value->id, $value->name);
        }
        return $result;
    }

}
