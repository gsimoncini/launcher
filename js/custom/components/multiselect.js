multiselect = {};

multiselect.defaultSettings = function() {
    var settings = {
        disableIfEmpty: true,
        numberDisplayed: 0,
        selectedClass: null,
        includeSelectAllOption: true,
        selectAllText: language.line('action_select_all'),
        nonSelectedText: language.line('action_select'),
        nSelectedText: language.line('action_selected'),
        buttonContainer: '<div class="btn-group-multiselect"/>',
        buttonClass: 'btn btn-default btn-block btn-sm',
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        filterPlaceholder: language.line('label_search'),
        templates: {
            filter: '<li class="multiselect-item filter"><div class="input-group input-group-sm"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control input-sm multiselect-search" type="text"></div></li>'
        },
        buttonText: function(options, select) {
            return (options.length == 0) ? this.nonSelectedText.toUpperCase() + ' <b class="caret"></b>' : options.length + ' ' + this.nSelectedText.toUpperCase() + ' <b class="caret"></b>';
        },
        onDropdownShow: function(event) {
            var multiselectSize = function() {
                var targetOffset = $(event.target).offset().top;
                var positionInWindow = targetOffset - $(window).scrollTop();
                var height = $(window).height() - positionInWindow - 44;

                $(event.target).find('.multiselect-container').css({
                    'max-height': height + 'px',
                    'overflow': 'auto'
                });
            };

            multiselectSize();

            $(window).off('resize.multiselectSize').on('resize.multiselectSize', multiselectSize);
            $(window).off('scroll.multiselectSize').on('scroll.multiselectSize', multiselectSize);
        }
    };

    return settings;
};

//Inicializa los multiselect
multiselect.initialize = function() {
    $('.multi-select').multiselect(multiselect.defaultSettings());
};

//Quita la opción 'Seleccionar todos' de los multiselect
multiselect.removeAllOption = function(element) {
    var values = $(element).val();

    if ($.inArray('multiselect-all', values) > -1)
        values.splice($.inArray('multiselect-all', values), 1);

    return values;
};

//Deseleccionar todos los elementos de un multiselect
multiselect.deselectAll = function(element) {
    var options = $(element).find('option:selected');

    if (options.length > 0) {
        options.prop('selected', false);
        $(element).multiselect('refresh');
    }
};

//Actualiza un multiselect
multiselect.refresh = function(element) {
    var options = $(element).find('option');

    $(element).multiselect('rebuild');
    $(element).multiselect(options.length > 0 ? 'enable' : 'disable');
};
