
/************************************************************************
 * AUTOSAVE STATE extension for jTable                                           *
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2013-05-21
 *************************************************************************/

/************************************************************************
 * AUTO SAVE STATE extension for jTable                                         *
 *************************************************************************/
(function($) {
    //Reference to base object members
    var base = {
        reload: $.hik.jtable.prototype.reload,
        load: $.hik.jtable.prototype.load,
        _create: $.hik.jtable.prototype._create,
        _onRecordsLoaded: $.hik.jtable.prototype._onRecordsLoaded,
        _sortTableByColumn: $.hik.jtable.prototype._sortTableByColumn,
        _onSelectionChanged: $.hik.jtable.prototype._onSelectionChanged,
        _restoreSelectionList: $.hik.jtable.prototype._restoreSelectionList,
        _changePage: $.hik.jtable.prototype._changePage
    };
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        _flag_loaded: false,
        options: {
            autoSaveChildTableState: false,
            autoSaveSortState: true
        },
        /* Overrides _create method to load last save state
         *************************************************************************/
        _create: function() {
            if (this.options.autoSaveChildTableState) {
                var self = this;
                $(window).on('beforeunload', function() {
                    self._saveNowState();
                });
            }

            base._create.apply(this, arguments);
        },
        /* Overrides reload method to save last state
         *************************************************************************/
        reload: function() {
            this._saveNowState();
            this._flag_loaded = false;
            base.reload.apply(this, arguments);
        },
        load: function() {
            this._saveNowState();
            this._flag_loaded = false;
            base.load.apply(this, arguments);
        },
        _saveNowState: function() {
            var $tr = $(this.options.tableId + ' .jtable tr.jtable-child-row[style="display: table-row;"]');
            if ($tr != null) {
                if ($tr.length > 0) {
                    var $tr = $tr.prev();
                    var index = $(this.options.tableId).jtable('rowIndex', $tr);
                    this._saveChildState(index);
                } else {
                    this._saveChildState(-1);
                }
            }
        },
        /* Overrides _onRecordsLoaded method to load last save state
         *************************************************************************/
        _onRecordsLoaded: function(data) {
            var self = this;
            base._onRecordsLoaded.apply(this, arguments);

            if (!this._flag_loaded) {
                this._flag_loaded = true;
                //Load Sort Data
                if (this.options.autoSaveSortState) {
                    var readSort = this._readSortState();
                    if (readSort != null) {
                        this._lastSorting = readSort;
                        this._sortTableByColumn();
                    } else
                        this._onSortFinished();
                }
                if (this.options.autoSaveChildTableState) {
                    var childIndex = parseInt(this._readChildState());
                    if (childIndex != -1) {
                        var trarr = $(this.options.tableId + ' .jtable-data-row').not('.jtable-child-row .jtable-data-row');
                        if (trarr != null) {
                            var $ro = trarr[childIndex];
                            $($ro).children().children('.child-opener-image').click();
                        }
                    }
                }
                var pageNo = this._readPageInfo();
                if (this.options.paging && pageNo != 0) {
                    self._changePage(parseInt(pageNo));
                }
                this._readSelections();
            }
        },
        /* Overrides  _sortTableByColumn method to save state
         *************************************************************************/
        _sortTableByColumn: function($columnHeader) {
            base._sortTableByColumn.apply(this, arguments);

            if (this.options.autoSaveSortState) {
                this._saveSortState();
            }
        },
        /* Save sort state
         *************************************************************************/
        _saveSortState: function() {
            var name = this._cookieKeyPrefix + '_jtableSortInfo';
            var value = JSON.stringify(this._lastSorting);
            localStorage.setItem(name, value);
        },
        /* Save child table state
         *************************************************************************/
        _saveChildState: function(pData) {
            var name = this._cookieKeyPrefix + '_jtableChildInfo';
            localStorage.setItem(name, pData);
        },
        /* Read sort state
         *************************************************************************/
        _readSortState: function() {
            var name = this._cookieKeyPrefix + '_jtableSortInfo';
            var value = localStorage.getItem(name);
            return $.parseJSON(value);
        },
        /* Read child table state
         *************************************************************************/
        _readChildState: function() {
            var name = this._cookieKeyPrefix + '_jtableChildInfo';
            return localStorage.getItem(name);
        },
        /* Return Index of a Row
         *************************************************************************/
        rowIndex: function($row) {
            return this._findRowIndex($row);
        },
        _onSelectionChanged: function() {
            // if (this.autoSaveSelectedRows) {
            this._saveSelections();
            //}
            base._onSelectionChanged.apply(this, arguments);
        },
        /* Save selected rows state
         *************************************************************************/
        _saveSelections: function() {
            var self = this;
            var name = this._cookieKeyPrefix + '_jtableSelectedRows';
            var values = [];
            $.each(this._getSelectedRows(), function(i, x) {
                var rec = $(x).data('record');
                values.push(self._getKeyValueOfRecord(rec));
            });
            localStorage.setItem(name, JSON.stringify(values));
        },
        /* Auto selecting rows
         *************************************************************************/
        _readSelections: function() {
            var self = this;
            var name = self._cookieKeyPrefix + '_jtableSelectedRows';
            var values = $.parseJSON(localStorage.getItem(name));

            if (values != null) {
                self._selectedRecordIdsBeforeLoad = values;
                this._restoreSelectionList();
            }

            if (self._selectedRecordIdsBeforeLoad == null || self._selectedRecordIdsBeforeLoad.length == 0)
                self._onSelectionChanged();
        },
        /* Auto save page No */
        _changePage: function() {
            base._changePage.apply(this, arguments);
            this._savePageInfo();
        },
        _savePageInfo: function() {
            var self = this;
            var name = self._cookieKeyPrefix + '_jtablePageInfo';
            var value = self._currentPageNo;
            if (value != 0)
                localStorage.setItem(name, JSON.stringify(value));
        },
        _readPageInfo: function() {
            var self = this;
            var name = self._cookieKeyPrefix + '_jtablePageInfo';
            return parseInt($.parseJSON(localStorage.getItem(name)));
        }
    });

})(jQuery);