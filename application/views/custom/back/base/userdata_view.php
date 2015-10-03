<?php echo $id->generate() ?>
<div class="row">
    <div class="col-md-8">
        <div class="row">
            <?php echo form_group($username, 6); ?>
            <?php echo form_group($profile, 6, true); ?>
        </div>
        <div class="row">
            <?php echo form_group($name, 6, true); ?>
            <?php echo form_group($last_name, 6, true); ?>
        </div>
        <div class="row">
            <?php echo form_group($birth_date, 6); ?>
            <?php echo form_group($doc_type, 3); ?>
            <?php echo form_group($doc_number, 3); ?>
        </div>
        <div class="row">
            <?php echo form_group($email, 6, true); ?>
            <?php echo form_group($email_confirmation, 6, true); ?>
        </div>
        <div class="row">
            <?php echo form_group($phone, 6); ?>
            <?php echo form_group($receive_emails, 6); ?>
        </div>
    </div>
    <div class="col-md-4">
        <?php echo form_group($photo, 12); ?>
    </div>
</div>