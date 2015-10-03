baseController = {};

baseController.getSelectedRecord = function(table, showMsg) {
    if (typeof (showMsg) == 'undefined')
        showMsg = true;

    var selectedRow = $(table).jtable('selectedRows');
    var record = null;

    if (selectedRow.length > 0)
        record = $(selectedRow).data('record');
    else {
        if (showMsg)
            bootbox.alert(language.line('message_no_item_selected'));
    }

    return record;
};

baseController.getSelectedRecords = function(table, showMsg) {
    if (typeof (showMsg) == 'undefined')
        showMsg = true;
    var selectedRow = $(table).jtable('selectedRows');
    var record = null;

    if (selectedRow.length > 0) {
        record = [];

        $(selectedRow).each(function(index, element) {
            record.push($(element).data('record'));
        });
    } else {
        if (showMsg)
            bootbox.alert(language.line('message_no_item_selected'));
    }

    return record;
};

baseController.prepareActionUrl = function(event, anchor, table, validation) {
    event.preventDefault();

    var startDelimeter = '{';
    var endDelimeter = '}';

    var url = $(anchor).attr('href');
    var record = baseController.getSelectedRecord(table);

    if (record == null)
        return null;

    if (typeof (validation) == 'undefined' || validation())
        location.href = baseController.parseParameters(url, record, startDelimeter, endDelimeter);
};

baseController.parseParameters = function(url, record, startDelimeter, endDelimeter) {
    var startIndex = url.indexOf(startDelimeter);
    var endIndex = url.indexOf(endDelimeter);

    if (startIndex == -1 || endIndex == -1)
        return url;

    var parameter = url.substring(startIndex + 1, endIndex);
    var resultUrl = url.replace(startDelimeter + parameter + endDelimeter, record[parameter]);

    return baseController.parseParameters(resultUrl, record, startDelimeter, endDelimeter);
};

baseController.parseListText = function(text, length) {
    if (typeof (length) == 'undefined')
        length = 40;

    if (text.length > length)
        text = text.substring(0, length) + '...(' + text.split(',').length + ')';

    return text;
};

baseController.toggleFilterPanel = function(open, panelTarget, buttonTarget, onlyOne, generalPanelTarget) {
    if (typeof (panelTarget) == 'undefined')
        panelTarget = '#filter';

    if (typeof (buttonTarget) == 'undefined')
        buttonTarget = '.btn-filter';

    if (typeof (onlyOne) == 'undefined')
        onlyOne = false;

    if (typeof (generalPanelTarget) == 'undefined')
        generalPanelTarget = '.filter-panel';

    if (typeof (open) == 'undefined' || open === null) {
        if (onlyOne)
            $(generalPanelTarget).not(panelTarget).addClass('hide');

        $(panelTarget).toggleClass('hide');
    } else {
        if (open) {
            $(panelTarget).removeClass('hide');
        } else {
            $(panelTarget).addClass('hide');
        }
    }
};

baseController.toggleMenu = function() {
    $('.sidebar').toggleClass('sidebar-flat');
    $('.page-content').toggleClass('page-content-full');

    $.cookie('sidebar-hide', $('.sidebar').hasClass('sidebar-flat'), {path: '/'});
};

baseController.refreshDropdownOptions = function(options) {
    var ajaxOptions = {url: options.url,
        type: 'post',
        dataType: 'json',
        data: {id: options.value}
    };

    $.ajax(ajaxOptions).done(function(response) {
        $(options.target).empty();

        $.each(response, function(index, value) {
            $(options.target).append('<option value="' + value.id + '">' + value.value + '</option>');
        });

        if (response.length == 0)
            $(options.target).attr({disabled: 'disabled'});
        else
            $(options.target).removeAttr('disabled');

        $(options.target).multiselect('refresh');

        if (typeof (options.callback) !== 'undefined')
            options.callback();
    });
};

baseController.getAllDropdownOptions = function(target) {
    var values = [];

    $(target + ' option').each(function(index, element) {
        var value = $(element).val();

        if (value !== 'multiselect-all')
            values.push(value);
    });

    return values;
};

baseController.sortTable = function(event, anchor, tableTarget, column) {
    event.preventDefault();

    var sort = table.sortTable(tableTarget, column);

    var icon = $(anchor).find('i');
    var iconClass = sort.sortOrder == 'desc' ? 'fa-caret-down' : 'fa-caret-up';

    $(anchor).closest('.dropdown-menu').find('i').removeClass('fa-caret-up fa-caret-down');

    icon.addClass(iconClass);
};

baseController.sortObjectProperties = function(data) {
    var keys = [];
    var sorted = {};

    for (var key in data)
        if (data.hasOwnProperty(key))
            keys.push(key);

    keys.sort();

    $.each(keys, function(index, key) {
        sorted[key] = data[key];
    });

    return sorted;
};

baseController.removeDecimals = function(number) {
    if (number == null)
        return number;

    return number.split('.')[0];
};

baseController.numberInRange = function(number, min, max) {
    return number >= min && number <= max;
};

baseController.dateInRange = function(date, min, max) {
    var arrayMin = min.split('/');
    var arrayMax = max.split('/');
    var arrayCheck = date.split('/');

    var dateFrom = new Date(arrayMin[2], arrayMin[1] - 1, arrayMin[0]);
    var dateTo = new Date(arrayMax[2], arrayMax[1] - 1, arrayMax[0]);
    var dateCheck = new Date(arrayCheck[2], arrayCheck[1] - 1, arrayCheck[0]);

    return dateCheck > dateFrom && dateCheck < dateTo;
};

baseController.inText = function(search, text) {
    if (search == null)
        return false;

    return search.toLowerCase().indexOf(text.toLowerCase()) >= 0;
};

baseController.popup = function(event, anchor) {
    event.preventDefault();

    $(anchor).attr({disabled: 'disabled'});

    var url = $(anchor).attr('href');

    $.ajax(url).done(function(response) {
        $(anchor).removeAttr('disabled');

        baseController.showPopup(response);
    });
};

baseController.showPopup = function(html, pCloseCallback) {
    $('#popup-container').html(html);

    $('#popup').modal({backdrop: 'static'});

    $('#popup').on('hidden.bs.modal', function() {
        $('#popup-container').empty();
        //Callback para luego de cerrar
        if (typeof (pCloseCallback) != 'undefined')
            pCloseCallback();
    });

    $('#popup button[type=submit]').click(function() {
        $(this).attr({disabled: 'disabled'});
    });
};

baseController.downloadURL = function(name, url) {
    var link = document.createElement('a');

    link.download = name;
    link.href = url;

    link.click();
};

baseController.defaultImage = function(image) {
    image.onerror = '';
    image.src = baseUrl + 'img/base/no-img.png';

    return true;
};

baseController.getLastParameterURL = function(url) {
    return url.substring(url.lastIndexOf('/') + 1);
};

baseController.startLoadingButtonEfect = function(button) {
    var $button = $(button);
    var $icon = $button.find('i');

    $icon.removeClass();
    $icon.addClass('fa fa-spin fa-spinner btn-icon-margin');

    $button.attr({disabled: 'disabled'});
};

baseController.endLoadingButtonEfect = function(button, buttonClass) {
    var $button = $(button);
    var $icon = $button.find('i');

    $icon.removeClass();
    $icon.addClass(buttonClass);

    $button.removeAttr('disabled');
};

baseController.fixFooterWidth = function() {
    $('.footer').css('width', $('body').width());
};

baseController.confirmFormSubmit = function(event, callback) {
    event.preventDefault();

    bootbox.confirm(language.line('message_confirm_form_submit'), function() {
        if (typeof (callback) == 'undefined')
            $('form').submit();
        else
            callback();
    });
};

baseController.confirmFormCancel = function(event, button) {
    event.preventDefault();

    bootbox.confirm(language.line('message_confirm_form_cancel'), function() {
        location.href = button.href;
    });
};

baseController.orderView = function(table, url, tabActive) {
    var record = baseController.getSelectedRecord(table);

    if (record == null)
        return null;

    baseController.orderPopup(record.id, url, tabActive);
};

baseController.orderPopup = function(id, url, tabActive) {
    var data = {
        id: id,
        tab_active: tabActive
    };

    $.post(url, data).done(function(response) {
        baseController.showPopup(response);
    });
};

baseController.searchOnEnter = function(event, controller, parameters) {
    if (typeof (parameters) === 'undefined')
        parameters = [];

    if (event.keyCode == 13)
        controller.searchTable.apply('searchTable', parameters);
};

baseController.toggleChildTable = function(options) {
    var defaults = {
        id: null,
        table: null,
        button: null,
        initializeChildTable: function(options) {
            return null;
        }
    };

    options = $.extend(defaults, options);

    var icon = $(options.button).children();

    if (icon.hasClass('fa-plus')) {
        $(options.table).find('button i').each(function() {
            $(this).removeClass('fa-minus');
            $(this).addClass('fa-plus');
        });

        icon.removeClass('fa-plus');
        icon.addClass('fa-minus');

        options.initializeChildTable(options);
    } else {
        icon.removeClass('fa-minus');
        icon.addClass('fa-plus');

        baseController.closeChildTable(options.table, options.button);
    }
};

baseController.closeChildTable = function(table, button) {
    var row = $(button).closest('tr');

    $(table).jtable('closeChildTable', row);
};

baseController.chooseExportMode = function(url, format) {
    bootbox.dialog({
        title: language.line('label_confirmation'),
        message: language.line('message_export_table'),
        buttons: {
            accept: {
                label: language.line('action_export_simple'),
                className: 'btn-primary btn-sm',
                callback: function() {
                    location.href = url + '/' + format;
                }
            },
            cancel: {
                label: language.line('action_export_with_details'),
                className: 'btn-default btn-sm',
                callback: function() {

                    urlArray = url.split('/');

                    urlArray[urlArray.length - 2] = 'export_with_detail';

                    url = '';
                    for (var index = 0; index < urlArray.length; index++) {
                        url += urlArray[index];
                        url += '/';
                    }
                    console.log(url);
                    location.href = url + format;
                }
            }
        }
    });
};

//Permite determinar si está activa el Block Mayus.
baseController.capsLockEventHandler = function(e, showCallback, hiddeCallback) {
    var ev = e ? e : window.event;
    if (!ev) {
        return;
    }
    var targ = ev.target ? ev.target : ev.srcElement;
    // get key pressed
    var which = -1;
    if (ev.which) {
        which = ev.which;
    } else if (ev.keyCode) {
        which = ev.keyCode;
    }
    // get shift status
    var shift_status = false;
    if (ev.shiftKey) {
        shift_status = ev.shiftKey;
    } else if (ev.modifiers) {
        shift_status = !!(ev.modifiers & 4);
    }
    if (((which >= 65 && which <= 90) && !shift_status) ||
            ((which >= 97 && which <= 122) && shift_status)) {
        // uppercase, no shift key
        showCallback(targ);
    } else {
        hiddeCallback(targ);
    }
};

baseController.showLoading = function(msg) {
    if (typeof (msg) == 'undefined')
        msg = '...';
    var content = '<div class="loading-panel"><span class="msg-box">' + msg + '</span></div>';
    $('body').append(content);
};

baseController.disposeLoading = function() {
    $('.loading-panel').remove();
};

baseController.fileExist = function(file) {
    var file_exist = false;
    $.ajax({
        url: baseUrl + file,
        type: 'HEAD',
        async: false,
        error: function() {
            file_exist = false;
        },
        success: function() {
            file_exist = true;
        }
    });
    return file_exist;
};

baseController.getMultiSelectCookieName = function(table) {
    return 'multipleTableRow' + table + '@' + baseController.getRelativeUrl();
};

baseController.multiSelectedRow = function(table, status) {
    var cookieName = baseController.getMultiSelectCookieName(table);

    localStorage.setItem(cookieName, status);

    $(table).jtable('multiselect', status);
};

baseController.isTableMultiSelect = function(table) {
    var cookieName = baseController.getMultiSelectCookieName(table);
    var multi = localStorage.getItem(cookieName);

    if (multi != null) {
        if (multi == 'true')
            return true;
        else
            return false;
    }

    return null;
};

baseController.changeMultiSelectedRow = function(table, toggle) {
    if (baseController.isTableMultiSelect(table)) {
        $(toggle).bootstrapToggle('on');
    } else {
        $(toggle).bootstrapToggle('off');
    }

    $(toggle).change(function() {
        if (baseController.isTableMultiSelect(table)) {
            baseController.multiSelectedRow(table, false);
        } else {
            baseController.multiSelectedRow(table, true);
        }
    });
};

baseController.getRelativeUrl = function() {
    var url = document.URL;

    return url.replace(siteUrl, '');
};

baseController.calculateTotal = function(table, field) {
    var records = $(table).jtable('getAllRecords');
    var total = 0;

    $.each(records, function(index, value) {
        total += parseInt(value[field]);
    });

    return total;
};

baseController.refreshDropdown = function(options) {
    var defaults = {
        url: null,
        target: null,
        data: [],
        multiselect: true
    };

    options = $.extend(defaults, options);

    var ajaxOptions = {
        url: options.url,
        type: 'post',
        dataType: 'json',
        data: options.data
    };

    $.ajax(ajaxOptions).done(function(response) {
        $(options.target).empty();

        $.each(response, function(index, value) {
            $(options.target).append('<option value="' + value.id + '">' + value.value + '</option>');
        });

        if (options.multiselect)
            $(options.target).multiselect('rebuild');

        if (response.length == 0) {
            if (options.multiselect)
                $(options.target).multiselect('disable');
            else
                $(options.target).attr('disabled', 'disabled');
        } else {
            if (options.multiselect)
                $(options.target).multiselect('enable');
            else
                $(options.target).removeAttr('disabled', 'disabled');
        }

        if (options.multiselect)
            $(options.target + ' + .btn-group-multiselect').trigger('hidden.bs.dropdown');
    });
};

//formate una fecha
baseController.formatDate = function(date, format) {
    if (format == "dd/mm/yyyy") {
        format = format.toUpperCase();
    }
    if (format == "dd/mm/yyyy hh:mm:ss") {
        format = "DD/MM/YYYY HH:mm:ss";
    }
    if (date == null || date == '')
        return null;

    if (typeof (date) == 'undefined')
        return '';

    if (typeof (format) == 'undefined')
        return date;

    if (date.split('/').length > 1)
        return date;

    var newDate = moment(date);
    var finalDate = newDate.format(format);

    return finalDate;
};

baseController.EXECUTE_SESSION_CONTROL = true;
//Inicializa el proceso de control de sesion de usuario
baseController.userSessionControl = function(pUrl, pTimeout) {
    setInterval(function() {
        var ajaxOptions = {
            url: pUrl,
            type: 'post',
            dataType: 'json'
        };
        if (baseController.EXECUTE_SESSION_CONTROL) {
            $.ajax(ajaxOptions).done(function(response) {

                if (!response.status) {

                    baseController.EXECUTE_SESSION_CONTROL = false;

                    bootbox.dialog({
                        title: 'La sesión ha caducado',
                        message: 'Su sesión de usuario ha caducado, si desea continuar operando deberá introducir sus credenciales nuevamente.',
                        buttons: {
                            accept: {
                                label: 'Ir al login',
                                className: 'btn-primary btn-sm',
                                callback: function() {
                                    window.location = response.redirect;
                                }
                            }
                        }
                    }); // end dialog
                } //end if
            }).fail(function() {
                window.location = siteUrl;
            });
        }
    }, pTimeout);
}; 