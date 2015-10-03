<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('merge_objects')) {

    function merge_objects(&$pObject, $pObjectAdded) {
        $newObject = (object) array_merge((array) $pObject, (array) $pObjectAdded);
        $pObject = $newObject;
    }

}

/* End of file object_helper.php */
/* Location: ./system/helpers/object_helper.php */