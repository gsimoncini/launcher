language = {};

language.load = function() {
    var ajaxOptions = {
        url: baseLoadLanguageUrl,
        dataType: 'json',
        async: false
    };

    $.ajax(ajaxOptions).done(function(response) {
        language.dictionary = response;
    });
};

language.line = function(key) {
    return language.dictionary[key];
};
