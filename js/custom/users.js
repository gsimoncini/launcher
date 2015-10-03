userController = {};
userController.tableTarget = '#user-table';
userController.centerTableTarget = '#center-table';
userController.profileTableTarget = '#profile-table';


userController.initializeTable = function() {
    $(userController.tableTarget).jtable({
        tableId: userController.tableTarget,
        autoSaveChildTableState: true,
        sorting: false,
        footer: false,
        paging: true,
        pageSize: 50,
        listActionMode: 1,
        selecting: true, //Enable selecting
        multiselect: false, //Allow multiple selecting
        selectingCheckboxes: false, //Show checkboxes on first column
        messages: table.messages,
        actions: {
            listAction: userElementsUrl
        },
        fields: {
            username: {
                title: language.line('table_head_user'),
                width: '1%',
                listClass: 'text-center',
                key: true
            },
            photo: {
                title: language.line('table_head_photo'),
                width: '0.1%',
                listClass: 'text-center photo-cell',
                display: function(data) {
                    if (data.record.photo != '' && data.record.photo != null)
                        return '<img src="' + baseUrl + data.record.photo + '" alt="' + language.line('label_without_photo') + '" style="max-height: 60px; class="img-thumbnail" onerror="baseController.defaultImage(this);"/>';

                    return language.line('label_without_picture');
                }
            },
            html: {
                title: language.line('table_head_data'),
                width: '90%',
                listClass: 'detailed-cell',
                display: function(data) {
                    return userController.recordToHTML(data.record);
                }
            }
        }
    });
    userController.filterTable();
};

//Genera un html para la tabla a partir de un Record
userController.recordToHTML = function(record) {
    var stateTextClass;

    switch (record.active) {
        case '1':
            stateTextClass = 'success';
            break;
        case '0':
            stateTextClass = 'danger';
            break;
    }

    var html = '';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<h4> ' + record.last_name + ', ' + record.name + ' <small><i class="fa fa-angle-double-right"></i> ' + record.profile_name + '</small></h4>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div class="text-right"><span style="font-size: 80%;" class="label label-' + stateTextClass + '">' + record.status_name + '</span></div>';
    html += '</div>';
    html += '</div>';
    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<p style="margin-top: 4px;">' + record.doc_type_name + ': ' + (record.doc_number == null ? language.line('label_without_document') : record.doc_number) + '</p>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div style="margin-top: 4px;" class="text-right">' + (record.phone == null ? language.line('label_without_phone') : record.phone) + '</div>';
    html += '</div>';
    html += '</div>';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<p style="margin-top: -6px;">' + (record.client == null || record.client == '' ? language.line('label_without_client') : baseController.parseListText(record.client, 100)) + '</p>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div style="margin-top: -8px;" class="text-right text-primary">' + (record.email == null ? language.line('label_without_email') : record.email) + '</div>';
    html += '</div>';
    html += '</div>';

    return html;
};

userController.searchOnEnter = function(event) {
    if (event.keyCode == 13)
        userController.searchTable();
};

userController.searchTable = function() {
    var search = $('#search_filter').val();

    $(userController.tableTarget).jtable('setFilterMethod', function(value) {
        var searchCondition = (
                baseController.inText(value.username, search) ||
                baseController.inText(value.last_name, search) ||
                baseController.inText(value.name, search) ||
                baseController.inText(value.phone, search) ||
                baseController.inText(value.profile_name, search) ||
                baseController.inText(value.doc_number, search) ||
                baseController.inText(value.client, search) ||
                baseController.inText(value.email, search));


        return searchCondition;
    });

    $(userController.tableTarget).jtable('refresh');
};

userController.filterTable = function() {
    var clientGroup = multiselect.removeAllOption('#client_group_filter');
    var client = multiselect.removeAllOption('#client_filter');
    var role = multiselect.removeAllOption('#role_filter');
    var status = multiselect.removeAllOption('#status_filter');
    $('#search_filter').val('');

    var options = {
        client_group_filter: clientGroup,
        client_filter: client,
        role_filter: role,
        status_filter: status
    };

    $.post(userApplyFilterUrl, options).done(function() {
        $(userController.tableTarget).jtable('load', options);
        //define los filtros aplicados en un div
        userController.showApplyFilters(options);
    });
};

userController.removeFilterTable = function() {
    var ajaxOptions = {
        url: userRemoveFilterUrl,
        dataType: 'json'
    };
    $.ajax(ajaxOptions).done(function(response) {
        $('#client_group_filter').val(response.client_group_filter);
        $('#role_filter').val(response.role_filter);
        $('#status_filter').val(response.status_filter);

        $('#filter .multi-select').multiselect('refresh');

        $('#search_filter').val('');

        clientController.clientsDropdown(null, '#client_filter');

        $(userController.tableTarget).jtable('load');

        baseController.toggleFilterPanel(false);
        userController.showApplyFilters([]);
    });
};

//Actualiza la inforación de filtros aplicados
userController.showApplyFilters = function(options) {
    var filterText = '';

    if (options.client_group_filter != null)
        filterText += '<span data-filter="client_group_filter">' + language.line('label_client_group') + '</span>';

    if (options.client_filter != null)
        filterText += '<span data-filter="client_filter">' + language.line('label_service') + '</span>';

    if (options.role_filter != null)
        filterText += '<span data-filter="role_filter">' + language.line('label_role') + '</span>';

    if (options.status_filter != null)
        filterText += '<span data-filter="status_filter">' + language.line('label_status') + '</span>';

    $('.filter-box div.filter span').html(filterText == '' ? language.line('label_no') : filterText);

    $('.filter-box div:last-child span span').each(function() {
        var filter = $(this).attr('data-filter');
        var elements = multiselect.removeAllOption('#' + filter);
        var title = '';

        $.each(elements, function(index, value) {
            var text = $('#' + filter + ' option[value=' + value + ']').text();

            title += (index == 0 ? '' : ', ') + text;
        });

        $('span[data-filter=' + filter + ']').tooltip({'title': title, 'placement': 'bottom'});
    });
};

//Tabla de servicios para asociar con User
userController.initializeClientTable = function(pEditable) {
    $(userController.centerTableTarget).jtable({
        tableId: userController.centerTableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        listActionMode: 1,
        sorting: true,
        messages: table.messages,
        actions: {
            listAction: userClientsUrl
        },
        fields: {
            id: {
                title: language.line('table_head_id'),
                width: '1%',
                listClass: 'text-center',
                key: true
            },
            name: {
                title: language.line('table_head_name'),
                width: '10%'
            },
            center: {
                title: language.line('table_head_client_group'),
                width: '10%'
            },
            assign_control: {
                title: language.line('table_head_role'),
                width: '8%',
                sorting: false,
                listClass: 'text-center',
                display: function(data) {
                    if (pEditable)
                        return data.record.assign_control;
                    return data.record.role_name;
                }
            },
            role_id: {
                list: false
            },
            role_name: {
                list: false
            },
            assign_date: {
                listClass: 'text-center',
                width: '3%',
                title: language.line('table_head_assigned')
            },
            assign_user: {
                listClass: 'text-center',
                width: '3%',
                title: language.line('table_head_user')
            }
        },
        recordsLoaded: function(event, data) {
            userController.selectUserClients(data.records);
        }
    });
    $(userController.centerTableTarget).jtable('load', {username: $('input[name="username"]').val()});
};

//Carga y selecciona en la tabla todos los servicios asociados al usuario
userController.selectUserClients = function(records) {
    //obtengo los catalogos originalmente asignados
    var clients = $('input[name="user_clients"').val();

    if (clients == '' && clients == null)
        return;

    clients = JSON.parse(clients);

    //Marco los incluidos
    $.each(clients, function(i, client) {
        userController.checkClient(client.id, client.role_id);
    });
};

//Marca el servicio como asignado
/*
 * Si el servicio está asignado desde la base de datos, mantiene los datos del
 * usuario y fecha originales.
 */
userController.checkClient = function(pClientId, pRoleId) {

    if (pClientId == undefined || pClientId == null)
        return;

    //Obtengo el record de la tabla por id
    var client = $(userController.centerTableTarget + ' [data-record-key="' + pClientId + '"]').data('record');
    if (client == undefined || client == null)
        return;

    var newAssignUser = null;
    var newAssignDate = null;
    //verifico si  hay que asignarlo o no
    if (pRoleId != -1) {
        //Indico los nuevos datos para el record
        newAssignUser = language.line('label_you');
        newAssignDate = new Date();
        newAssignDate = newAssignDate.format('dd/mm/yyyy hh:mm');
    }

    userController._refreshAssignClientsButton(pClientId, pRoleId);

    //Actuaizo el estado del campo HIDDEN que mantiene los ids de catalogo
    userController._updateHiddenClientsIds(pClientId, pRoleId);

    //Actualizo los datos de la tabla

    //Si record no tiene usuario ==> es porque fue asignado ahora (aun no almacenado)
    //OR Si el nuevo usuario es null, significa que debe quitarse
    if (client.assign_user == null || client.assign_user == '' || newAssignUser == null) {
        //Asignado ahora y debe indicarse
        $(userController.centerTableTarget).jtable('changeCellValue', 'assign_user', pClientId, newAssignUser);
    }
    //Si record no tiene fecha  ==> es porque fue asignado ahora (aun no almacenado)
    //OR Si la nueva fecha es null, significa que debe quitarse
    if (client.assign_date == null || client.assign_date == '' || newAssignDate == null) {
        //Asignado ahora y debe indicarse
        $(userController.centerTableTarget).jtable('changeCellValue', 'assign_date', pClientId, newAssignDate);
    }

    //Indico si está asignado o no.
    client.role_id = pRoleId;
};

//Actualiza incluyendo o quitando el ID de catalogo en el campo hidden de catalogos
userController._updateHiddenClientsIds = function(pClientId, pRoleId) {

    if (pClientId == undefined || pClientId == null)
        return;

    //Obtengo los clientes asociados al usuerio
    var clients = $('input[name="user_clients"').val();
    clients = JSON.parse(clients);

    if (pRoleId != -1) {
        //Agrego al campo hidden de ids de clients
        clients = $.grep(clients, function(el, index) {
            return el.id != pClientId;
        });
        clients.push({'id': pClientId, 'role_id': pRoleId});

    } else {
        //Quito del campo hdden de ids de clients
        clients = $.grep(clients, function(el, index) {
            return el.id != pClientId;
        });
    }
    //Actualizo el campo hidden
    $('input[name="user_clients"').val(JSON.stringify(clients));
};


//Actualiza el estado del boton asignado/asginar
userController._refreshAssignClientsButton = function(pClientId, pRoleId) {

    if (pClientId == undefined || pClientId == null)
        return;

    $('#role_' + pClientId).val(pRoleId);

};

userController.searchOnEnterClient = function(event) {
    if (event.keyCode == 13)
        userController.filterOnClientTable();
};

//Filtra en caliente el listado de clients
userController.filterOnClientTable = function() {
    var text = $('input[name="client_search"]').val();
    var clientGroup = $('#client_group_filter').val();

    if (text == null || text == undefined)
        text = '';

    $('#center-table').jtable('setFilterMethod', function(value, ix) {
        var clientGroups = value.center_id.split(',');

        return value.name.toLowerCase().indexOf(text.toLowerCase()) >= 0 //filtro por text
                && userController._getClientFilterCheck(value)  //filtro por estado
                && (clientGroup == null || userController.arrayValueInArray(clientGroups, clientGroup));
    });

    $('#center-table').jtable('refresh');
};

userController.arrayValueInArray = function(values, array) {
    var success = false;

    $.each(values, function(index, element) {
        if ($.inArray(element, array) > -1) {
            success = true;
            return false;
        }
    });

    return success;
};

//Obtiene el estado del radiobutton de asignados/noasignados/todos
userController._getClientFilterCheck = function(pRecord) {
    var filter = $('input[name="client_assign"]:checked').val();
    if (filter == 'assigned')
        return pRecord.role_id > -1;
    else if (filter == 'not_assigned')
        return pRecord.role_id == -1;

    return true;
};

userController.refreshProfileFunctions = function() {
    var profile = $('#profile option:selected').val();
    $('#profile-table').jtable('load', {'profile': profile});
};


userController.initializeProfileTable = function() {
    $(userController.profileTableTarget).jtable({
        tableId: userController.profileTableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        listActionMode: 1,
        sorting: true,
        messages: table.messages,
        actions: {
            method: 'POST',
            listAction: profileFunctionsUrl
        },
        fields: {
            name: {
                title: language.line('table_head_name'),
                width: '10%'
            }
        }
    });
    $(userController.profileTableTarget).jtable('load', {profile: $('#profile option:selected').val()});
};
