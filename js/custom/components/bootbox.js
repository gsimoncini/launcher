bootbox.alert = function(message, callback) {
    bootbox.dialog({
        title: language.line('label_information'),
        message: message,
        buttons: {
            close: {
                label: language.line('action_close'),
                className: 'btn-default btn-sm',
                callback: callback
            }
        }
    });
};

bootbox.confirm = function(message, callback) {
    bootbox.dialog({
        title: language.line('label_confirmation'),
        message: message,
        buttons: {
            accept: {
                label: language.line('action_accept'),
                className: 'btn-primary btn-sm',
                callback: callback
            },
            cancel: {
                label: language.line('action_cancel'),
                className: 'btn-default btn-sm',
                callback: function() {
                    return null;
                }
            }
        }
    });
};

bootbox.prompt = function(message, title, value, inputid, callback) {
    var html = '';
    html += '<div class="row">';
    html += '<div class="col-md-4"></div>';
    html += '<div class="col-md-4">';
    html += '<label>' + message + '</label>';
    html += '<p><input type="text" class="form-control text-center" tab-index="0" id="' + inputid + '" value="' + value + '"/></p>';
    html += '<div class="col-md-4"></div>';
    html += '</div>';

    bootbox.dialog({
        title: title,
        message: html,
        buttons: {
            accept: {
                label: language.line('action_accept'),
                className: 'btn-primary btn-sm',
                callback: callback
            },
            cancel: {
                label: language.line('action_cancel'),
                className: 'btn-default btn-sm',
                callback: function() {
                    return null;
                }
            }
        }
    });
};