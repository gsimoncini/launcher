<?php

require_once APPPATH . 'controllers/base/CRUDController.php';

class Settings extends CRUDController {

    var $controllerId = 4;

    public function __construct() {
        parent::__construct($this->controllerId);

        $this->title = 'Configuraci&oacute;n';
        $this->url = 'back/settings';
        $this->viewPath = 'custom/back/base/settings';

        $this->load->model('custom/setting_model', 'Setting_Model');

        $this->accessControl();
    }

    function define_for_component() {
        
    }

    function table($group = null) {
        $this->accessControl('settings_table');

        $strToHex = function($string) {
            if (is_int($string)) {
                $string = (string) $string;
            }
            $hex = '';
            for ($i = 0; $i < strlen($string); $i++) {
                $ord = ord($string[$i]);
                $hexCode = dechex($ord);
                $hex .= substr('0' . $hexCode, -2);
            }
            return strToUpper($hex);
        };

        $this->data['strToHex'] = $strToHex;

//Buscamos las funciones a la que puede acceder este perfil
//$profile_functions = $this->Profile_Model->_functions_of_profile($pId);
//Buscamos todos las settings entity registradas
        $groups = $this->Setting_Model->get_groups();

        $group = $group == null ? 0 : array_search($group, $groups);
        $group = $groups[$group];
        $settings = $this->Setting_Model->get_by_group($group);

        $this->data['groups'] = $groups;
        $this->data['group'] = $group;
        $this->data['settings'] = $settings;

        $pSubmit = new SubmitButton($this->lang->line('action_accept'), site_url('/custom/settings/store'));
        $pSubmit->setIcon('fa-check');

        $pCancel = new Button($this->lang->line('action_cancel'), site_url('/custom/settings/store'));
        $pCancel->setIcon('fa-close');
        $pCancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);"');
        $pCancel->addClass('btn-danger');

        $this->loadFormView($this->viewPath . '/table_view', $this->data, $this->lang->line('subtitle_update_settings'), $pSubmit, $pCancel);
    }

    function store() {
        $this->accessControl('settings_table');
        $hexToStr = function($hex) {
            $string = '';
            for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
                $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
            }
            return $string;
        };

        $data = $this->input->post();
        $group = $data['group'];
        unset($data['group']);
        $new = $data['new'];
        unset($data['new']);

        foreach ($new as $setting) {
            if ($setting['key'] != "" && $setting['val'] != "") {
                $this->Setting_Model->insert_setting($group, $setting['key'], $setting['val']);
            }
        }

        foreach ($data as $key => $val) {
            $key = substr($key, 1);
            $key = $hexToStr($key);
            if ($val['val'] == "") {
                $this->Setting_Model->delete_setting($group, $key);
            } else {
                $this->Setting_Model->update_value($group, $key, $val['val']);
                if ($key != $val['key']) {
                    $this->Setting_Model->update_key($group, $key, $val['key']);
                }
            }
        }
        redirect(site_url() . "/custom/settings/table/" . $group);
    }

}
