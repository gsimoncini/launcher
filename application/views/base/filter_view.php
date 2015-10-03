<div id="<?php echo $target; ?>" class="panel panel-default filter-panel hide">
    <div class="panel-body">
        <div class="row">
            <?php
            foreach ($fields as $field)
                echo form_group($field['component'], $field['width']);

            foreach ($hidden_fields as $hidden_field)
                echo $hidden_field['component']->generate();
            ?>
            <div class="col-md-<?php echo $buttons['container_width']; ?> filter-buttons">
                <?php echo $buttons['apply_button']->generate(); ?>
                <?php echo $buttons['remove_button']->generate(); ?>
            </div>
        </div>
    </div>
</div>