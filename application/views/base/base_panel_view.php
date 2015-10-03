<?php if ($tabs != null) echo $tabs->generate(); ?>
<div class="panel panel-default">
    <div class="panel-body">
        <!-- Carga visualización particular del formulario -->
        <?php if ($tabs != null) echo '<div class="tab-content">' ?>
        <?php $this->load->view($form_content_panel_view); ?>
        <?php if ($tabs != null) echo '</div>' ?>
    </div>
    <div class="panel-footer">
        <div class="form-actions">
            <?php
            if (isset($form_submit))
                echo $form_submit->generate();

            if (isset($form_cancel))
                echo $form_cancel->generate();
            ?>  
        </div>
    </div>
</div>