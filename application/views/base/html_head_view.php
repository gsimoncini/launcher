<head>
    <!-- Definimos el charset y otros metas -->
    <meta charset="<?php echo $this->config->item('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->config->item('software_title'); ?></title>

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/custom-bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap.multiselect/css/bootstrap-multiselect.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap.datepicker/css/datepicker.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap.datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap.toggle/css/bootstrap-toggle.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap.select/css/bootstrap-select.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.ui/css/ui-lightness/jquery-ui.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.jtable/themes/metro/' . $this->config->item('jtable_theme') . '/jtable.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.metisMenu/css/jquery.metisMenu.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.iCheck/skins/all.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.selectize/css/selectize.bootstrap3.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.owl-carousel/owl.carousel.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jquery.owl-carousel/owl.theme.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.min.css'); ?>">


    <link rel="stylesheet" href="<?php echo base_url('css/custom.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/' . $this->config->item('theme_name') . '.css'); ?>">

    <!-- Icono -->
    <?php echo link_tag(array('href' => base_url() . $this->config->item('icon'), 'rel' => 'icon', 'type' => 'image/x-icon')); ?>

    <!-- Plugins Javascript -->
    <script src="<?php echo base_url('plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery/jquery.ui.widget.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.ui/js/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.cookie/jquery.cookie.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.attrchange/jquery.attrchange.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/jquery.jtable.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.autosavestate.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.colorchange.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.livesort.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.livedata.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.footer.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.livefilter.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.various.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.gridview.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.jtable/extensions/jquery.jtable.lookandfeel.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.metisMenu/js/jquery.metisMenu.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.fileupload/jquery.iframe-transport.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.fileupload/jquery.fileupload.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.birthdaypicker/bday-picker.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.iCheck/icheck.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.inputmask/inputmask/jquery.inputmask.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.inputmask/inputmask/jquery.inputmask.numeric.extensions.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.sticky/jquery.sticky.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.selectize/js/standalone/selectize.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.owl-carousel/owl.carousel.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/jquery.minijs/miniSlider.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/moment/moment-with-locales.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/moment/moment-timezone-with-data.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/dateFormat/dateFormat.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.bootbox/bootbox.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.multiselect/js/bootstrap-multiselect.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.datepicker/js/bootstrap-datepicker.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.toggle/js/bootstrap-toggle.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.select/js/bootstrap-select.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.typeahead/bootstrap3-typeahead.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap.filestyle/bootstrap-filestyle.min.js'); ?>"></script>



    <!-- Componentes -->
    <script src="<?php echo base_url('js/custom/components/language.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/filter.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/bootbox.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/multiselect.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/table.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/selectize.js'); ?>"></script>
    <script src="<?php echo base_url('js/custom/components/quantity_product_box.js'); ?>"></script>
 

    <script src="<?php echo base_url('js/custom/base.js'); ?>"></script>

    <script>
        baseUrl = '<?php echo base_url(); ?>';
        siteUrl = '<?php echo site_url(); ?>';

        var globalFilterBox;

        baseLoadLanguageUrl = '<?php echo site_url('custom/language/load'); ?>';

        clientElementsUrl = '<?php echo site_url('custom/clients/elements'); ?>';
        clientElementsForDropdownUrl = '<?php echo site_url('custom/clients/elements_for_dropdown'); ?>';
        clientAllElementsForDropdownUrl = '<?php echo site_url('custom/clients/all_elements_for_dropdown'); ?>';
        clientApplyFilterUrl = '<?php echo site_url('custom/clients/apply_filter'); ?>';
        clientRemoveFilterUrl = '<?php echo site_url('custom/clients/remove_filter'); ?>';
        clientGet = '<?php echo site_url('custom/clients/get'); ?>';
        clientTablePopupUrl = '<?php echo site_url('custom/clients/open_table_popup'); ?>';

        clientGroupTypeByClientGroupUrl = '<?php echo site_url('custom/client_group_type/client_group_by_type'); ?>';

        groupsForPopupUrl = '<?php echo site_url('custom/client_group/add'); ?>';

        massiveOrdersElementsUrl = '<?php echo site_url('custom/massive_orders/elements'); ?>';
        massiveOrdersElementsToFormUrl = '<?php echo site_url('custom/massive_orders/elements_to_form'); ?>';
        massiveOrdersApplyFilterUrl = '<?php echo site_url('custom/massive_orders/apply_filter'); ?>';
        massiveOrdersRemoveFilterUrl = '<?php echo site_url('custom/massive_orders/remove_filter'); ?>';
        massiveOrdersGetProductUrl = '<?php echo site_url('custom/massive_orders/get_product'); ?>';
        massiveOrdersProductsForDropdownUrl = '<?php echo site_url('custom/massive_orders/products_for_dropdown'); ?>';
        massiveOrdersApplyFormFilterUrl = '<?php echo site_url('custom/massive_orders/apply_filter'); ?>';
        massiveOrdersRemoveFormFilterUrl = '<?php echo site_url('custom/massive_orders/remove_filter'); ?>';
        massiveOrdersGetTotalRequiredUrl = '<?php echo site_url('custom/massive_orders/get_total_required'); ?>';
        massiveOrdersProductsByCatalogUrl = '<?php echo site_url('custom/massive_orders/products_by_catalog'); ?>';
        massiveOrdersCrosstabByClientUrl = '<?php echo site_url('custom/massive_orders/crosstab/cross_product_by_client'); ?>';

        purchaseOrderElementsUrl = '<?php echo site_url('custom/purchase_orders/elements'); ?>';
        purchaseOrderByCliensUrl = '<?php echo site_url('custom/purchase_orders/get_by_client'); ?>';
        purchaseOrderProductElementUrl = '<?php echo site_url('custom/purchase_orders/product_elements'); ?>';
        purchaseOrderApplyFilterUrl = '<?php echo site_url('custom/purchase_orders/apply_filter'); ?>';
        purchaseOrderRemoveFilterUrl = '<?php echo site_url('custom/purchase_orders/remove_filter'); ?>';
        purchaseOrderViewUrl = '<?php echo site_url('custom/purchase_orders/view'); ?>';
        purchaseOrderViewFormat1Url = '<?php echo site_url('custom/purchase_orders/view_order_format1'); ?>';
        purchaseOrderChangeStatusUrl = '<?php echo site_url('custom/purchase_orders/multiple_change_status'); ?>';
        purchaseOrderGetByProductsUrl = '<?php echo site_url('custom/purchase_orders/get_orders_by_products'); ?>';
        purchaseOrderShippingInfoPopupUrl = '<?php echo site_url('custom/purchase_orders/shipping_indications_popup'); ?>';


        productElementsUrl = '<?php echo site_url('custom/products/elements'); ?>';
        productElementsForConsoleUrl = '<?php echo site_url('custom/products/elements_for_console'); ?>';
        productApplyFilterUrl = '<?php echo site_url('custom/products/apply_filter'); ?>';
        productRemoveFilterUrl = '<?php echo site_url('custom/products/remove_filter'); ?>';
        productGetByPurchaseOrderUrl = '<?php echo site_url('custom/products/get_by_purchase_order'); ?>';
        productGetBySupplyOrderUrl = '<?php echo site_url('custom/products/get_by_supply_order'); ?>';
        productGetByProductCatalogUrl = '<?php echo site_url('custom/products/get_by_product_catalog'); ?>';
        productGetByProductTemplateUrl = '<?php echo site_url('custom/products/get_by_product_template'); ?>';
        productGetProductCatalogsUrl = '<?php echo site_url('custom/products/get_product_catalogs'); ?>';
        productGetProductIdentityListUrl = '<?php echo site_url('custom/products/get_product_identity_list'); ?>';
        productElementsForDropdownUrl = '<?php echo site_url('custom/products/elements_for_dropdown'); ?>';

        productIdentitySaveUrl = '<?php echo site_url('custom/product_identity/save'); ?>';
        productIdentityGetEntityUrl = '<?php echo site_url('custom/product_identity/get_entity'); ?>';
        productIdentityGetRangeInfoUrl = '<?php echo site_url('custom/product_identity/get_range_info'); ?>';
        productIdentityGetByProductUrl = '<?php echo site_url('custom/product_identity/get_by_product'); ?>';

        clientCatalogsUrl = '<?php echo site_url('custom/clients/catalogs_elements'); ?>';

        clientGroupElementsUrl = '<?php echo site_url('custom/client_group/elements'); ?>';
        clientGroupApplyFilterUrl = '<?php echo site_url('custom/client_group/apply_filter'); ?>';
        clientGroupRemoveFilterUrl = '<?php echo site_url('custom/client_group/remove_filter'); ?>';
        clientGroupElementsForDropdownUrl = '<?php echo site_url('custom/client_group/elements_for_dropdown'); ?>';
        clientGroupSave = '<?php echo site_url('custom/client_group/save'); ?>';

        clientGroupTypeElementsUrl = '<?php echo site_url('custom/clients/client_group_type_elements'); ?>';
        clientsGroupTypeElementsUrl = '<?php echo site_url('custom/client_group_type/elements'); ?>';
        clientGroupTypeApplyFilterUrl = '<?php echo site_url('custom/client_group_type/apply_filter'); ?>';
        clientGroupTypeRemoveFilterUrl = '<?php echo site_url('custom/client_group_type/remove_filter'); ?>';
        clientGroupTypeRemoveClientGroup = '<?php echo site_url('custom/client_group_type/unassign_client_group'); ?>';

        shippingCompanyTypeElementsUrl = '<?php echo site_url('custom/shipping_company_type/elements'); ?>';
        shippingCompanyTypeApplyFilterUrl = '<?php echo site_url('custom/shipping_company_type/apply_filter'); ?>';
        shippingCompanyTypeRemoveFilterUrl = '<?php echo site_url('custom/shipping_company_type/remove_filter'); ?>';
        shippingCompanyAddressData = '<?php echo site_url('custom/shipping_company/address_elements'); ?>';

        supplierElementsUrl = '<?php echo site_url('custom/supplier/elements'); ?>';
        supplierApplyFilterUrl = '<?php echo site_url('custom/supplier/apply_filter'); ?>';
        supplierRemoveFilterUrl = '<?php echo site_url('custom/supplier/remove_filter'); ?>';

        warehousesElementsUrl = '<?php echo site_url('custom/warehouses/elements'); ?>';
        warehouseApplyFilterUrl = '<?php echo site_url('custom/warehouses/apply_filter'); ?>';
        warehouseRemoveFilterUrl = '<?php echo site_url('custom/warehouses/remove_filter'); ?>';

        shippingCompanyElementsUrl = '<?php echo site_url('custom/shipping_company/elements'); ?>';
        shippingCompanyApplyFilterUrl = '<?php echo site_url('custom/shipping_company/apply_filter'); ?>';
        shippingCompanyRemoveFilterUrl = '<?php echo site_url('custom/shipping_company/remove_filter'); ?>';

        productCatalogElementsUrl = '<?php echo site_url('custom/product_catalog/elements'); ?>';
        productCatalogElementsForDropdownUrl = '<?php echo site_url('custom/product_catalog/elements_for_dropdown'); ?>';
        productCatalogProductsUrl = '<?php echo site_url('custom/product_catalog/products_elements'); ?>';
        productCatalogApplyFilterUrl = '<?php echo site_url('custom/product_catalog/apply_filter'); ?>';
        productCatalogRemoveFilterUrl = '<?php echo site_url('custom/product_catalog/remove_filter'); ?>';

        entryOrderElementsUrl = '<?php echo site_url('custom/entry_orders/elements'); ?>';
        entryOrderProductElementsUrl = '<?php echo site_url('custom/entry_orders/products_elements'); ?>';
        entryOrderProductsUrl = '<?php echo site_url('custom/entry_orders/products_by_order'); ?>';
        entryOrderApplyFilterUrl = '<?php echo site_url('custom/entry_orders/apply_filter'); ?>';
        entryOrderRemoveFilterUrl = '<?php echo site_url('custom/entry_orders/remove_filter'); ?>';
        entryOrderGetSupplierUrl = '<?php echo site_url('custom/entry_orders/get_supplier'); ?>';
        entryOrderGetWarehouseUrl = '<?php echo site_url('custom/entry_orders/get_warehouse'); ?>';
        entryOrderSupplyOrderTablePopupUrl = '<?php echo site_url('custom/entry_orders/open_supply_order_table_popup'); ?>';
        entryOrderViewUrl = '<?php echo site_url('custom/entry_orders/view'); ?>';

        supplyOrderElementsUrl = '<?php echo site_url('custom/supply_orders/elements'); ?>';
        supplyOrderProductElementsUrl = '<?php echo site_url('custom/supply_orders/products_elements'); ?>';
        supplyOrderProductsUrl = '<?php echo site_url('custom/supply_orders/products_by_order'); ?>';
        supplyOrderApplyFilterUrl = '<?php echo site_url('custom/supply_orders/apply_filter'); ?>';
        supplyOrderRemoveFilterUrl = '<?php echo site_url('custom/supply_orders/remove_filter'); ?>';
        supplyOrderGetSupplierUrl = '<?php echo site_url('custom/supply_orders/get_supplier'); ?>';
        supplyOrderGetWarehouseUrl = '<?php echo site_url('custom/supply_orders/get_warehouse'); ?>';
        supplyOrderViewUrl = '<?php echo site_url('custom/supply_orders/view'); ?>';

        transferOrderGetClientUrl = '<?php echo site_url('custom/transfer_orders/get_client'); ?>';
        transferOrderProductElementsUrl = '<?php echo site_url('custom/transfer_orders/products_elements'); ?>';
        transferOrderClientTablePopupUrl = '<?php echo site_url('custom/transfer_orders/client_table_popup'); ?>';

        userElementsUrl = '<?php echo site_url('back/users/elements'); ?>';
        userClientsUrl = '<?php echo site_url('back/users/clients_elements'); ?>';
        userApplyFilterUrl = '<?php echo site_url('back/users/apply_filter'); ?>';
        userRemoveFilterUrl = '<?php echo site_url('back/users/remove_filter'); ?>';
        profileFunctionsUrl = '<?php echo site_url('back/profiles/profile_permission'); ?>';

        processElementsUrl = '<?php echo site_url('custom/process/elements'); ?>';
        processGetOrdersUrl = '<?php echo site_url('custom/process/get_orders'); ?>';
        processExecuteUrl = '<?php echo site_url('custom/process/execute'); ?>';
        processExecuteDirectUrl = '<?php echo site_url('custom/process/execute/true'); ?>';
        processCancelExecuteUrl = '<?php echo site_url('custom/process/cancel_execute'); ?>';
        processDownloadUrl = '<?php echo site_url('custom/process/download'); ?>';
        processApplyFilterUrl = '<?php echo site_url('custom/process/apply_filter'); ?>';
        processRemoveFilterUrl = '<?php echo site_url('custom/process/remove_filter'); ?>';
        processOrderViewUrl = '<?php echo site_url('custom/process/view'); ?>';
        processOrderGetStatusInfoUrl = '<?php echo site_url('custom/process/get_status_info'); ?>';

        adequacyOrderElementsUrl = '<?php echo site_url('custom/adequacy_orders/elements'); ?>';
        adequacyOrderApplyFilterUrl = '<?php echo site_url('custom/adequacy_orders/apply_filter'); ?>';
        adequacyOrderRemoveFilterUrl = '<?php echo site_url('custom/adequacy_orders/remove_filter'); ?>';
        adequacyOrderGetByClient = '<?php echo site_url('custom/adequacy_orders/get_by_client'); ?>';

        prepareOrderElementsUrl = '<?php echo site_url('custom/prepare_orders/elements'); ?>';
        prepareOrderApplyFilterUrl = '<?php echo site_url('custom/prepare_orders/apply_filter'); ?>';
        prepareOrderRemoveFilterUrl = '<?php echo site_url('custom/prepare_orders/remove_filter'); ?>';
        prepareOrderGetByClient = '<?php echo site_url('custom/prepare_orders/get_by_client'); ?>';

        supplyClientsElementsUrl = '<?php echo site_url('custom/supply_clients/elements'); ?>';
        supplyClientsApplyFilterUrl = '<?php echo site_url('custom/supply_clients/apply_filter'); ?>';
        supplyClientsGetByClient = '<?php echo site_url('custom/supply_clients/get_by_client'); ?>';

        highVolumeOrdersElementsUrl = '<?php echo site_url('custom/high_volume_orders/elements'); ?>';
        highVolumeOrdersElementsDetailUrl = '<?php echo site_url('custom/high_volume_orders/elements_detail'); ?>';
        highVolumeOrdersElementsByIdsUrl = '<?php echo site_url('custom/high_volume_orders/elements_by_ids'); ?>';
        highVolumeOrdersGetProductCatalogByClient = '<?php echo site_url('custom/high_volume_orders/get_product_catalog_by_client'); ?>';
        highVolumeOrdersApplyFilterUrl = '<?php echo site_url('custom/high_volume_orders/apply_filter'); ?>';
        highVolumeOrdersGetMultiselectStatusByActionCodeUrl = '<?php echo site_url('/custom/high_volume_orders/get_multi_select_status'); ?>';
        highVolumeOrdersGetWarehouseUrl = '<?php echo site_url('/custom/high_volume_orders/get_warehouse'); ?>';
        highVolumeOrdersValidationForm = '<?php echo site_url('/custom/high_volume_orders/validate_form'); ?>';
        highVolumeOrdersCheckValidPrecondition = '<?php echo site_url('/custom/high_volume_orders/precondition_for_actions'); ?>';

        actionsElementsUrl = '<?php echo site_url('/custom/actions/elements'); ?>';
        actionsApplyFilterUrl = '<?php echo site_url('custom/actions/apply_filter'); ?>';
        actionsValidationForm = '<?php echo site_url('/custom/actions/validate_form'); ?>';
        actionsPopupView = '<?php echo site_url('/custom/actions/view_action_popup'); ?>';
        actionsPopupViewAssociatedProduct = '<?php echo site_url('/custom/actions/view_associate_product_to_purchase_order_popup'); ?>';
        actionsPopupViewTracking = '<?php echo site_url('/custom/actions/view_tracking_purchase_order_popup'); ?>';

        orderSelectionElementsUrl = '<?php echo site_url('custom/order_selection/elements'); ?>';
        orderSelectionOrdersPopupUrl = '<?php echo site_url('custom/order_selection/orders_popup'); ?>';
        orderSelectionApplyFilterUrl = '<?php echo site_url('custom/order_selection/apply_filter'); ?>';
        orderSelectionOrderElementsUrl = '<?php echo site_url('custom/order_selection/order_elements'); ?>';
        orderSelectionOrderElementsDetailUrl = '<?php echo site_url('custom/order_selection/product_elements'); ?>';

        IOFileElementsUrl = '<?php echo site_url('custom/io_file/elements'); ?>';
        IOFileProcessElementsUrl = '<?php echo site_url('custom/io_file/process_elements'); ?>';
        IOFileProcessFileUrl = '<?php echo site_url('custom/io_file/confirm_process_file'); ?>';
        IOFileApplyFilterUrl = '<?php echo site_url('custom/io_file/apply_filter'); ?>';

        quantityProductBoxUrl = '<?php echo site_url('custom/products/data_for_quantity_product_box'); ?>';

        ordersByStatusElementsUrl = '<?php echo site_url('custom/orders_by_status/elements'); ?>';
        ordersByStatusApplyFilterUrl = '<?php echo site_url('custom/orders_by_status/apply_filter'); ?>';
        ordersByStatusGetProductCatalogByClient = '<?php echo site_url('custom/orders_by_status/get_product_catalog_by_client'); ?>';

        $(document).ready(function() {
            language.load();


            /* Control de session de usuario  */
<?php if ($this->userId != null) { ?>
                //baseController.userSessionControl('<?php echo site_url('back/home/ajax_session_expire'); ?>', 60000);
<?php } ?>


            table.setDefaultMessages();

            $('#side-menu').metisMenu();

            $('.menu-toggle').click(function() {
                baseController.toggleMenu();
            });

            if ($.cookie('sidebar-hide') == 'true')
                baseController.toggleMenu();

            multiselect.initialize();

            if ($('form').length > 0)
                $('.sidebar a, .navbar a').not('a[href="#"]').click(function(event) {
                    baseController.confirmFormCancel(event, this);
                });

            /* Sticky */
            $('.sticker').sticky({
                topSpacing: 0,
                getWidthFrom: '.page-content',
                responsiveWidth: true
            });

            $('.sticker-form').sticky({
                topSpacing: 0,
                getWidthFrom: '.tab-content',
                responsiveWidth: true

            });

            $('.sticky-wrapper').css('height', 'initial');

            //Fix bounce sticky
            $('.sticker').on('sticky-start', function() {
                var stickyWrapHeight = $(this).height();
                $('.sticky-wrapper').css('padding-top', stickyWrapHeight + 'px');
                $('.page-content').css('margin-bottom', (stickyWrapHeight + 50) + 'px');
                $('.footer').css('bottom', '-' + (stickyWrapHeight + 50) + 'px');
            });

            $('.sticker').on('sticky-end', function() {
                $('.sticky-wrapper').css('padding-top', '0px');
            });

            //End fix bounce sticky
            $('.sticker, .sticker-form').on('sticky-end', function() {
                $('.sticky-wrapper').css('height', 'initial');
            });
            /* Fin Sticky */

            $('input[type="password"]').on('keypress', function(e) {
                baseController.capsLockEventHandler(e, function() {
                    $('.block-may-msg').remove();
                    $('.login-form-action').append('<span class="block-may-msg alert alert-warning pull-left"><span class="fa fa-warning"></span>' + language.line('message_uppercase_active') + '</span>');
                }, function() {
                    $('.block-may-msg').remove();
                });
            });

<?php if ($this->config->item('transversal_filters')) { ?>
                //Box para Disney
                globalFilterBox = new GlobalFilterBox(siteUrl + '/back/home/get_filter');
                globalFilterBox.defaultImage = baseUrl + 'img/base/no-img.png';
                globalFilterBox.initialize('.global-filter-selected');

                $('#filter .btn-primary').click(function() {
                    setTimeout(function() {
                        globalFilterBox.initialize('.global-filter-selected');
                    }, 750);
                });
<?php } ?>
        });

        $.fn.serializeObject = function() {
            var object = {};
            var array = this.serializeArray();

            $.each(array, function() {
                if (object[this.name]) {
                    if (!object[this.name].push)
                        object[this.name] = [object[this.name]];
                    object[this.name].push(this.value || '');
                } else
                    object[this.name] = this.value || '';
            });

            return object;
        };
    </script>

    <!-- JavaScript extra del controlador -->
    <?php if (isset($extra_js) && $extra_js == !null) { ?><script><?php echo $extra_js; ?></script><?php } ?>
    <!-- Carga dinamicamente archivos de Javascript -->
    <?php
    if (isset($extra_js_array) && $extra_js_array == !null) {
        foreach ($extra_js_array AS $filename) {
            ?>
            <script src="<?php echo $filename; ?>?ts<?php echo date('Ymd'); ?>"></script>
        <?php } ?>
    <?php } ?>
</head>
