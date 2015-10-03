<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <p class="login-text"><?php echo $this->lang->line('message_password_recovery'); ?></p>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well">
                    <form name="form" method="POST" action="<?php echo $okButton->getUrl(); ?>" enctype="multipart/form-data">
                        <div class="row">
                            <?php echo form_group($user, 12); ?>
                            <?php echo form_group($email, 12); ?>
                        </div>
                        <div class="login-form-action">
                            <?php echo $okButton->generate(); ?>
                            <?php echo $cancelButton->generate(); ?>
                        </div>
                    </form>
                </div>
                <?php if (validation_errors() != null) { ?>
                    <!--mensajes al usuario-->
                    <div class="alert alert-danger" role="alert">
                        <?php echo validation_errors(); ?>
                    </div>
                <?php } ?>
                <?php echo $this->messages->show(); ?>
            </div>
        </div>
        <p class="login-text"><?php echo $this->lang->line('message_account_blocked_contact_the_administrator'); ?></p>
    </div>
</div>