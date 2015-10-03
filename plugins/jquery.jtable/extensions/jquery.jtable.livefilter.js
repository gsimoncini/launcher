
/************************************************************************
 * Live FILTER extension for jTable                                           
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2014-10-05
 *************************************************************************/

//REQUIRE LIVEDATA EXTENSION

/************************************************************************
 * FILTERING extension for jTable                                          *
 *************************************************************************/
(function($) {
    //Reference to base object members
    var base = {
        _onRecordsLoaded: $.hik.jtable.prototype._onRecordsLoaded,
        _onSortFinished: $.hik.jtable.prototype._onSortFinished
    };
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        _filterMethod: function(value, ix) {
            return true;
        },
        setFilterMethod: function(pFunction) {
            this._filterMethod = pFunction;
        },
        //Devuelve los registros a mostrar filtrandolos por filterMethod
        _getRecordsToShow: function(start, end) {
            var self = this;
            var recordsToShow = self._listModel;

            recordsToShow = $.grep(recordsToShow, function(value, ix) {
                return self._filterMethod(value, ix);
            });

            return recordsToShow;
        }
    });

})(jQuery);

