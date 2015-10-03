<?php

class Client_Group_Type_Model extends BaseModel {

    function Client_Group_Type_Model() {
        parent::__construct();

        $this->initialize('client_group_type_lkp');

        $this->rename_column('active', 'status_id');
    }

    function get_by_id($pId) {
        if ($pId == '' || $pId == null)
            return null;

        $sql = "SELECT
                    cgt_lkp.id,
                    cgt_lkp.description,
                    cgt_lkp.active
                FROM client_group_type_lkp cgt_lkp
                WHERE id = " . $pId . "
                LIMIT 1;";
        $query = $this->db->query($sql);

        return $query->row();
    }

    function all_elements($pFilters = null) {
        if ($pFilters == null)
            $pFilters = array();

        $filters_sql = '';

        if (isset($pFilters['status_filter']) && $pFilters['status_filter'] != null)
            $filters_sql .= " AND cgt_lkp.active IN " . $this->_array_to_sql($pFilters['status_filter']);

        $sql = "SELECT
                    cgt_lkp.id,
                    cgt_lkp.description,
                    cgt_lkp.active AS status_id,
                    array_to_string(array_agg(cg_lkp.id ORDER BY cg_lkp.name), ',') AS client_group_ids,
                    array_to_string(array_agg(cg_lkp.name ORDER BY cg_lkp.name), '{{separator}}') AS client_group_names,
                    array_to_string(array_agg(cg_lkp.description ORDER BY cg_lkp.name), '{{separator}}') AS client_group_descriptions
                FROM client_group_type_lkp cgt_lkp
                    LEFT JOIN client_group_lkp cg_lkp ON cg_lkp.client_group_type_id = cgt_lkp.id
                    WHERE 1 = 1 " . $filters_sql . "
                GROUP BY cgt_lkp.id
                ORDER BY cgt_lkp.description;";

        $query = $this->db->query($sql);

        return $query->result();
    }

    function elements_for_dropdown($pHasNullOption = false) {
        $result = array();

        if ($pHasNullOption)
            $result[] = new DropdownItem('-1', ' - '.$this->lang->line('label_without_client_group_type'). ' - ');

        foreach ($this->elements() as $element)
            $result[] = new DropdownItem($element->id, $element->description);

        return $result;
    }

    function status_for_dropdown() {
        try {
            $array = array((object) array('id' => 1, 'name' => 'Activo'), (object) array('id' => 2, 'name' => 'Desactivo'));
            $result = array();
            foreach ($array AS $status)
                $result[] = new DropdownItem($status->id, $status->name);
            return $result;
        } catch (Exception $e) {
            return array();
        }
    }

    function get_default_filters() {
        $filters['status_filter'] = array();

        return $filters;
    }

    function save_status($pClientTypeGroup) {
        return parent::save($pClientTypeGroup);
    }

}
