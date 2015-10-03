<?php

require_once APPPATH . 'controllers/base/BaseController.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CRUDFactory
 *
 * @author Mirco
 */
class CRUDController extends BaseController {

    var $entity_model = null;
    var $title = 'Entities';
    var $singularName = 'entity';
    var $url = 'custom/clients';
    var $viewPath = 'custom/back/clients';
    var $action;

    public function __construct($controllerId) {
        parent::__construct($controllerId);
    }

    public function table() {

        //Botones de accion
        //@TODO: Actions Buttons Factory
        //Filtro
        //@TODO: Filters Factory

        return $this->load->view($this->viewPath . '/table_view', $this->data, TRUE);
    }

    public function elements() {
        $filters = $this->get_filters();
        $elements = $this->entity_model->all_elements($filters);

        $response['Result'] = 'OK';
        $response['Records'] = $elements;
        $response['TotalRecordCount'] = count($elements);

        echo json_encode($response);
    }

    public function get_filters() {
        $filters = $this->entity_model->get_filters();

        if ($filters == null)
            $filters = $this->entity_model->get_default_filters();

        return $filters;
    }

    public function apply_filter() {
        $this->entity_model->set_filters($this->input->post());
    }

    public function remove_filter() {
        $this->entity_model->unset_filters();

        $filters = $this->entity_model->get_default_filters();
        $this->entity_model->set_filters($filters);

        echo json_encode($filters);
    }

    public function _generate_form($pObject, $pOperation, $pSubtitle, $pStatic = false, $pRedirect = null) {
        $this->define_form_components($pObject, $pStatic);

        $submit_url = $this->url . $pOperation . ($pRedirect == null ? '' : '/' . $pRedirect);
        $cancel_url = $pRedirect == null ? $this->url . '/table' : str_replace(':', '/', $pRedirect);

        $submit = new SubmitButton($this->lang->line('action_accept'), site_url($submit_url));
        $submit->setIcon('fa-check');

        $cancel = new Button($this->lang->line('action_cancel'), site_url($cancel_url));
        $cancel->setIcon('fa-close');
        $cancel->setAttributes('onclick="baseController.confirmFormCancel(event, this);" ');
        $cancel->addClass('btn-danger');

        $tabs = $this->get_tabs();

        $this->loadFormView($this->viewPath . '/form_view', $this->data, $pSubtitle, $submit, $cancel, $tabs);
    }

    public function add($pRedirect = null, $pObject = null) {
        $this->action = 'add';

        $this->_generate_form($pObject, '/save/1', $this->lang->line('subtitle_new') . ' ' . $this->singularName, false, $pRedirect);
    }

    public function edit($pObject, $pRedirect = null) {
        $this->action = 'edit';

        $this->_generate_form($pObject, '/save/2', $this->lang->line('subtitle_edit') . ' ' . $this->singularName, false, $pRedirect);
    }

    public function copy($pObject) {
        $this->action = 'copy';

        $this->_generate_form($pObject, '/save/3', $this->lang->line('subtitle_copy') . ' ' . $this->singularName);
    }

    function change_status($pObject, $pStatusId, $pTitle = null) {
        $this->action = 'change_status';

        if ($pTitle == null)
            $pTitle = $pStatusId == 1 ? $this->lang->line('subtitle_activate') : $this->lang->line('subtitle_deactivate');

        $this->_generate_form($pObject, '/save_status', $pTitle . ' ' . $this->singularName, true);
    }

    public function save($pOperation, $pRedirect = null) {
        $this->set_validation_rules();

        if ($this->form_validation->run() == false) {
            switch ($pOperation) {
                case 1:
                    $this->add($pRedirect);
                    break;
                case 2:
                    $this->edit($this->input->post('id'), $pRedirect);
                    break;
                case 3:
                    $this->copy($this->input->post('id'));
                    break;
            }
        } else {
            $object = (object) $this->input->post();

            if (isset($object->shipping_date)) {
                $object->fulfillment_date = $object->shipping_date;
            }

            if ($pOperation == 3)
                $object->id = null;

            if ($this->entity_model->save($object)) {
                if ($this->input->is_ajax_request()) {
                    $response['Result'] = 'OK';
                    $response['message'] = $this->lang->line('message_client_group_successfully_save');
                    echo json_encode($response);
                    return null;
                } else {
                    if ($pOperation == 2)
                        $this->messages->set('success', $this->lang->line('message_edit_first_part') . ' ' . $this->singularName . ' ' . $this->lang->line('message_edit_second_part'));
                    else
                        $this->messages->set('success', $this->lang->line('message_store_first_part') . ' ' . $this->singularName . ' ' . $this->lang->line('message_store_second_part'));
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    $response['Result'] = 'ERROR';
                    $response['message'] = $this->lang->line('message_client_group_error_save');
                    echo json_encode($response);
                    return null;
                } else
                    $this->messages->set('warning', $this->lang->line('message_save_error') . ' ' . $this->singularName . '.');
            }

            redirect($pRedirect == null ? $this->url . '/table' : str_replace(':', '/', $pRedirect));
        }
    }

    public function save_status($pRedirect = null) {
        if ($pRedirect == null)
            $pRedirect = $this->url . '/table';

        $object = (object) $this->input->post();
        if ($this->entity_model->save_status($object)) {
            $this->messages->set('success', $this->lang->line('message_update_status'));
        } else
            $this->messages->set('warning', $this->lang->line('message_update_error'));
        redirect($pRedirect);
    }

    public function set_validation_rules() {
        //@TODO: ValidationBuilder
    }

    //Devuelve los tabs a conformar
    function get_tabs() {
        return null;
    }

    function get_item_value($pObject, $attr, $default = '') {
        try {
            return set_value($attr, $pObject && isset($pObject->$attr) ? $pObject->$attr : $default);
        } catch (Error $e) {
            return $default;
        }
    }

}
