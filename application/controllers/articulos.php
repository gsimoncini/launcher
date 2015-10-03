<?php
require_once APPPATH . 'controllers/base/BaseController.php';
class Articulos extends BaseController {

function Articulos() {
        
        parent::BaseController(-1);
        
    }
	
function index()
{
echo 'Bienvenido a mi primer controlador en CodeIgniter';
}
}
?> 