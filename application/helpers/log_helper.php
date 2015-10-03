<?php

if (!function_exists('register_log')) {

    function register_log($msg, $path) {
        $file_name = $path .  date("Y.n.j") . '.log';
        $text = date('Y-m-d h:i:s') . ' ::: ' . $msg . PHP_EOL;

        file_put_contents($file_name, $text, FILE_APPEND);
    }

}