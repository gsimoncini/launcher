clientGroupTypeController = {};

clientGroupTypeController.tableTarget = '#client-group-type-table';
clientGroupTypeController.popupGroup;

clientGroupTypeController.initializeTable = function() {
    $(clientGroupTypeController.tableTarget).jtable({
        paging: true,
        selecting: true,
        sorting: true,
        pageSize: 50,
        listActionMode: 1,
        openChildAsAccordion: true,
        messages: table.messages,
        actions: {
            listAction: clientsGroupTypeElementsUrl
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
                    return clientGroupTypeController.clientGroupTypeDisplay(data.record);
                }
            },
            actions: {
                width: '1%',
                listClass: 'text-center',
                visibility: 'fixed',
                sorting: false,
                display: function(data) {
                    return '<button class="btn btn-link" onclick="clientGroupTypeController.toggleClientGroupTable(this, ' + data.record.id + ');"><i class="fa fa-plus"></i></button>';
                }
            }
        }
    });

    clientGroupTypeController.removeFilterTable();
};

clientGroupTypeController.clientGroupTypeDisplay = function(record) {
    var stateTextClass;
    var stateText;

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

    var html = '';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<h4> ' + record.description + ' </h4>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<p class="text-right"><span style="font-size: 80%;" class="label label-' + stateTextClass + '">' + stateText + '</span></p>';
    html += '</div>';
    html += '</div>';

    return html;
};

clientGroupTypeController.searchTable = function() {
    var search = $('#search_filter').val();

    $(clientGroupTypeController.tableTarget).jtable('setFilterMethod', function(value) {
        var searchCondition = (
                baseController.inText(value.id, search) ||
                baseController.inText(value.description, search));

        return searchCondition;
    });

    $(clientGroupTypeController.tableTarget).jtable('refresh');
};

clientGroupTypeController.filterTable = function() {
    var status = multiselect.removeAllOption('#status_filter');

    var options = {
        status_filter: status

    };

    $.post(clientGroupTypeApplyFilterUrl, options).done(function() {
        $(clientGroupTypeController.tableTarget).jtable('load', options);

        clientGroupTypeController.showApplyFilters(options);
    });
};

clientGroupTypeController.removeFilterTable = function() {
    var ajaxOptions = {
        url: clientGroupTypeRemoveFilterUrl,
        dataType: 'json'
    };

    $.ajax(ajaxOptions).done(function(response) {
        $('#status_filter').val(response.status_filter);

        $('#filter .multi-select').multiselect('refresh');

        $('#search_filter').val(response.search_filter);

        $(clientGroupTypeController.tableTarget).jtable('load');

        baseController.toggleFilterPanel(false);
        clientGroupTypeController.showApplyFilters([]);

    });
};

clientGroupTypeController.showApplyFilters = function(options) {
    var filterText = '';

    if (options.status_filter != null)
        filterText += '<span data-filter="status_filter">' + language.line('label_status') + '</span>';

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

//sub tabla con los grupos
clientGroupTypeController.toggleClientGroupTable = function(button, id) {
    var icon = $(button).children();

    if (icon.hasClass('fa-plus')) {
        $(clientGroupTypeController.tableTarget).find('button i').each(function() {
            $(this).removeClass('fa-minus');
            $(this).addClass('fa-plus');
        });

        icon.removeClass('fa-plus');
        icon.addClass('fa-minus');

        clientGroupTypeController.initializeClientGroupTable(button, id);
    } else {
        icon.removeClass('fa-minus');
        icon.addClass('fa-plus');

        clientGroupTypeController.closeClientGroupTable(button);
    }
};

clientGroupTypeController.closeClientGroupTable = function(button) {
    var row = $(button).closest('tr');

    $(clientGroupTypeController.tableTarget).jtable('closeChildTable', row);
};

clientGroupTypeController.initializeClientGroupTable = function(button, id) {
    var row = $(button).closest('tr');

    $(clientGroupTypeController.tableTarget).jtable('openChildTable', row, {
        sorting: true,
        messages: table.messages,
        selecting: true,
        actions: {
            listAction: clientGroupTypeByClientGroupUrl
        },
        fields: {
            id: {
                list: false,
                key: true
            },
            html: {
                title: language.line('table_head_group'),
                listClass: 'detailed-cell detailed-cell-sm',
                display: function(data) {
                    return clientGroupController.clientGroupDisplay(data.record);
                }
            }
        }
    }, function(data) {
        data.childTable.jtable('load', {id: id});
    });
};


clientGroupTypeController.selectedClientGroup = function() {
    var record = baseController.getSelectedRecord(clientGroupTypeController.tableTarget + ' .jtable-child-table-container');

    if (record == null)
        return false;

    bootbox.confirm(language.line('message_unassign_client_group'), function() {

        clientGroupTypeController.removeClientGroup(record);
    });


};

clientGroupTypeController.removeClientGroup = function(record) {
    var ajaxOptions = {
        url: clientGroupTypeRemoveClientGroup,
        type: 'post',
        dataType: 'json',
        data: {clientGroup: record.id}
    };

    $.ajax(ajaxOptions).done(function(response) {

        if (response.Result) {
            $(clientGroupTypeController.tableTarget + ' .jtable-child-table-container tr[data-record-key="' + record.id + '"]').remove();

            $('.message').html('<span class="alert alert-success">' + language.line('message_client_group_successfully_unassigned') + '</span>');
        }
    }).fail(function() {
        $('.message').html('<span class="alert alert-danger">' + language.line('message_client_group_no_unassigned') + '</span>');
    });
};

clientGroupTypeController.addClientGroup = function(event, anchor, cancel) {
    var records = baseController.getSelectedRecord(clientGroupTypeController.tableTarget, false);
    var clientGroup = {};

    clientGroup.id = null;
    clientGroup.client_group_type_id = records == null ? null : records.id;

    var url = $(anchor).attr('href');

    clientGroupTypeController.showPopupClientGroup(clientGroup, anchor, url);

};

clientGroupTypeController.editClientGroup = function(event, anchor, cancel) {
    var record = baseController.getSelectedRecord(clientGroupTypeController.tableTarget + ' .jtable-child-table-container');
    var clientGroup = {};

    if (record != null) {

        var url = $(anchor).attr('href');
        url = url + '/' + record.id

        clientGroupTypeController.showPopupClientGroup(clientGroup, anchor, url);
    }
};

clientGroupTypeController.showPopupClientGroup = function(clientGroup, anchor, url) {
    var ajaxOptions = {
        url: url,
        type: 'post',
        data: {clientGroup: clientGroup}

    };

    $(anchor).attr({disabled: 'disabled'});

    $.ajax(ajaxOptions).done(function(response) {
        $(anchor).removeAttr('disabled');

        baseController.showPopup(response);
    });
};


