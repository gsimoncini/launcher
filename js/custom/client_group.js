clientGroupController = {};

clientGroupController.tableTarget = '#client-group-table';

clientGroupController.initializeTable = function() {
    $(clientGroupController.tableTarget).jtable({
        paging: true,
        selecting: true,
        sorting: true,
        pageSize: 50,
        listActionMode: 1,
        openChildAsAccordion: true,
        messages: table.messages,
        actions: {
            listAction: clientGroupElementsUrl
        },
        fields: {
            id: {
                title: language.line('table_head_id'),
                width: '1%',
                listClass: 'text-center',
                key: true
            },
            html: {
                title: language.line('table_head_group'),
                listClass: 'detailed-cell',
                display: function(data) {
                    return clientGroupController.clientGroupDisplay(data.record);
                }
            }

        }
    });

    clientGroupController.removeFilterTable();
};

clientGroupController.clientGroupDisplay = function(record) {
    var stateTextClass;
    var stateText;
    var description;

    switch (record.status_id) {
        case '1':
            stateTextClass = 'success';
            stateText = language.line('label_active');
            break;
        case '2':
            stateTextClass = 'danger';
            stateText = language.line('label_desactive');
            break;
    }

    if (record.description != null)
        description = record.description;
    else
        description = ' Sin descripci&oacute;n';

    var html = '';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<h4> ' + record.name + ' <small><i class="fa fa-angle-double-right"></i> ' + description + '</small></h4>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<p class="text-right"><span style="font-size: 80%;" class="label label-' + stateTextClass + '">' + stateText + '</span></p>';
    html += '</div>';
    html += '</div>';

    html += '<div class="row">';
    html += '<div class="col-md-12">';
    html += '<p  style="margin-bottom: 0px"> ' + (record.client_group_type_name == null ? language.line('label_without_client_group_type') : record.client_group_type_name) + ' </p>';
    html += '</div>';
    html += '</div>';

    return html;
};

clientGroupController.searchTable = function() {
    var search = $('#search_filter').val();

    $(clientGroupController.tableTarget).jtable('setFilterMethod', function(value) {
        var searchCondition = (
                baseController.inText(value.id, search) ||
                baseController.inText(value.name, search) ||
                baseController.inText(value.description, search));

        return searchCondition;
    });

    $(clientGroupController.tableTarget).jtable('refresh');
};

clientGroupController.filterTable = function() {
    var status = multiselect.removeAllOption('#status_filter');
    var groupType = multiselect.removeAllOption('#group_type_filter');

    var options = {
        status_filter: status,
        group_type_filter: groupType

    };

    $.post(clientGroupApplyFilterUrl, options).done(function() {
        $(clientGroupController.tableTarget).jtable('load', options);

        clientGroupController.showApplyFilters(options);
    });
};

clientGroupController.removeFilterTable = function() {
    var ajaxOptions = {
        url: clientGroupRemoveFilterUrl,
        dataType: 'json'
    };

    $.ajax(ajaxOptions).done(function(response) {
        $('#status_filter').val(response.status_filter);
        $('#group_type_filter').val(response.group_type_filter);

        $('#filter .multi-select').multiselect('refresh');

        $('#search_filter').val(response.search_filter);

        $(clientGroupController.tableTarget).jtable('load');

        baseController.toggleFilterPanel(false);
        clientGroupController.showApplyFilters([]);

    });
};

clientGroupController.showApplyFilters = function(options) {
    var filterText = '';

    if (options.status_filter != null)
        filterText += '<span data-filter="status_filter">' + language.line('label_status') + '</span>';

    if (options.group_type_filter != null && options.group_type_filter.length > 0)
        filterText += '<span data-filter="group_type_filter">' + language.line('label_client_group_type') + '</span>';

    $('.filter-box div.filter span').html(filterText != '' ? filterText : language.line('label_no'));

    $('.filter-box div:last-child span span').each(function() {
        var filter = $(this).attr('data-filter');
        var elements = multiselect.removeAllOption('#' + filter);
        var title = '';

        if ($.isArray(elements) == false)
            elements = [elements];

        $.each(elements, function(index, value) {
            var text = $('#' + filter + ' option[value=' + value + ']').text();

            title += (index == 0 ? '' : ', ') + text;
        });

        $('span[data-filter=' + filter + ']').tooltip({title: title, placement: 'bottom'});
    });
};

clientGroupController.clientGroupsDropdown = function(clientGroupType, target) {
    var ajaxOptions = {
        url: clientGroupElementsForDropdownUrl,
        type: 'post',
        dataType: 'json',
        data: {client_group_type: clientGroupType}
    };

    $.ajax(ajaxOptions).done(function(response) {
        $(target).empty();

        $.each(response, function(index, value) {
            $(target).append('<option value="' + value.id + '">' + value.value + '</option>');
        });

        $(target).multiselect('rebuild');

        if (response.length == 0)
            $(target).multiselect('disable');
        else
            $(target).multiselect('enable');
    });
};

clientGroupController.save = function(pOp) {
    var id = $('#id').val();
    var name = $('#name').val();
    var description = $('#description').val();
    var status_id = $('#status_id').val();
    var client_group_type = $('#client_group_type option:selected').val();


    var ajaxOptions = {
        url: clientGroupSave + '/' + pOp,
        type: 'post',
        dataType: 'json',
        data: {id: id, name: name, description: description, client_group_type: client_group_type, status_id: status_id}
    };

    $.ajax(ajaxOptions).done(function(response) {
        if (response.Result = 'OK')
            $('.message').html(language.line('message_client_group_successfully_save')).addClass('alert alert-success');
        else
            $('.message').html(language.line('message_client_group_error_save')).addClass('alert alert-danger');
        $('#popup').modal('hide');
    }).fail(function(data) {
        $('.modal-footer .row .col-md-6:first-child').html('<span class="text-danger pull-left">' + language.line('message_verify_required_data') + '</span>');
    });
};