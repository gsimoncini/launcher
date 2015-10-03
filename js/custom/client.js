clientController = {};

clientController.tableTarget = '#client-table';
clientController.catalogTableTarget = '#client-catalog-table';
clientController.shippingTableTarget = '#client-shipping-table';
clientController.groupTableTarget = '#client-group-table';
clientController.mapName = 'client-map';
clientController.mapController;
clientController.selectedRecord;
clientController.showCatalog;
clientController.selectedButtonCall;

clientController.initializeTable = function(popup) {
    if (typeof (popup) == 'undefined')
        popup = false;

    $(clientController.tableTarget).jtable({
        tableId: clientController.tableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        pageSize: 50,
        listActionMode: 1,
        selecting: true, //Enable selecting
        multiselect: false, //Allow multiple selecting
        selectingCheckboxes: false, //Show checkboxes on first column
        sorting: false,
        messages: table.messages,
        actions: {
            listAction: clientElementsUrl
        },
        fields: {
            id: {
                title: language.line('table_head_id'),
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
                        return '<img src="' + baseUrl + data.record.photo + '" alt="' + language.line('label_without_photo') + '"  style="max-height: 67px;"  class="img-thumbnail" onerror="baseController.defaultImage(this);"/>';

                    return language.line('label_without_picture');
                }
            },
            html: {
                title: language.line('table_head_service'),
                listClass: 'detailed-cell detailed-cell-sm',
                display: function(data) {
                    return clientController.recordToHTML(data.record);
                }
            },
            center: {
                list: false
            },
            description: {
                list: false
            },
            name: {
                list: false
            },
            email: {
                list: false
            },
            phone: {
                list: false
            },
            status_id: {
                list: false
            },
            client_type_description: {
                list: false
            },
            catalog: {
                list: false
            }
        },
        sortFinished: function() {
            if (popup)
                clientController.selectionOnChange();
        },
        selectionChanged: function() {
            if (popup)
                clientController.selectionOnChange();
        }
    });

    if (popup)
        $(clientController.tableTarget).jtable('load');
    else
        clientController.filterTable();
};

//Genera un html para la tabla a partir de un Record
clientController.recordToHTML = function(record) {
    var stateTextClass;

    switch (record.status_id) {
        case '1':
            stateTextClass = 'success';
            break;
        case '2':
            stateTextClass = 'danger';
            break;
    }

    var html = '';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<h4 style="margin: 0px;">';
    if (record.description != null)
        html += record.description + ' <small><i class="fa fa-angle-double-right"></i>';

    var lastNameFirstName = record.last_name + ', ' + record.first_name;
    html += (lastNameFirstName.length > 150) ? '' + lastNameFirstName.substring(0, 150) + '...</small></p>' : '' + lastNameFirstName + '</small></p>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<p class="text-right" style="margin-top: 2px; margin-bottom:1px;"><span  style="font-size: 80%;" class="label label-' + stateTextClass + '">' + record.status_name + '</span></p>';
    html += '</div>';
    html += '</div>';

    var catalog = record.catalog;

    if (catalog.length > 40) {
        var catalog_qty = record.catalog.split(',').length;
        catalog = catalog.substring(0, 40) + '...(' + catalog_qty + ')';
    }
    var email = '';
    if (record.email != null) {
        if (record.email.length > 50) {
            var email_qty = record.email.split(';').length;
            email = record.email.substring(0, 50) + '...(' + email_qty + ')';
        } else
            email = record.email;
    }

    var showCatalog;

    if (clientController.showCatalog)
        showCatalog = ' | ' + (catalog == null || catalog == '' ? language.line('label_without_catalog') : catalog)
    else
        showCatalog = '';

    html += '<div class="row">';
    html += '<div class="col-md-8">';
    html += '<h6>' + record.center + ' | ' + record.client_type_description + showCatalog + '</h6>';
    html += '<div class="text-left" style="margin-bottom: 5px;">' + (record.complete_address == null || record.complete_address == '' ? language.line('label_without_address') : record.complete_address) + '</div>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div class="text-right" style="margin-top: 5px; margin-bottom: 5px;">' + (record.phone == null || record.phone == '' ? language.line('label_without_phone') : record.phone) + '</div>';
    html += '<div class="text-right text-primary" style="margin-bottom: 5px;">' + (record.email == null || record.email == '' ? language.line('label_without_email') : email) + '</div>';
    html += '</div>';
    html += '</div>';



    return html;
};

clientController.searchOnEnter = function(event, input) {
    if (event.keyCode == 13)
        clientController.searchTable(input);
};

clientController.searchTable = function(input) {
    var search = $(input).val();

    $(clientController.tableTarget).jtable('setFilterMethod', function(value) {
        var searchCondition = (
                baseController.inText(value.id, search) ||
                baseController.inText(value.center, search) ||
                baseController.inText(value.description, search) ||
                baseController.inText(value.name, search) ||
                baseController.inText(value.email, search) ||
                baseController.inText(value.complete_address, search) ||
                baseController.inText(value.client_type_description, search) ||
                baseController.inText(value.last_name, search) ||
                baseController.inText(value.first_name, search) ||
                baseController.inText(value.status_name, search) ||
                baseController.inText(value.client_type_description, search) ||
                baseController.inText(value.catalog, search) ||
                baseController.inText(value.phone, search));

        return searchCondition;
    });

    $(clientController.tableTarget).jtable('refresh');
};

clientController.filterTable = function() {
    var clientGroup = multiselect.removeAllOption('#client_group_filter');
    var groupType = multiselect.removeAllOption('#group_type_filter');
    var status = multiselect.removeAllOption('#status_filter');

    var search = $('#search_filter').val();

    var options = {
        client_group_filter: clientGroup,
        status_filter: status,
        search_filter: search,
        group_type_filter: groupType
    };

    $.post(clientApplyFilterUrl, options).done(function() {
        $(clientController.tableTarget).jtable('load', options);
        //define los filtros aplicados en un div
        clientController.showApplyFilters(options);
    });
};

clientController.removeFilterTable = function() {
    var ajaxOptions = {
        url: clientRemoveFilterUrl,
        dataType: 'json'
    };

    $.ajax(ajaxOptions).done(function(response) {
        $('#group_type_filter').val(response.group_type_filter);
        $('#client_group_filter').val(response.client_group_filter);
        $('#client_filter').val(response.client_filter);
        $('#status_filter').val(response.status_filter);

        $('#filter .multi-select').multiselect('refresh');

        $('#date_from_filter').datepicker('setValue', response.date_from_filter);
        $('#date_to_filter').datepicker('setValue', response.date_to_filter);

        $('#search_filter').val(response.search_filter);

        $(clientController.tableTarget).jtable('load');

        baseController.toggleFilterPanel(false);

        clientController.showApplyFilters([]);
    });
};

clientController.refreshDropdownOption = function(clientGroup, target) {
    var ajaxOptions = {
        url: clientElementsForDropdownUrl,
        type: 'post',
        dataType: 'json',
        data: {client_group: clientGroup}
    };

    $.ajax(ajaxOptions).done(function(response) {
        $(target).empty();

        $.each(response, function(index, value) {
            $(target).append('<option value="' + value.id + '">' + value.value + '</option>');
        });

        if (response.length == 0)
            $(target).attr({disabled: 'disabled'});
        else
            $(target).removeAttr('disabled');

        //REUBICAR COMO CALLBACK
        $(target).change();
    });
};

clientController.fillById = function(id, data) {
    var ajaxOptions = {
        url: clientGet,
        type: 'post',
        dataType: 'json',
        data: {id: id}
    };

    $.ajax(ajaxOptions).done(function(response) {
        $.each(data, function(index, element) {
            $('#' + element.target).val(response[element.name]);
        });
    });
};

//Actualiza la inforación de filtros aplicados
clientController.showApplyFilters = function(options) {
    var filterText = '';

    if (options.client_group_filter != null)
        filterText += '<span data-filter="client_group_filter">' + language.line('label_client_group') + '</span>';

    if (options.status_filter != null)
        filterText += '<span data-filter="status_filter">' + language.line('label_status') + '</span>';

    if (options.group_type_filter != null && options.group_type_filter.length > 0)
        filterText += '<span data-filter="group_type_filter">' + language.line('label_client_group_type') + '</span>';

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

//Tabla de catalogos para asociar con Client

clientController.initializeCatalogTable = function(pEditable) {
    $(clientController.catalogTableTarget).jtable({
        tableId: clientController.catalogTableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        listActionMode: 1,
        sorting: true,
        messages: table.messages,
        actions: {
            listAction: clientCatalogsUrl
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
            description: {
                title: language.line('table_head_description'),
                width: '10%'
            },
            product_quantity: {
                title: language.line('table_head_product_quantity'),
                listClass: 'text-center',
                width: '3%'
            },
            assign_control: {
                title: language.line('table_head_assigned'),
                width: '3%',
                sorting: false,
                listClass: 'text-center',
                display: function(data) {
                    if (pEditable)
                        //Devuelve el control para asignar/desasignar catalogos
                        return '<button type="button" class="btn_' + data.record.id + ' btn ' + (data.record.assign ? 'btn-success' : 'btn-default') + '" onclick="clientController.checkCatalog(' + data.record.id + ', $(this).hasClass(\'btn-default\'));" >' + (data.record.assign ? '<i class="fa fa-check"></i> ' + language.line('label_assigned') : language.line('label_assign')) + '</button>';
                    else
                        return (data.record.assign ? '<span class="text-success">' + language.line('label_assigned') : '<span>' + language.line('label_not_assigned')) + '</span>';
                }
            },
            assign: {
                list: false
            },
            assign_date: {
                listClass: 'text-center',
                width: '3%',
                title: language.line('table_head_selection_date')
            },
            assign_user: {
                listClass: 'text-center',
                width: '3%',
                title: language.line('table_head_user')
            }
        },
        recordsLoaded: function(event, data) {
            clientController.selectClientCatalogs(data.records);
        }
    });
    $(clientController.catalogTableTarget).jtable('load', {id: $('input[name="id"]').val()});
};

//Carga y selecciona en la tabla todos los catalogos asociados al cliente
clientController.selectClientCatalogs = function(records) {
    //obtengo los catalogos originalmente asignados
    var catalogs = $('input[name="client_catalogs"').val();

    if (catalogs == '' && catalogs == null)
        return;

    catalogs = catalogs.split(',');

    //Marco los incluidos
    $.each(catalogs, function(i, catalogId) {
        clientController.checkCatalog(catalogId, true);
    });
};

//Marca el catalogo como asignado o no en la tabla
/*
 * Si el catalogo está asignado desde la base de datos, mantiene los datos del
 * usuario y fecha originales.
 */
clientController.checkCatalog = function(pCatalogId, pIsChecked) {
    if (pCatalogId == undefined || pCatalogId == null)
        return;

    //Obtengo el record de la tabla por id
    var catalog = $(clientController.catalogTableTarget + ' [data-record-key="' + pCatalogId + '"]').data('record');
    if (catalog == undefined || catalog == null)
        return;

    var newAssignUser = null;
    var newAssignDate = null;
    //verifico si  hay que asignarlo o no
    if (pIsChecked) {
        //Indico los nuevos datos para el record
        newAssignUser = language.line('label_you');
        newAssignDate = new Date();
        newAssignDate = newAssignDate.format('dd/mm/yyyy hh:mm');
    }

    //Actualizo el texto del boton
    clientController._refreshAssignCatalogButton(pCatalogId, pIsChecked);

    //Actuaizo el estado del campo HIDDEN que mantiene los ids de catalogo
    clientController._updateHiddenCatalogsIds(pCatalogId, pIsChecked);

    //Actualizo los datos de la tabla

    //Si record no tiene usuario ==> es porque fue asignado ahora (aun no almacenado)
    //OR Si el nuevo usuario es null, significa que debe quitarse
    if (catalog.assign_user == null || catalog.assign_user == '' || newAssignUser == null) {
        //Asignado ahora y debe indicarse
        $(clientController.catalogTableTarget).jtable('changeCellValue', 'assign_user', pCatalogId, newAssignUser);
    }
    //Si record no tiene fecha  ==> es porque fue asignado ahora (aun no almacenado)
    //OR Si la nueva fecha es null, significa que debe quitarse
    if (catalog.assign_date == null || catalog.assign_date == '' || newAssignDate == null) {
        //Asignado ahora y debe indicarse
        $(clientController.catalogTableTarget).jtable('changeCellValue', 'assign_date', pCatalogId, newAssignDate);
    }

    //Indico si está asignado o no.
    catalog.assign = pIsChecked;
};

//Actualiza incluyendo o quitando el ID de catalogo en el campo hidden de catalogos
clientController._updateHiddenCatalogsIds = function(pCatalogId, pIsChecked) {
    if (pCatalogId == undefined || pCatalogId == null)
        return;

    //Obtengo los catalogos asociados al client
    var catalogs = $('input[name="client_catalogs"').val();

    if (catalogs == '')
        catalogs = [];
    else
        catalogs = catalogs.split(',');

    if (pIsChecked) {
        var index = catalogs.indexOf(pCatalogId.toString());

        //Agrego al campo hidden de ids de catalogos
        if (index > -1)
            catalogs[index] = pCatalogId.toString();
        else
            catalogs.push(pCatalogId.toString());
    } else
        //Quito del campo hdden de ids de catalogos
        catalogs.splice(catalogs.indexOf(pCatalogId.toString()), 1);

    //Actualizo el campo hidden
    $('input[name="client_catalogs"').val(catalogs.join(','));
};

//Actualiza el estado del boton asignado/asginar
clientController._refreshAssignCatalogButton = function(pCatalogId, pIsChecked) {
    if (pCatalogId == undefined || pCatalogId == null)
        return;

    var btnTxt = '';

    if (pIsChecked) {
        btnTxt = '<i class="fa fa-check"></i> ' + language.line('label_assigned');
        $('.btn_' + pCatalogId).removeClass('btn-default');
        $('.btn_' + pCatalogId).addClass('btn-success');
    } else {
        //Desasigno
        btnTxt = language.line('action_assign');
        $('.btn_' + pCatalogId).addClass('btn-default');
        $('.btn_' + pCatalogId).removeClass('btn-success');
    }

    //Actualizo el texto del boton
    $('.btn_' + pCatalogId).html(btnTxt);
};

clientController.searchOnEnterProductCatalog = function(event) {
    if (event.keyCode == 13)
        clientController.filterOnCatalogList();
};

//Filtra en caliente el listado de catalogos
clientController.filterOnCatalogList = function() {
    var text = $('input[name="catalog_search"]').val();

    if (text == null || text == undefined)
        text = '';

    $('#client-catalog-table').jtable('setFilterMethod', function(value, ix) {
        return value.name.toLowerCase().indexOf(text.toLowerCase()) >= 0 //filtro por text
                & clientController._getCatalogFilterCheck(value);  //filtro por estado
    });
    $('#client-catalog-table').jtable('refresh');
};

//Obtiene el estado del radiobutton de asignados/noasignados/todos
clientController._getCatalogFilterCheck = function(pRecord) {
    var filter = $('input[name="catalog_assign"]:checked').val();
    if (filter == 'assigned')
        return pRecord.assign;
    else if (filter == 'not_assigned')
        return !pRecord.assign;

    return true;
};

/* #### MAPA #### */

//Inicializa el mapa del form de Client.
clientController.initializeGeorreferenceMap = function(pId) {

    var map_options = {
        controls: {
            geocode: true
            , simpleSelection: false
            , multiSelection: false
            , polygonSelection: false
            , clearSelection: false
            , refresh: false
            , fit: false
            , enable: false
            , iconFamilySet: false
        },
        //Pack de iconos
        iconFamilySet: {
            marker: true
            , distributor: false
            , route: false
            , term: false
        }
    };

    clientController.mapController = new Generic_Map_Manager(clientController.mapName, pId, map_options);

    clientController.mapController.confirmPointCallback = function(x, y, type) {
        $('input[name="coord_x"]').val(x);
        $('input[name="coord_y"]').val(y);
        $('input[name="georreff_type"]').val(type);
        var marker = clientController.mapController.rmap.markers[0];

        if (marker != undefined) {
            marker.transaction.mark_type = type;
            marker.type = type;
        }
        clientController.mapController.rmap.refreshIcons();
    };
    clientController.mapController.rmap.regenerate();
};

//Genera el mapa mostrando el punto actual, si lo hay
clientController.generateGeorreferenceMap = function() {
    setTimeout(function() {
        clientController.mapController.rmap.refresh();
        clientController.mapController.rmap.removeAllMarkers();
        var x = $('input[name=coord_x]').val();
        var y = $('input[name=coord_y]').val();
        var geotype = $('input[name=georreff_type]').val();
        if (x != 0 && y != 0) {

            var entity = new Generic_Marker(x, y, clientController.getEntity(), geotype);
            entity.setMap(clientController.mapController.rmap, []);
            clientController.mapController.rmap.fit();
        }
        clientController.mapController.rmap.map.setZoom(15);
    }, 450);
};

//Devuelve el domicilio tomando los datos del form
clientController.getAddress = function() {
    return $('input[name="street"]').val()
            + ($('input[name="number"]').val() != '' ? ' ' + $('input[name="number"]').val() : '')
            + ($('input[name="locality"]').val() != '' ? ', ' + $('input[name="locality"]').val() : '')
            + ($('input[name="estate"]').val() != '' ? ', ' + $('input[name="estate"]').val() : '')
            + ($('input[name="country"]').val() != '' ? ', ' + $('input[name="country"]').val() : '');
};

clientController.getEntity = function() {
    return new Client({
        id: ($('input[name="id"]').val() ? $('input[name="id"]').val() : 1)
        , client_group: $('#client_group_id option:selected').text()
        , description: $('input[name="description"]').val()
        , code_client: $('input[name="code_client"]').val()
        , name: $('input[name="last_name"]').val() + ', ' + $('input[name="first_name"]').val()
        , address: clientController.getAddress()
        , street: $('input[name="street"]').val()
        , number: $('input[name="number"]').val()
        , floor: $('input[name="floot"]').val()
        , apartment: $('input[name="apartment"]').val()
        , zip_code: $('input[name="zip_code"]').val()
        , locality: $('input[name="locality"]').val()
        , estate: $('input[name="estate"]').val()
        , country: $('input[name="country"]').val()
        , status: $('input[name="status"]').val()
        , mark_type: $('input[name="georreff_type"]').val()
    });
};

clientController.clearPointing = function(event) {
    event.preventDefault();
    bootbox.dialog({
        message: language.line('message_clear_pointing_question'),
        title: language.line('label_delete_point'),
        buttons: {
            success: {
                label: language.line('label_i_wish_to_continue'),
                className: "btn-primary btn-sm",
                callback: function() {
                    clientController.mapController.rmap.removeAllMarkers();
                    clientController.mapController.confirmPointCallback(null, null, null);
                }
            },
            cancel: {
                label: language.line('label_cancel'),
                className: "btn-default btn-sm",
                callback: function() {
                }
            }
        }
    });
};

//Punteo manual aproximado
clientController.manualApprox = function(event) {
    event.preventDefault();
    if (clientController.mapController.rmap.markers.length > 0) {
        bootbox.dialog({
            message: language.line('message_clear_pointing_question'),
            title: language.line('label_approximate_manual_plotting'),
            buttons: {
                success: {
                    label: language.line('label_i_wish_to_continue'),
                    className: "btn-primary btn-sm",
                    callback: function() {
                        clientController.mapController.rmap.removeAllMarkers();
                        clientController._manuaPoint(clientController.mapController.MODE_APPROX);
                    }
                },
                cancel: {
                    label: language.line('label_cancel'),
                    className: "btn-default btn-sm",
                    callback: function() {
                    }
                }
            }
        });
    } else {
        clientController._manuaPoint(clientController.mapController.MODE_APPROX);
    }
};

//Metodo privado para generar punteo manual.
clientController._manuaPoint = function(pMode) {
    var p = clientController.getEntity();
    clientController.mapController.setManualPoint(pMode, p);
};

//Punteo manual exacto
clientController.manualExact = function(event) {
    event.preventDefault();
    if (clientController.mapController.rmap.markers.length > 0) {
        bootbox.dialog({
            message: language.line('message_clear_pointing_question'),
            title: language.line('label_accurate_manual_plotting'),
            buttons: {
                success: {
                    label: language.line('label_i_wish_to_continue'),
                    className: "btn-primary btn-sm",
                    callback: function() {
                        clientController.mapController.rmap.removeAllMarkers();
                        clientController._manuaPoint(clientController.mapController.MODE_EXACT);
                    }
                },
                cancel: {
                    label: language.line('label_cancel'),
                    className: "btn-default btn-sm",
                    callback: function() {
                    }
                }
            }
        });
    } else {
        clientController._manuaPoint(clientController.mapController.MODE_EXACT);
    }
};

//Punteo automatico
clientController.automatic = function(event) {
    event.preventDefault();
    if (clientController.mapController.rmap.markers.length > 0) {
        bootbox.dialog({
            message: language.line('message_clear_pointing_question'),
            title: language.line('label_automatic_plotting'),
            buttons: {
                success: {
                    label: language.line('label_i_wish_to_continue'),
                    className: "btn-primary btn-sm",
                    callback: function() {
                        clientController.mapController.rmap.removeAllMarkers();
                        var p = clientController.getEntity();
                        clientController.mapController.setAutomaticPoint(p);
                    }
                },
                cancel: {
                    label: language.line('label_cancel'),
                    className: "btn-default btn-sm",
                    callback: function() {
                    }
                }
            }
        });
    } else {
        var p = clientController.getEntity();
        clientController.mapController.setAutomaticPoint(p);
    }
};

clientController.showPopupTable = function(url, button) {
    if (typeof (url) == 'undefined')
        url = clientTablePopupUrl;

    clientController.selectedButtonCall = button;

    $.ajax(url).done(function(response) {
        baseController.showPopup(response);
    });
};

clientController.selectOrder = function(callback) {
    clientController.selectedRecord = baseController.getSelectedRecord(clientController.tableTarget);

    if (typeof (callback) !== 'undefined')
        callback();

    $('#popup').modal('hide');
};

clientController.selectionOnChange = function() {
    var selectedRow = $(clientController.tableTarget).jtable('selectedRows');

    if (selectedRow.length == 0)
        $('#popup .modal-footer button:first').attr({disabled: 'disabled'});
    else
        $('#popup .modal-footer button:first').removeAttr('disabled');
};

clientController.clientsDropdown = function(clientGroup, target, groupType) {
    var ajaxOptions = {
        url: clientElementsForDropdownUrl,
        type: 'post',
        dataType: 'json',
        data: {client_group: clientGroup, group_type: groupType}
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

clientController.allClientsDropdown = function(clientGroup, target) {
    var ajaxOptions = {
        url: clientAllElementsForDropdownUrl,
        type: 'post',
        dataType: 'json',
        data: {client_group: clientGroup}
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

clientController.clientsGroupsDropdown = function(groupType, target) {
    var ajaxOptions = {
        url: clientGroupElementsForDropdownUrl,
        type: 'post',
        dataType: 'json',
        data: {client_group_type: groupType}
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

        $(target + ' + .btn-group-multiselect').trigger('hidden.bs.dropdown');
    });
};

/* ########## TABLA DE EMPRESAS DE TRANSPORTE ############## */

clientController.shippingData = {'Result': 'OK', 'Records': [],
    'TotalRecordCount': 0};

clientController.initializeShippingTable = function(pEditable) {
    $(clientController.shippingTableTarget).jtable({
        tableId: clientController.tableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        selecting: true,
        sorting: true,
        pageSize: 25,
        listActionMode: 1,
        openChildAsAccordion: true,
        messages: table.messages,
        actions: {
            listAction: function() {
                return clientController.shippingData;
            }
        },
        fields: {
            id: {
                title: language.line('table_head_id'),
                width: '1%',
                listClass: 'text-center',
                key: true
            },
            name: {
                title: language.line('table_head_shipping'),
                listClass: 'text-left'
            },
            is_prefered: {
                title: language.line('table_head_prefered'),
                listClass: 'text-center',
                display: function(data) {
                    if (data.record.is_prefered == true)
                        return language.line('label_yes');
                    else
                        return language.line('label_no');
                }
            },
            shipping_type_name: {
                title: language.line('table_head_shipping_type'),
                listClass: 'text-left'
            },
            order_type_name: {
                title: language.line('table_head_order'),
                listClass: 'text-left',
                display: function(data) {
                    return language.line('label_' + data.record.order_type_name + '_order');

                }
            },
            distribution_plan: {
                title: language.line('table_head_distribution_plan'),
                listClass: 'text-left'
            }
        }
    });
    $(clientController.shippingTableTarget).jtable('load', {id: $('input[name="id"]').val()});
};


clientController.removeShippingCompany = function() {


    var selected = baseController.getSelectedRecord(clientController.shippingTableTarget);
    if (selected != null) {

        bootbox.confirm(language.line('message_delet_shipping_company'), function() {

            //Quito los elementos seleccionados
            var key = selected.id;

            var result = $.grep(clientController.shippingData.Records, function(value) {
                return value.id != key;
            });

            clientController.shippingData.Records = result;

            $(clientController.shippingTableTarget).jtable('load');
        });
    }
};

clientController.addShippingCompany = function(evt, anchor) {
    baseController.popup(evt, anchor);
};

clientController.acceptAddShippingCompany = function() {
    var record = $('#popup').find('input, select, textarea').serializeObject();

    record.id = record.shipping_company;
    var selectizeObject = $('#shipping_company').selectize({})[0].selectize;
    var valueSelectizeObject = selectizeObject.getValue();
    record.name = selectizeObject.options[valueSelectizeObject].value_1;
    record.is_prefered = $('#preference').prop("checked");
    record.shipping_type_name = $('#shipping_type option[value=' + record.shipping_type + ']').text();
    record.shipping_type_id = record.shipping_type;
    record.default_shipping_term = null;
    record.order_type_name = $('#order_type option[value=' + record.order_type + ']').val();
    record.distribution_plan = $('#client_distribution_plan option[value=' + record.client_distribution_plan + ']').text();
    record.distribution_plan_id = $('#client_distribution_plan option[value=' + record.client_distribution_plan + ']').val();

    var result = $.grep(clientController.shippingData.Records, function(value) {
        return value.id == record.id;
    });

    if (result == false) {
        clientController.shippingData.Records.push(record);

        $(clientController.shippingTableTarget).jtable('load');

        $('#popup').modal('hide');
    }
    else
        bootbox.alert(language.line('message_shipping_company_exist'));
};

clientController.getShippingCompany = function() {
    var shipping_company = {
        id: $('#id').val(),
        name: $('#shipping_company').val(),
        is_prefered: $('#preference').prop("checked"),
        shipping_type_name: $('#shipping_type').val()
    };

    return shipping_company;
};

/* ########## TABLA DE CENTROS ############## */

clientController.initializeGroupTable = function() {
    $(clientController.groupTableTarget).jtable({
        tableId: clientController.tableTarget,
        autoSaveChildTableState: true,
        footer: false,
        paging: true,
        selecting: true,
        sorting: true,
        pageSize: 25,
        listActionMode: 1,
        openChildAsAccordion: true,
        messages: table.messages,
        actions: {
            listAction: clientGroupTypeElementsUrl
        },
        fields: {
            id: {
                title: language.line('table_head_id'),
                width: '1%',
                listClass: 'text-center',
                key: true
            },
            description: {
                title: language.line('table_head_type_group'),
                listClass: 'text-left'
            },
            client_group: {
                title: language.line('table_head_group'),
                listClass: 'text-left',
                display: function(data) {
                    return  clientController.clientGroupDisplay(data.record);
                }
            },
            client_group_id: {
                list: false
            }}
    });

    $(clientController.groupTableTarget).jtable('load', {}, function(data) {
        clientController.assignClientGroupValues();

        $(clientController.groupTableTarget).jtable('refresh');
    });
};

clientController.clientGroupDisplay = function(record) {
    var values = record.client_group_ids.split(',');
    var names = record.client_group_names.split('{{separator}}');
    var descriptions = record.client_group_descriptions.split('{{separator}}');

    var options = '<option value="0">' + language.line('label_no_aplication_dropdown') + '</option>';

    if (values.length >= 1 && values[0] != "") {
        $.each(values, function(index, value) {
            options += '<option value="' + value + '"' + (record.client_group_id == value ? ' selected' : '') + '>' + names[index] + ' - ' + descriptions[index] + '</option>';
        });
    }

    var dropdown = '<select id="client_group_' + record.id + '" class="form-control input-sm" onchange="clientController.dropdownClientGroupChange(this);">' + options + '</select>';

    return dropdown;
};

clientController.dropdownClientGroupChange = function(select) {
    var value = $(select).val();
    var record = $(select).closest('tr').data('record');

    if (value == 0)
        value = null;

    record.client_group_id = value;
};

clientController.getClientGroupFromTable = function() {
    var records = $(clientController.groupTableTarget).jtable('getAllRecords');
    var ids = [];

    $.each(records, function(index, value) {
        if (value.client_group_id != null)
            ids.push(value.client_group_id);
    });

    return ids;
};

clientController.assignClientGroupValues = function() {
    var records = $(clientController.groupTableTarget).jtable('getAllRecords');

    var ids = $('#client_group_elements').val().split(',');

    $.each(ids, function(i, id) {
        $.each(records, function(j, record) {
            if (record.client_group_ids != null) {
                var clientGroup = record.client_group_ids.split(',');
                var index = $.inArray(id, clientGroup);

                if (index > -1) {
                    record.client_group_id = id;
                    return false;
                }
            }
        });
    });
};
