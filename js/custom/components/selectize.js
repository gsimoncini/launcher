$(document).ready(function() {
    $.extend(Selectize.prototype, {
        disable: function() {
            var self = this;
            self.isDisabled = true;
            self.lock();
        },
        enable: function() {
            var self = this;
            self.isDisabled = false;
            self.unlock();
        }
    });
});