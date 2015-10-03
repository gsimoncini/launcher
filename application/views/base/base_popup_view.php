<div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog <?php echo isset($modal_class) ? $modal_class : 'modal-lg'; ?>">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title"><?php echo $modal_title; ?></h4></div>
            <div class="modal-body"><?php $this->load->view($form_content_view); ?></div>
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