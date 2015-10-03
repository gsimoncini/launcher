<form name="form" method="POST" action="<?php echo $form_url; ?>" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
    <?php if (validation_errors() != null) { ?>
        <!--mensajes al usuario-->
        <div class="alert alert-danger" role="alert">
            <?php echo validation_errors(); ?>
        </div>
    <?php } ?>
    <?php $this->load->view($form_content_view); ?>
</form>