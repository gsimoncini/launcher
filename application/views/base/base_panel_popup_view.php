<div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title"><?php echo $modal_title; ?></h4></div>
            <div class="modal-body">
                <?php if ($tabs != null) echo $tabs->generate(); ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!-- Carga visualización particular del formulario -->
                        <?php if ($tabs != null) echo '<div class="tab-content">' ?>
                        <?php $this->load->view($form_content_view); ?>
                        <?php if ($tabs != null) echo '</div>' ?>
                    </div> 
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <?php if (isset($form_submit)) echo $form_submit->generate(); ?>
                        <?php if (isset($form_cancel)) echo $form_cancel->generate(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>