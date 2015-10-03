<?php

class Multimedia_Model extends BaseModel {

    function Multimedia_Model() {
        parent::__construct();

        $this->initialize('multimedia_object');
    }

    function get_by_id($pId) {
        return parent::get($pId);
    }

    function get_file_name($pUrl) {
        $url_segments = explode('/', $pUrl);
        $file_name = end($url_segments);

        $file_name_segments = explode('.', $file_name);
        $file_format = count($file_name_segments) > 1 ? end($file_name_segments) : null;

        return array('file_name' => $file_name, 'file_format' => $file_format);
    }

    function get_relative_url($pUrl) {
        $base_url = base_url();
        $base_url_count = strlen($base_url);

        $relative_url = substr($pUrl, $base_url_count);

        return $relative_url;
    }

    function save($pUrl, $pId = null) {
        $relative_url = $this->get_relative_url($pUrl);
        $info_url = $this->get_file_name($pUrl);

        $multimedia = new stdClass();

        $multimedia->id = $pId;
        $multimedia->url = $relative_url;
        $multimedia->name = $info_url['file_name'];
        $multimedia->format = $info_url['file_format'];

        return parent::save($multimedia);
    }

}
