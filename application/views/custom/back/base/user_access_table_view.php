<form action="<?php echo $filter_button->getUrl(); ?>" method="POST">
    <div id="filter" class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <?php echo form_group($filter_username, 3); ?>
                <?php echo form_group($filter_date_from, 3); ?>
                <?php echo form_group($filter_date_to, 3); ?>
                <div class="col-md-3 filter-buttons">
                    <?php echo $filter_button->generate(); ?>
                    <?php echo $filter_unset->generate(); ?>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="panel panel-default"><?php echo $table; ?></div>