<?php

class Setting_Model extends BaseModel {

    function Setting_Model() {
        parent::__construct();

        $this->initialize('settings');
    }

    function get_smtp() {
        $sql = "SELECT * FROM settings WHERE setting IN ('smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'from_mail', 'from_name', 'smtp_crypto') ";
        $query = $this->db->query($sql);
        $result = array();
        foreach ($query->result() AS $item) {
            $result[$item->setting] = $item->value;
        }
        return (object) $result;
    }

    function get($pSetting) {
        $sql = "SELECT s.value FROM settings s WHERE s.setting = '" . $pSetting . "';";
        $query = $this->db->query($sql);

        return $query->row() == null ? null : $query->row()->value;
    }

    function all_elements() {
        $sql = "SELECT
                   s.entity,
                   s.setting,
                   s.value
               FROM settings s ";

        $query = $this->db->query($sql);

        return $query->result();
    }

    function get_groups() {
        $sql = "SELECT DISTINCT settings.entity FROM public.settings;";
        $query = $this->db->query($sql);
        $groups = $query->result();
        foreach ($groups as &$val) {
            $val = $val->entity;
        };
        return $groups;
    }

    function get_by_group($group) {
        $sql = "SELECT settings.setting, settings.value
                FROM public.settings
                WHERE settings.entity = '" . $group . "';";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function delete_setting($group, $key) {
        $sql = "DELETE FROM public.settings
                WHERE settings.entity = '" . $group . "' AND settings.setting = '" . $key . "';";
        return $this->db->query($sql);
    }

    function insert_setting($group, $key, $val) {
        $sql = "INSERT INTO public.settings (entity, setting, value)
                VALUES ('" . $group . "', '" . $key . "', '" . $val . "');";
        return $this->db->query($sql);
    }

    function update_value($group, $key, $val) {
        $sql = "UPDATE public.settings SET value='" . $val . "' WHERE entity = '" . $group . "' AND setting = '" . $key . "';";
        return $this->db->query($sql);
    }

    function update_key($group, $key, $val) {
        $sql = "UPDATE public.settings SET setting='" . $val . "' WHERE entity = '" . $group . "' AND setting = '" . $key . "';";
        return $this->db->query($sql);
    }

    function delete_group($group, $key) {
        $sql = "DELETE FROM public.settings
                WHERE settings.entity = '" . $group . "';";
        return $this->db->query($sql);
    }

}
