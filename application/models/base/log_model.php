<?php

/**
 * Modelo que administra consultas de auditoría.
 *
 * @author Mirco Bombieri
 */
class Log_Model extends CI_Model {

    function Log_Model() {
        parent::__construct();
        $this->Session_Model->setSessionName('log_filters');
    }

    //Devuleve la cantidad de accesos fallidos que hubo entre 2 fechas.
    function _users_failed_access() {
        //cargo el asistente de fechas para convertir la fecha del post a mysql
        $this->load->helper('date_helper');

        $sql = "SELECT u.username, TO_CHAR(u.date,'DD/MM/YYYY') AS date, TO_CHAR(u.time,'HH:MI:SS') AS time
                    FROM userdata_fail_log as u";

        $filters = $this->get_filters();

        if ($filters == null)
            $filters = $this->get_default_filters();

        $filter_username = $filters['filter_username'];

        //Conveirto las fechas a mysql
        $filter_date_from = to_mysql_date($filters['filter_date_from']);
        $filter_date_to = to_mysql_date($filters['filter_date_to']);

        if ($filter_username != '') {
            //Si filtra por nombre
            $sql.= " WHERE UPPER(u.username) LIKE UPPER('%" . $filter_username . "%') ";
            if ($filter_date_from != '' && $filter_date_to != '') {
                //si filtra, además de por nombre, por fechas
                $sql.= " AND u.date BETWEEN '" . $filter_date_from . "' AND '" . $filter_date_to . "'";
            }
        } elseif ($filter_username == '' && ($filter_date_from != '' && $filter_date_to != '')) {
            //Si no filtra por nombre pero si por fechas
            $sql.= " WHERE u.date BETWEEN '" . $filter_date_from . "' AND '" . $filter_date_to . "'";
        }

        $sql .= " GROUP BY u.username, u.date, u.time ORDER BY u.date, u.time;";

        $query = $this->db->query($sql);

        return $query->result();
    }

    //Devuleve la cantidad de accesos que hubo entre 2 fechas.
    function _users_access() {
        //cargo el asistente de fechas para convertir la fecha del post a mysql
        $this->load->helper('date_helper');

        $sql = "SELECT a.username, TO_CHAR(a.date,'DD/MM/YYYY') AS date, TO_CHAR(a.time,'HH:MI:SS') AS time
                FROM access_log as a";

        $filters = $this->get_filters();

        if ($filters == null)
            $filters = $this->get_default_filters();

        $filter_username = $filters['filter_username'];

        //Conveirto las fechas a mysql
        $filter_date_from = to_mysql_date($filters['filter_date_from']);
        $filter_date_to = to_mysql_date($filters['filter_date_to']);

        if ($filter_username != '') {
            //Si filtra por nombre
            $sql.= " WHERE UPPER(a.username) LIKE UPPER('%" . $filter_username . "%') ";
            if ($filter_date_from != '' && $filter_date_to != '') {
                //si filtra, además de por nombre, por fechas
                $sql.= " AND a.date BETWEEN '" . $filter_date_from . "' AND '" . $filter_date_to . "'";
            }
        } elseif ($filter_username == '' && ($filter_date_from != '' && $filter_date_to != '')) {
            //Si no filtra por nombre pero si por fechas
            $sql.= " WHERE a.date BETWEEN '" . $filter_date_from . "' AND '" . $filter_date_to . "'";
        }

        $sql .= " GROUP BY a.username, a.date, a.time ORDER BY a.date, a.time;";

        $query = $this->db->query($sql);

        return $query->result();
    }

    function set_filters($pFilterList) {
        $this->Session_Model->set_elements($pFilterList);
    }

    function unset_filters() {
        $this->Session_Model->remove_all();
    }

    function get_filters() {
        return $this->Session_Model->elements();
    }

    function get_default_filters() {
        $filters['filter_username'] = '';
        $filters['filter_date_from'] = date('d/m/Y', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $filters['filter_date_to'] = date('d/m/Y');

        return $filters;
    }

}
