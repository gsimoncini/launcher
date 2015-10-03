<?php

/*
 * Este modelo implementa los mecanismos para formar el menu.
 */

/**
 * Description of menu_model
 *
 * @author Mirco Bombieri
 */
class Menu_Model extends CI_Model {

    var $padding_increment = 10;

    function Menu_Model() {
        parent::__construct();
        $this->load->database();
    }

    //Genera el HTML necesario para el menu, de acuerdo al usuario logueado.
    function generate() {
        $countItems = 0;
        $result = '<ul class="nav navigation" id="side-menu">';

        $result .= '<li><div><button class="menu-toggle btn btn-inverse btn-sm"><b class="fa fa-bars"></b></button></div></li>';

        //busco las categorias sin padres.
        foreach ($this->_withoutparent_items() AS $item) {
            if ($item->id == 5) {
                $item->url = (count($this->Parameter_Model->get_by_parameter('home')) == 0) ? $item->url : $this->Parameter_Model->get_by_parameter('home')->value;
            }

            $icon = ($item->icon != '') ? '<i class="fa ' . $item->icon . ' fa-fw"></i>' : '';

            if ($item->is_category) {
                $childs = $this->_get_item_childs($item->id);
                //Verifico si tiene hijos
                if (count($childs) > 0) {
                    $subitems = $this->_desplegate_subitems($item, 15);
                    //si hay subitems permitidos
                    if ($subitems != '') {
                        $result .= '<li><a href="#">' . $icon . '<span class="menu-text">' . $item->name . '</span><span class="fa arrow"></span></a>';
                        $result .= '<ul class="nav">' . $subitems . "</ul></li>";
                        $countItems++;
                    }
                }
            } else {
                $result .= '<li><a href="' . site_url($item->url) . '">' . $icon . '<span class="menu-text">' . $item->name . '</span></a></li>';
                $countItems++;
            }
        }
        //Si no se mostró ninguna categoria, informo que no hay opreaciones disponibles.
        if ($countItems == 0)
            $result .= "<li>No hay funciones disponibles.</li>";
 
        $result .= "</ul>";

        echo $result;
    }

    //Si tiene hijos despliega los hijos, sino se muestra el item
    function _desplegate_subitems($pParentItem, $pPadding) {
        $result = "";
        //Verifico si tiene hijos
        $childs = $this->_get_item_childs($pParentItem->id);
        if (count($childs) > 0) {
            //Por cada hijo proceso
            foreach ($childs as $child) {
                $child_item = $this->_desplegate_item($child, $pPadding + $this->padding_increment);
                //si no es una categoria vacia
                if ($child_item != '<ul class="nav">') {
                    $result .= $child_item;
                }
            }
        }
        //no tiene permisos sobre ninguna funcion
        if ($result == '<ul class="nav"></ul>')
            return '';
        else
            return $result;
    }

    //Despliega un elemento (Genera el HTML correcto)
    function _desplegate_item($pChild, $pPadding) {
        $icon = ($pChild->icon != '') ? '<i class="fa ' . $pChild->icon . ' fa-fw"></i>' : '';

        if ($pChild->is_category) {
            //Es MENU _CATEGORY
            if ($this->_have_items($pChild))
                return '<li><a href="#" style="padding-left:' . $pPadding . 'px;">' . $icon . $pChild->name . '<span class="fa arrow"></span></a><ul class="nav">' . $this->_desplegate_subitems($pChild, $pPadding + $this->padding_increment) . '</ul></li>';
            else
                return '<ul class="nav">';
        } else {
            //Es un MENU_ITEM
            return '<li><a href="' . site_url($pChild->url) . '" style="padding-left:' . $pPadding . 'px;">' . $icon . $pChild->name . '</a></li>';
        }
    }

    //devuelve las categorias y los menu items hijos de una MENU CATEGORY
    function _get_item_childs($pId) {
        $sql = "SELECT id, name, icon, parent_id, 1 AS is_category FROM menu_category WHERE parent_id =" . $pId . " ORDER BY sort;";
        $query = $this->db->query($sql);
        if ($query != null)
            $categories = $query->result();
        else
            $categories = array();

        //Usuario identificado
        $user_id = $this->session->userdata('user_id');
        $sql = "SELECT m.id, m.name, m.icon, m.url, 0 AS is_category, m.category
                FROM system_user AS u, profile AS p, profile_has_function AS pf, system_function AS f, menu_item m 
                WHERE u.profile = p.id
                AND p.id = pf.profile_id
                AND pf.function_name = f.name
                AND f.menu_item = m.id
                AND u.username = '$user_id' 
                AND m.category = " . $pId . "
                ORDER BY m.sort ASC;";
        $query = $this->db->query($sql);
        if ($query != null)
            $items = $query->result();
        else
            $items = array();
        return array_merge($items, $categories);
    }

    //Devuelve un array con los MENU_CATEGORY y MENU_ITEM sin padre
    function _withoutparent_items() {
        $sql = "SELECT id, name, icon, parent_id, 1 AS is_category
                FROM menu_category
                WHERE parent_id IS NULL ORDER BY sort;";
        $query = $this->db->query($sql);
        $categories = $query->result();

        //Busco si el usuario tiene acceso a los items sin categoria
        $user_id = $this->session->userdata('user_id');

        $sql = "SELECT i.id, i.name, i.icon, i.url, 0 AS is_category
                FROM system_user AS u, profile AS p, profile_has_function AS pf, system_function AS f, menu_item i
                WHERE p.id = pf.profile_id
                AND pf.function_name = f.name
                AND f.menu_item = i.id
                AND i.category IS NULL
                AND u.profile = p.id
                AND u.username = '" . $user_id . "'
                ORDER BY i.sort;";
        $query = $this->db->query($sql);
        $items = $query->result();
        return array_merge($items, $categories);
    }

    //Determina si una categoria tiene items, dentro ed ella misma o de alguna
    //de sus subcategorias
    function _have_items($category) {
        $count = 0;
        $result = false;
        $childs = $this->_get_item_childs($category->id);
        if (count($childs) > 0) {
            foreach ($childs as $child) {
                if (!$child->is_category) {
                    $count++;
                } else { //si el hijo es una catgoria, pregunto si tiene items dentro
                    $result = $this->_have_items($child) || $result;
                }
            }
        }
        return $result || ($count > 0);
    }

}
