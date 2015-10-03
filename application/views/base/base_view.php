<!DOCTYPE html>
<html lang="es">
    <?php $this->load->view('/base/html_head_view'); //Levanto la vista del encabezado ?>
    <body <?php if (isset($on_load_js)) echo 'onload="' . $on_load_js . '"'; ?>>
        <?php $this->load->view('/base/head_view'); //Levanto la vista del encabezado de la página ?>
        <div class="page-container"> 
            <div class="sidebar">
                <?php $this->load->view('/base/menu_view'); //Levanto el menú ?>
            </div>
            <div class="page-content page-content-footer-fix">
                <?php if ($view_title != '') { ?>
                    <div class="page-header">
                        <h5><?php echo $view_title ?>
                            <?php if ($view_operation != '') { ?>
                                <small>
                                    <i class="fa fa-angle-double-right"></i>
                                    <?php echo $view_operation; ?>
                                </small>
                            <?php } ?>
                        </h5>
                    </div>
                <?php } ?>
                <?php echo $this->messages->show(); ?>
                <?php $this->load->view($customView); //Levanto la vista configurada ?>
            </div>
            <?php $this->load->view('/base/footer_view'); //Levanto el pie de página ?>
        </div>
        <div id="popup-container"></div>
    </body>
</html>