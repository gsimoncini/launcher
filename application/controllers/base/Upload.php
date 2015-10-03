<?php

/**
 * Description of Upload
 *
 * @author Mirco
 */
class Upload extends CI_Controller {

//put your code here
    private $upload_dir;
    private $upload_url;
    private $script_url;

    function Upload() {
        parent::__construct();
        $this->upload_dir = FCPATH . 'files/photos/';
        $this->upload_url = base_url() . 'files/photos/';
        $this->script_url = base_url() . 'base/upload/delete';
    }

    function index() {
        require APPPATH . 'controllers/base/UploadHandler.php';
        $upload_handler = new UploadHandler(array(
            'upload_dir' => $this->upload_dir
            , 'script_url' => $this->script_url
            , 'upload_url' => $this->upload_url
            , 'image_versions' => array(
// The empty image version key defines options for the original image:
                '' => array(
// Automatically rotate images based on EXIF meta data:
                    'auto_orient' => true
                ),
                // Uncomment the following to create medium sized images:
                /*
                  'medium' => array(
                  'max_width' => 800,
                  'max_height' => 600
                  ),
                 */
                'thumbnail' => array(
// Uncomment the following to use a defined directory for the thumbnails
// instead of a subdirectory based on the version identifier.
// Make sure that this directory doesn't allow execution of files if you
// don't pose any restrictions on the type of uploaded files, e.g. by
// copying the .htaccess file from the files directory for Apache:
//'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
//'upload_url' => $this->get_full_url().'/thumb/',
// Uncomment the following to force the max
// dimensions and e.g. create square thumbnails:
//'crop' => true,
                    'max_width' => 200,
                    'max_height' => 200
                )
            )
                )
        );
    }

    function delete() {
        $file = $this->input->post('file');
        $success = unlink($this->upload_dir . $file);
        $success = unlink($this->upload_dir . 'thumbnail/' . $file);
        $info = new StdClass;
        $info->sucess = $success;
        $info->path = $this->upload_url . $file;
        $info->file = is_file($this->upload_dir . $file);

        echo json_encode(array($info));
    }

}
