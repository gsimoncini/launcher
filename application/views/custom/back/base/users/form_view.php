<div class="tab-pane<?php if ($tab_active == 'info') echo ' active'; ?>" id="info">
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <?php echo form_group($profile, 6, true); ?>
                <?php echo form_group($username, 6, $this->config->item('required_username')); ?>
            </div>
            <div class="row">
                <?php echo form_group($name, 6, $this->config->item('required_name')); ?>
                <?php echo form_group($last_name, 6, $this->config->item('required_last_name')); ?>
            </div>
            <?php if ($id->getValue() == null) { ?>
                <div class="row">
                    <?php echo form_group($password, 6, true); ?>
                    <?php echo form_group($password_confirmation, 6, true); ?>
                </div>
            <?php } ?>
            <div class="row">
                <?php echo form_group($birth_date, 6, $this->config->item('required_birth_date')); ?>
                <?php echo form_group($doc_type, 3, $this->config->item('required_doc_type')); ?>
                <?php echo form_group($doc_number, 3, $this->config->item('required_doc_number')); ?>
            </div>
            <div class="row">
                <?php echo form_group($email, 6, $this->config->item('required_email')); ?>
                <?php if ($this->config->item('required_email')) echo form_group($email_confirmation, 6, $this->config->item('required_email')); ?>
                <?php echo form_group($phone, 6, $this->config->item('required_phone')); ?>
                <?php echo form_group($receive_emails, 6, true); ?>
            </div>
        </div>
        <div class="col-md-4">
            <?php //echo form_group($photo, 12); ?>
        </div>
    </div>
</div>
<div class="tab-pane<?php if ($tab_active == 'client-groups') echo ' active'; ?>" id="client-groups">
    <div class="row"> 
        <?php echo form_group($name_duplicated); ?>
        <?php echo form_group($last_name_duplicated); ?>
    </div>
    <div class="row">
        <?php echo form_group($client_search); ?>
        <?php echo form_group($client_assign_all, 2); ?>
        <?php echo form_group($client_assign_yes, 2); ?>
        <?php echo form_group($client_assign_no, 2); ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="center-table" style="margin-top: 5px;"></div>
        </div>
    </div>
</div>
<div class="tab-pane<?php if ($tab_active == 'rights') echo ' active'; ?>" id="rights">
    <div class="row">
        <?php echo form_group($name_duplicated); ?>
        <?php echo form_group($last_name_duplicated); ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="profile-table" style="margin-top: 5px;"></div>
        </div>
    </div>
</div>

<!-- HIDEN FIELDS -->
<input type="hidden" name="photo" value=""/>
<?php echo $id->generate(); ?>
<?php echo $user_clients->generate(); ?>
<?php echo $user_active->generate(); ?>
<?php echo $multimedia_object_id->generate(); ?>

<script>
    $(document).ready(function() {
        $('#client_group_filter + .btn-group-multiselect').on('hidden.bs.dropdown', function() {
            userController.filterOnClientTable();
        });

        //Actualiza los campos replicados en los demas tabs
        $('#name').on('keyup', function() {
            $('input[name=name_duplicated]').val($('#name').val());
        });
        $('#last_name').on('keyup', function() {
            $('input[name=last_name_duplicated]').val($('#last_name').val());
        });
        $('#client_group_id').on('change', function() {
            $('input[name="client_group_duplicated"]').val($('#client_group_id option:selected').text());
        });

        userController.initializeClientTable('<?php echo isset($allow_assign_client) ? $allow_assign_client : 'true'; ?>');
        userController.initializeProfileTable();
    });
</script>