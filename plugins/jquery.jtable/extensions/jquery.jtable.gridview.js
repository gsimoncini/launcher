
/************************************************************************
 * VARIOUS extension for jTable                                           *
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2014-11-23
 *************************************************************************/


/************************************************************************
 * GRID VIEW for jtable                          *
 *************************************************************************/
(function($) {
    //Reference to base object members
    var base = {
        load: $.hik.jtable.prototype.load,
        _create: $.hik.jtable.prototype._create,
        _setOption: $.hik.jtable.prototype._setOption,
        _createRecordLoadUrl: $.hik.jtable.prototype._createRecordLoadUrl,
        _createCellForRecordField: $.hik.jtable.prototype._createCellForRecordField,
        _addRowToTable: $.hik.jtable.prototype._addRowToTable,
        _addRow: $.hik.jtable.prototype._addRow,
        _removeRowsFromTable: $.hik.jtable.prototype._removeRowsFromTable,
        _onRecordsLoaded: $.hik.jtable.prototype._onRecordsLoaded,
        _addCellsToRowUsingRecord: $.hik.jtable.prototype._addCellsToRowUsingRecord,
        _addNoDataRow: $.hik.jtable.prototype._addNoDataRow,
        _createTableHead: $.hik.jtable.prototype._createTableHead,
        _getSelectedRows: $.hik.jtable.prototype._getSelectedRows,
        _refreshSelectAllCheckboxState: $.hik.jtable.prototype._refreshSelectAllCheckboxState,
        _createRowFromRecord: $.hik.jtable.prototype._createRowFromRecord
    };

    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        options: {
            grid: false
        },
        setGrid: function(pBool) {
            this.option.grid = pBool;
        },
        /* Constructor para generar grid en lugar de tabla
         *************************************************************************/
        _create: function() {
            base._create.apply(this, arguments);

            if (this.options.grid) {
                //Initialization
                this._normalizeFieldsOptions();
                this._initializeFields();
                this._createFieldAndColumnList();

                //Creating DOM elements
                //this._createMainContainer();
                this._createTableTitle();
                this._createToolBar();
                this._createGrid();
                this._createBusyPanel();
                this._createErrorDialogDiv();
                this._addNoDataRow();

                this._cookieKeyPrefix = this._generateCookieKeyPrefix();
            }
        },
        /* Creates the grid
         *************************************************************************/
        _createGrid: function() {

            this._$table = $('<div></div>')
                    .addClass('jtable jtable-grid panel-body')
                    .appendTo(this._$mainContainer);

            if (this.options.tableId) {
                this._$table.attr('id', this.options.tableId);
            }

            this._jqueryuiThemeAddClass(this._$table, 'ui-widget-content');

            this._createGridBody();
        },
        /* Creates tbody tag and adds to the table.
         *************************************************************************/
        _createGridBody: function() {
            this._$tableBody = $('<div></div>').addClass('jtable-grid-body').appendTo(this._$table);
        },
        /* Creates a row from given record
         *************************************************************************/
        _createRowFromRecord: function(record) {
            if (this.options.grid) {
                var $tr = $('<div></div>')
                        .addClass('jtable-grid-row col-md-3')
                        .attr('data-record-key', this._getKeyValueOfRecord(record))
                        .data('record', record);

                this._addCellsToRowUsingRecord($tr);
                return $tr;
            }
            else
                return  base._createRowFromRecord.apply(this, arguments);
        },
        /* Create a cell for given field.
         *************************************************************************/
        _createCellForRecordField: function(record, fieldName) {
            if (this.options.grid)
                return $('<span></span>')
                        .addClass(this.options.fields[fieldName].listClass)
                        .append((this._getDisplayTextForRecordField(record, fieldName)));
            else
                return  base._createCellForRecordField.apply(this, arguments);
        },
        /* Adds "no data available" row to the table.
         *************************************************************************/
        _addNoDataRow: function() {
            if (!this.options.grid)
                base._addNoDataRow.apply(this, arguments);
        },
        /* Gets all selected rows.
         *************************************************************************/
        _getSelectedRows: function() {
            if (this.options.grid)
                return this._$tableBody
                        .find('>.jtable-row-selected');
            else
                return base._getSelectedRows.apply(this, arguments);
        },
        /* Creates header (all column headers) of the table.
         *************************************************************************/
        _createTableHead: function() {
            if (!this.options.grid)
                base._createTableHead.apply(this, arguments);
        },
        /* Updates state of the 'select/deselect' all checkbox according to count of selected rows.
         *************************************************************************/
        _refreshSelectAllCheckboxState: function() {
            if (!this.options.grid)
                base._refreshSelectAllCheckboxState.apply(this, arguments);
        }
    });

})(jQuery);