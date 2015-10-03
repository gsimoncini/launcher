
/************************************************************************
 * Client Data Management extension for jTable                                           
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2014-07-22
 *************************************************************************/


/************************************************************************
 * Client Data Management extension for jTable                                          *
 *************************************************************************/
(function($) {
    //Reference to base object members
    var base = {
        _reloadTable: $.hik.jtable.prototype._reloadTable,
        _changePage: $.hik.jtable.prototype._changePage,
        _changePageSize: $.hik.jtable.prototype._changePageSize
    };
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        options: {
            listActionMode: 2 //1 = Client | 2 = Server (AJAX)
        },
        _listModel: new Array(), //Model to store data table data
        //
        _reloadTable: function(completeCallback) {
            var self = this;
            if (self.options.listActionMode != undefined && self.options.listActionMode == 1)
                self.fullReload(true, completeCallback);
            else
                self.fullReload(false, completeCallback);
        },
        fullReload: function(pClientOnly, completeCallback) {
            var self = this;

            var completeReload = function(data) {
                self._hideBusy();

                //Show the error message if server returns error
                if (data.Result != 'OK') {
                    self._showError(data.Message);
                    return;
                }

                self._listModel = data.Records;
                if (pClientOnly)
                    self._refreshPage(self._currentPageNo);
                else {
                    self._removeAllRows('reloading');
                    self._addRecordsToTable(data.Records);
                    self._onRecordsLoaded(data);
                }
                if (typeof (this.options) != 'undefined') {
                    if (this.options.footer)
                        this._updateTableFoot();
                }

                //Call complete callback
                if (typeof (completeCallback) == 'function') {
                    completeCallback();
                }
            };

            self._showBusy(self.options.messages.loadingMessage, self.options.loadingAnimationDelay); //Disable table since it's busy
            self._onLoadingRecords();

            //listAction may be a function, check if it is
            if ($.isFunction(self.options.actions.listAction)) {

                //Execute the function

                if (pClientOnly) {
                    //Cargo todos los items, y luego aplico paginado en JS
                    var funcResult = self.options.actions.listAction(self._lastPostData);
                } else
                    var funcResult = self.options.actions.listAction(self._lastPostData, self._createJtParamsForLoading());

                //Check if result is a jQuery Deferred object
                if (self._isDeferredObject(funcResult)) {
                    funcResult.done(function(data) {
                        completeReload(data);
                    }).fail(function() {
                        self._showError(self.options.messages.serverCommunicationError);
                    }).always(function() {
                        self._hideBusy();
                    });
                } else { //assume it's the data we're loading
                    completeReload(funcResult);
                }

            } else { //assume listAction as URL string.

                //Generate URL (with query string parameters) to load records
                var loadUrl = self._createRecordLoadUrl();

                //Load data from server using AJAX
                self._ajax({
                    url: loadUrl,
                    data: self._lastPostData,
                    success: function(data) {
                        completeReload(data);
                    },
                    error: function() {
                        self._hideBusy();
                        self._showError(self.options.messages.serverCommunicationError);
                    }
                });

            }
        },
        refresh: function() {
            this._refreshPage(this._currentPageNo);
        },
        _refreshPage: function(pageNo) {
            var self = this;
            /*paging*/
            self._showBusy(self.options.messages.loadingMessage, self.options.loadingAnimationDelay);
            var start = parseInt(pageNo - 1) * parseInt(this.options.pageSize);
            var end = parseInt(start) + parseInt(this.options.pageSize);

            //Obtengo los records por filtrado
            var recordsR = self._getLivedataRecordsToShow();
            //records a mostrar
            var recordsToShow = recordsR.recordsToShow;
            //total de registros a mostrar
            var total = recordsR.total;
            //Pagino
            if (this.options.paging)
                recordsToShow = recordsToShow.slice(start, end);

            //Re-generate table rows
            this._removeAllRows('reloading');
            if (this.options.paging) {
                this._createPagingInfo();
                this._createPagingList();
            }
            this._addRecordsToTable(recordsToShow);
            self._onRecordsLoaded({Records: recordsToShow, TotalRecordCount: total});
            self._hideBusy();
        },
        //Devuelve todos los records a mostrar segun si tiene filtro o no
        _getLivedataRecordsToShow: function() {
            var self = this;
            var recordsToShow;
            var total = 0;

            if ($.isFunction(self._filterMethod)) {
                //obtengo por filtro
                recordsToShow = self._getRecordsToShow();
                total = recordsToShow.length;
            } else {
                //Proceso todos sin extension de livefilter
                total = self._listModel.length;
                recordsToShow = self._listModel;
            }

            return {recordsToShow: recordsToShow, total: total};
        },
        /* Changes current page to given value.
         *************************************************************************/
        _changePage: function(pageNo) {
            pageNo = this._normalizeNumber(pageNo, 1, this._calculatePageCount(), 1);
            if (pageNo == this._currentPageNo) {
                this._refreshGotoPageInput();
                return;
            }

            this._currentPageNo = pageNo;
            this._refreshPage(pageNo);
        },
        _changePageSize: function(pageSize) {

            if (pageSize == this.options.pageSize) {
                return;
            }

            this.options.pageSize = pageSize;

            //Normalize current page
            var pageCount = this._calculatePageCount();
            if (this._currentPageNo > pageCount) {
                this._currentPageNo = pageCount;
            }
            if (this._currentPageNo <= 0) {
                this._currentPageNo = 1;
            }

            //if user sets one of the options on the combobox, then select it.
            var $pageSizeChangeCombobox = this._$bottomPanel.find('.jtable-page-size-change select');
            if ($pageSizeChangeCombobox.length > 0) {
                if (parseInt($pageSizeChangeCombobox.val()) != pageSize) {
                    var selectedOption = $pageSizeChangeCombobox.find('option[value=' + pageSize + ']');
                    if (selectedOption.length > 0) {
                        $pageSizeChangeCombobox.val(pageSize);
                    }
                }
            }

            this._savePagingSettings();
            this._refreshPage(this._currentPageNo);
        },
        getAllRecords: function() {
            return this._listModel;
        },
        removeRecord: function(key, value) {
            var self = this;

            $.each(this._listModel, function(index, element) {
                if (element[key] == value) {
                    self._listModel.splice(index, 1);
                    return false;
                }
            });
        }
    });

})(jQuery);