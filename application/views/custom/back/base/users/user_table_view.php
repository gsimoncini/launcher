<div class="sticker">
    <div class="btn-toolbar" role="toolbar" id="action-buttons">
        <div  class="btn-group">
            <?php echo $add_button; ?>
            <?php echo $edit_button; ?>
            <?php echo $change_password_button; ?>
        </div>
        <div class="btn-group">
            <?php echo $activate_button; ?>
            <?php echo $deactivate_button; ?>
        </div>
        <div class="btn-group">
            <?php echo $view_filter_button->generate(); ?>
            <?php echo $sort_table_button->generate(); ?>
        </div>
    </div>

    <div id="filter" class="panel panel-default hide">
        <div class="panel-body">
            <div class="row"> 
                <?php echo form_group($client_filter); ?>
                <?php echo form_group($role_filter); ?>
            </div>
            <div class="row">
                <?php echo form_group($status_filter); ?>
                <div class="col-md-4 col-md-offset-4 filter-buttons">
                    <?php echo $filter_button->generate(); ?>
                    <?php echo $remove_filter_button->generate(); ?>
                </div>
            </div>
        </div>
    </div>

    <div id="table-toolbar" class="row row-sm">
        <?php echo table_filter(); ?>
        <?php echo table_search($search_filter); ?>
    </div>
</div>

<div id="user-table"></div>

<script>
    $(document).ready(function() {
        userController.initializeTable();

        $('#client_group_filter + .btn-group-multiselect').on('hidden.bs.dropdown', function() {
            var clientGroup = multiselect.removeAllOption('#client_group_filter');

            clientController.allClientsDropdown(clientGroup, '#client_filter');
        });
    });
</script>