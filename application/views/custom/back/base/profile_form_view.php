<?php
//Clave primaria
if (isset($id))
    echo $id->generate();
?>
<div class="row">
    <?php echo form_group($name, 12, true); ?>
    <?php echo form_group($pass_length, 6, true); ?>
    <?php echo form_group($pass_composition, 6, true); ?>
    <?php echo form_group($pass_rotation, 6, true); ?>
    <?php echo form_group($lock_account, 6, true); ?>
    <?php echo form_group($max_failed_attempts, 6, true); ?>
    <?php echo form_group($is_administrator, 3, true); ?>
    <?php echo form_group($propagate_client_relation, 3, true); ?>
</div>