<?php echo $username->generate(); ?>
<?php if (isset($pass_rotarion)) echo $pass_rotation->generate(); ?>

<div class="row">
    <?php echo form_group($name, 6); ?>
    <?php if (isset($current_pass)) echo form_group($current_pass, 6); ?>
    <?php echo form_group($new_pass, 6); ?>
    <?php echo form_group($new_pass2, 6); ?>
</div>