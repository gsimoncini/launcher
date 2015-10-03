<!DOCTYPE html>
<html lang="es">
    <?php $this->load->view('/base/html_head_view'); //Levanto la vista del encabezado ?>
    <body onload="setFocus();" class="login-body">
        <?php $this->load->view('base/head_login_view'); //Levanto la vista de encabezado de login ?>
        <div class="container login page-content-footer-fix">
            <h1 class="text-center login-title"><?php echo $view_title; ?></h1>
            <?php $this->load->view($customView); //Levanto la vista configurada ?> 
        </div>
        <?php $this->load->view('base/footer_login_view'); //Levanto la vista de footer de login ?>
    </body>
</html>