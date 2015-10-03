
/************************************************************************
 * Live SORT extension for jTable                                           
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2014-05-21
 *************************************************************************/


/************************************************************************
 * SORTING extension for jTable                                          *
 *************************************************************************/
(function($) {
    //Reference to base object members
    var base = {
        _onRecordsLoaded: $.hik.jtable.prototype._onRecordsLoaded,
        _onSortFinished: $.hik.jtable.prototype._onSortFinished
    };
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        options: {
            loadingAnimationDelay: 50,
            messages: {
                sortingMessage: 'Sorting'
            }
        },
        //Permite invocar el ordenamiento por multiples columnas desde Javascript
        sortTableByColumns: function(pArray) {
            this._lastSorting = pArray;
            this._sortTableByColumn();
        },
        //Permite invocar el ordenamiento por columna desde Javascript
        sortTableByColumn: function(pFieldName, pOrder) {
            this._lastSorting.push({
                'fieldName': pFieldName,
                sortOrder: pOrder
            });
            this._sortTableByColumn();
        },
        getCurrentSort: function() {
            return this._lastSorting;
        },
        //SOBRESCRIBE EL METODO ORIGINAL para ordenar
        _sortTableByColumn: function($columnHeader) {
            var self = this;
            self._showBusy(self.options.messages.sortingMessage);

            //update sort order in column header
            if (typeof ($columnHeader) != 'undefined')
                self._changeColumnHeadSortOrder($columnHeader);

            //Prepare array with sort criteria
            var sortParam = '{';
            $.each(this._lastSorting, function(i, value) {
                if (i > 0)
                    sortParam += ', ';
                sortParam += '"' + value.fieldName + '":"' + value.sortOrder + '"';
            });
            sortParam += '}';
            sortParam = eval('(' + sortParam + ')');

            //Generate array with data to sort
            var records = new SorteableArray();
            var recordsToShow;

            //Cuando funciona con livedata
            if (self.options.listActionMode != undefined && self.options.listActionMode == 1) {
                $.each(self._listModel, function(i, value) {
                    records.push(value);
                });
                records.keySort(sortParam);
                //Coloca todos los records ordenados en el listmodel
                self._listModel = records;
                //proceso los elementos segun tienen o no filtro
                var recordsResponse = self._getLivedataRecordsToShow();
                //Obtengo el array de records para paginar
                recordsToShow = recordsResponse.recordsToShow;
                /*paging*/
                if (self.options.paging) {
                    var jsParams = self._createJtParamsForLoading();
                    var start = parseInt(jsParams.jtStartIndex);
                    var end = parseInt(start) + parseInt(jsParams.jtPageSize);
                    //Sort the array data 
                    recordsToShow = recordsToShow.slice(start, end);
                }
            }
            else {
                $.each(self._$tableRows, function(i, value) {
                    records.push($(value).data('record'));
                });
                //Sort the array data 
                records.keySort(sortParam);
                recordsToShow = records;
            }

            //Remove visual rows
            this._removeAllRows('reloading');

            //Add sorted records to table
            self._addRecordsToTable(recordsToShow);

            if (typeof (self._refreshColoreable) == 'function')
                self._refreshColoreable();

            self._onSortFinished();

            this._hideBusy();

            if (this.options.autoSaveSortState && typeof (this._saveSortState) !== 'undefined') {
                this._saveSortState();
            }
        },
        /* Overrides _onRecordsLoaded method to to render footer row.
         *************************************************************************/
        _onRecordsLoaded: function(data) {
            base._onRecordsLoaded.apply(this, arguments);
        },
        _changeColumnHeadSortOrder: function($columnHeader) {
            //Remove sorting styles from all columns except this one
            if (this._lastSorting.length == 0) {
                $columnHeader.siblings().removeClass('jtable-column-header-sorted-asc jtable-column-header-sorted-desc');
            }

            //If current sorting list includes this column, remove it from the list
            for (var i = 0; i < this._lastSorting.length; i++) {
                if (this._lastSorting[i].fieldName == $columnHeader.data('fieldName')) {
                    this._lastSorting.splice(i--, 1);
                }
            }

            //Sort ASC or DESC according to current sorting state
            if ($columnHeader.hasClass('jtable-column-header-sorted-asc')) {
                $columnHeader.removeClass('jtable-column-header-sorted-asc').addClass('jtable-column-header-sorted-desc');
                this._lastSorting.push({
                    'fieldName': $columnHeader.data('fieldName'),
                    sortOrder: 'desc'
                });
            } else {
                $columnHeader.removeClass('jtable-column-header-sorted-desc').addClass('jtable-column-header-sorted-asc');
                this._lastSorting.push({
                    'fieldName': $columnHeader.data('fieldName'),
                    sortOrder: 'asc'
                });
            }
        },
        _onSortFinished: function() {
            this._trigger('sortFinished', null, {});
        }
    });

})(jQuery);


/*
 * Extensión de Array para ordenar un elemento
 */
var SorteableArray = function() {
};
SorteableArray.prototype = new Array;
SorteableArray.prototype.keySort = function(keys) {

    keys = keys || {};

// via
// http://stackoverflow.com/questions/5223/length-of-javascript-object-ie-associative-array
    var obLen = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key))
                size++;
        }
        return size;
    };

// avoiding using Object.keys because I guess did it have IE8 issues?
// else var obIx = function(obj, ix){ return Object.keys(obj)[ix]; } or
// whatever
    var obIx = function(obj, ix) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (size == ix)
                    return key;
                size++;
            }
        }
        return false;
    };

    var keySort = function(a, b, d) {
        d = d !== null ? d : 1;

        //Si es nulo lo coloco ultimo
        if (a == null)
            return 1 * d;

        if (a == b)
            return 0;

        //Clean HTML Tags and Commas for Numbers
        if ((typeof (a) == 'string') && (a != null))
            a = a.replace(/(<([^>]+)>)/ig, "").replace(',', '');
        if ((typeof (b) == 'string') && (b != null))
            b = b.replace(/(<([^>]+)>)/ig, "").replace(',', '');

        if (a == b)
            return 0;

        if (!isNaN(Number(a))) {
            return parseFloat(a) > parseFloat(b) ? 1 * d : -1 * d;
        }
        else {
            return a > b ? 1 * d : -1 * d;
        }
    };

    var KL = obLen(keys);

    if (!KL)
        return this.sort(keySort);

    for (var k in keys) {
        // asc unless desc or skip
        keys[k] =
                keys[k] == 'desc' || keys[k] == -1 ? -1
                : (keys[k] == 'skip' || keys[k] === 0 ? 0
                        : 1);
    }

    this.sort(function(a, b) {
        var sorted = 0, ix = 0;

        while (sorted === 0 && ix < KL) {
            var k = obIx(keys, ix);
            if (k) {
                var dir = keys[k];
                sorted = keySort(a[k], b[k], dir);
                ix++;
            }
        }
        return sorted;
    });
    return this;
};

