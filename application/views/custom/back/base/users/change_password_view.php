<div class="row">
    <div class="col-md-8"> 
        <div class="row">
            <?php echo form_group($username, 6); ?>
            <?php echo form_group($profile, 6); ?>
        </div>
        <div class="row">
            <?php echo form_group($name, 6); ?>
            <?php echo form_group($last_name, 6); ?>
        </div>
        <div class="row"> 
            <?php echo form_group($password_original, 6); ?>
        </div> 
        <div class="row"> 
            <?php echo form_group($password, 6); ?>
            <?php echo form_group($password_confirmation, 6); ?>
        </div>
    </div>
    <div class="col-md-4"> 
        <?php echo form_group($photo, 12); ?>
    </div> 
</div>