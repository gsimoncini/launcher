filterController = {};

filterController.current = {};

filterController.data = function(filter) {
    var data = {};

    $('#' + filter + ' [data-type=multiselect]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = multiselect.removeAllOption('#' + name);

        data[name] = {type: type, value: value};
    });

    $('#' + filter + ' [data-type=dropdown]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = $(this).val();

        data[name] = {type: type, value: value};
    });

    $('#' + filter + ' [data-type=date]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = $(this).val();

        data[name] = {type: type, value: value};
    });

    $('#' + filter + ' [data-type=checkbox]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = $(this).is(':checked') ? 1 : 0;

        data[name] = {type: type, value: value};
    });

    $('#' + filter + ' [data-type=text]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = $(this).val();

        data[name] = {type: type, value: value};
    });

    $('#' + filter + ' [data-type=hidden]').each(function(index, value) {
        var name = $(this).attr('id');
        var type = $(this).attr('data-type');
        var value = $(this).val();

        if (value != '')
            value = JSON.parse(value);

        data[name] = {type: type, value: value};
    });

    return data;
};

filterController.setDefaults = function(filter) {
    $('#' + filter + ' [data-type=multiselect]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = JSON.parse($(this).attr('data-default'));

        $('#' + name).val(value);
    });

    $('#' + filter + ' [data-type=multiselect]').multiselect('refresh').change();

    $('#' + filter + ' [data-type=dropdown]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = JSON.parse($(this).attr('data-default'));

        if (value == null)
            $('#' + name).val($('#' + name + ' option:first').val());
        else
            $('#' + name).val(value);

    });

    $('#' + filter + ' [data-type=dropdown]').selectpicker('refresh').change();

    $('#' + filter + ' [data-type=date]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = $(this).attr('data-default');

        $('#' + name).datepicker('setValue', value);
    });

    $('#' + filter + ' [data-type=checkbox]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = $(this).attr('data-default');

        $('#' + name).prop('checked', value == 1 ? true : 0);
    });

    $('#' + filter + ' [data-type=checkbox]').iCheck('update');

    $('#' + filter + ' [data-type=text]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = $(this).attr('data-default');

        $('#' + name).val(value);
    });

    $('#' + filter + ' [data-type=hidden]').each(function(index, value) {
        var name = $(this).attr('id');
        var value = $(this).attr('data-default');

        if (value != '')
            value = JSON.parse(value);

        $('#' + name).val(value);
    });
};

filterController.apply = function(filter, table, url) {
    var data = {};

    $.each(filter, function(index, value) {
        var options = filterController.data(value);

        filterController.current[value] = options;

        $.extend(data, options);
    });

    if (url == null || url == '') {
        $(table).jtable('load', data);

        filterController.showFiltersBoxes(filter, baseController.sortObjectProperties(data));
    } else {
        $.post(url, data).done(function() {
            $(table).jtable('load');

            filterController.showFiltersBoxes(filter, baseController.sortObjectProperties(data));
        });
    }
};

filterController.remove = function(filter, table, url) {
    var target = filter[0];

    filterController.setDefaults(target);
    filterController.apply(filter, table, url);
};

filterController.showFiltersBoxes = function(filter, data) {
    $.each(filter, function(index, value) {
        filterController.showFiltersBox(value, data);
    });
};

filterController.showFiltersBox = function(name, data) {
    var filterText = '';

    $.each(data, function(index, element) {
        if (element.value != null && element.value.length > 0) {
            var label = $('label[for=' + index + ']').text();

            filterText += '<span data-filter="' + index + '" data-type="' + element.type + '">' + label + '</span>';
        }
    });

    $('.show-filters-box-' + name + ' div.filter span').html(filterText == '' ? language.line('label_no') : filterText);

    $('.show-filters-box-' + name + ' div:last-child span span').each(function() {
        var filter = $(this).attr('data-filter');
        var filterType = $(this).attr('data-type');

        var selected;
        var title = '';

        switch (filterType) {
            case 'multiselect':
                selected = multiselect.removeAllOption('#' + filter);

                $.each(selected, function(index, value) {
                    var text = $('#' + filter + ' option[value=' + value + ']').text();

                    title += (index == 0 ? '' : ', ') + text;
                });
                break;
            case 'dropdown':
                selected = $('#' + filter).val();
                title = $('#' + filter + ' option[value=' + selected + ']').text();
                break;
            case 'date':
                selected = $('#' + filter).val();
                title = selected;
                break;
            case 'checkbox':
                selected = $('#' + filter).is(':checked');
                title = selected ? language.line('label_yes') : language.line('label_no');
                break;
            case 'text':
                selected = $('#' + filter).val();
                title = selected;
                break;
        }

        $('.show-filters-box-' + name + ' span[data-filter=' + filter + ']').tooltip({title: title, placement: 'bottom'});
    });
};
