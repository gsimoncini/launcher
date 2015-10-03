<?php

class Client_Group_Model extends BaseModel {

    protected $relation_system_user_client = false;

    function Client_Group_Model() {
        parent::__construct();

        $this->initialize('client_group_lkp');

        $this->rename_column('active', 'status_id');
        $this->rename_column('client_group_type_id', 'client_group_type');

        /* Propagación de relacion cliente/usuario segun perfil logueado */
        $profile = $this->Profile_Model->_profile_by_user_id($this->session->userdata('user_id'));
        $this->relation_system_user_client = $profile->propagate_system_user_client;
    }

    function get_by_id($pId) {
        if ($pId == '' || $pId == null)
            return null;

        $sql = "SELECT *
                FROM client_group_lkp
                WHERE id = " . $pId . "
                LIMIT 1;";
        $query = $this->db->query($sql);

        return $query->row();
    }

    //Grupos de clientes segun usuario identificado
    function elements($pFilters = null) {
        if ($pFilters == null)
            $pFilters = array();

        $filters_sql = '';

        if (isset($pFilters['group_type_filter']) && $pFilters['group_type_filter'] != null)
            $filters_sql .= " AND cgt_lkp.id IN " . $this->_array_to_sql($pFilters['group_type_filter']);

        $username = $this->session->userdata('user_id');

        if ($this->relation_system_user_client)
            $filter_by_user = " 1 = 1";
        else
            $filter_by_user = " suc.username = '" . $username . "'";

        $sql = "SELECT DISTINCT cgl.id, cgl.name, cgl.description, cgl.active
                FROM client_group_lkp cgl
                    LEFT JOIN client_group cg ON cg.client_group_id = cgl.id
                    LEFT JOIN system_user_client suc ON suc.client_id = cg.client_id
                    LEFT JOIN client_group_type_lkp cgt_lkp ON cgt_lkp.id = cgl.client_group_type_id
                WHERE " . $filter_by_user . $filters_sql . "
                ORDER BY cgl.name;";

        $query = $this->db->query($sql);

        return $query->result();
    }

    function elements_for_dropdown($pGroupType = null, $pHasNullOption = false) {
        if ($pGroupType !== null && $pGroupType !== '')
            $filters['group_type_filter'] = is_array($pGroupType) ? $pGroupType : array($pGroupType);
        else
            $filters = null;

        $result = array();

        if ($pHasNullOption)
            $result[] = new DropdownItem('-1', ' - ' . $this->lang->line('label_without_client_group') . ' - ');

        if ($this->relation_system_user_client)
            $elements = $this->all_elements($filters);
        else
            $elements = $this->elements($filters);

        foreach ($elements as $element)
            $result[] = new DropdownItem($element->id, $element->name);

        return $result;
    }

    function first_element() {
        $elements = $this->elements();
        return isset($elements[0]) ? $elements[0] : null;
    }

    //Devuelve todos los centros sin filtro
    function all_client_groups_for_dropdown($pHasNullOption = false) {
        $result = array();

        if ($pHasNullOption)
            $result[] = new DropdownItem('-1', ' - ' . $this->lang->line('label_without_client_group') . ' - ');

        foreach ($this->all_client_groups() as $element)
            $result[] = new DropdownItem($element->id, $element->name);

        return $result;
    }

    //Devuelve todos los centros sin filtro
    function all_client_groups() {
        $sql = "SELECT cg.id AS id, cg.name, cg.description
                FROM client_group_lkp cg
                WHERE cg.active = 1
                ORDER BY cg.name;";

        $query = $this->db->query($sql);

        return $query->result();
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

        $filters['group_type_filter'] = array();

        return $filters;
    }

    function all_elements($pFilters = null) {
      try {
        if ($pFilters == null)
            $pFilters = array();

        $filters_sql = '';
        if (isset($pFilters['status_filter']) && $pFilters['status_filter'] != null)
            $filters_sql .= " AND cg_lkp.active IN " . $this->_array_to_sql($pFilters['status_filter']);

        if (isset($pFilters['group_type_filter']) && $pFilters['group_type_filter'] != null) {
            $filters_sql .= "AND cgt_lkp.id IN " . $this->_array_to_sql($pFilters['group_type_filter']);
        }

        if (isset($pFilters['client_group_type_filter']) && $pFilters['client_group_type_filter'] != null)
            $filters_sql .= " AND cg_lkp.client_group_type_id IN " . $this->_array_to_sql($pFilters['client_group_type_filter']);

        $sql = "SELECT
                    cg_lkp.id,
                    cg_lkp.active AS status_id,
                    cg_lkp.name,
                    cg_lkp.description,
                    cg_lkp.client_group_type_id,
                    cgt_lkp.description AS client_group_type_name
                FROM client_group_lkp cg_lkp
                LEFT JOIN client_group_type_lkp cgt_lkp ON cgt_lkp.id = cg_lkp.client_group_type_id
                WHERE 1 = 1 " . $filters_sql . " 
                ORDER BY cg_lkp.name";


        $query = $this->db->query($sql);

        return $query->result();
      } catch(Exception $e){
          return array();
      }
    }

    function save_status($pClientGroup) {
        return parent::save($pClientGroup);
    }

    function unassign_client_group($pId) {
        if ($pId == '' || $pId == null)
            return null;

        $sql = "UPDATE client_group_lkp
                SET client_group_type_id= NULL
                WHERE id = " . $pId . "
                ";
        $this->db->query($sql);

        if ($this->db->affected_rows() == 0)
            return false;
        else
            return true;
    }

    function save($pClientGroup) {
        if ($pClientGroup->client_group_type == '-1')
            $pClientGroup->client_group_type = null;

        return parent::save($pClientGroup);
    }

    function check_client_group_type($pClientGroupTypeId, $pClientGroupId) {
        $filters['group_type_filter'] = $pClientGroupTypeId;
        $result = $this->all_elements($filters);
        foreach ($result AS $client_group) {
            if ($client_group->id == $pClientGroupId) {
                return true;
                break;
            }
        }
        return false;
    }

}
