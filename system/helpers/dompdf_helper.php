<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function pdf_create($html, $b64 = TRUE, $filename = '') {
    require_once("dompdf/dompdf_config.inc.php");

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    if ($b64) {
        return base64_encode($dompdf->output());
    } else {
        $dompdf->stream($filename . ".pdf");
    }
}

?>