<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <p class="login-text"><?php echo $this->lang->line('message_for_access_to_application'); ?></p>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well">
                    <form name="form" method="POST" action="<?php echo $inButton->getUrl(); ?>" enctype="multipart/form-data">
                        <div class="row">
                            <?php echo form_group($user, 12); ?>
                            <?php echo form_group($pass, 12); ?>
                        </div>
                        <div class="login-form-action"><?php echo $inButton->generate(); ?></div>                        
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
        <p class="login-text"><?php echo $this->lang->line('message_forget_password_first_part') . ' '; ?> <a href="<?php echo site_url('login/password_recovery'); ?>" title="<?php echo $this->lang->line('label_recovery_password'); ?>"><?php echo $this->lang->line('label_here') . ' '; ?></a> <?php echo $this->lang->line('message_forget_password_second_part'); ?></p>
    </div>
</div>
<script>
    $(document).ready(function() {
    });
</script>