<?php

class Language extends CI_Controller {

    function load() {
        echo json_encode($this->lang->language);
    }

}
