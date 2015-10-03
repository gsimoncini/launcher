
/************************************************************************
 * FOOTER extension for jTable                                           *
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2014-05-21
 *************************************************************************/
(function($) {

    //Reference to base object members
    var base = {
        _create: $.hik.jtable.prototype._create,
        _onRowsRemoved: $.hik.jtable.prototype._onRowsRemoved,
        _onRecordsLoaded: $.hik.jtable.prototype._onRecordsLoaded,
        _onRecordAdded: $.hik.jtable.prototype._onRecordAdded,
        _onRecordUpdated: $.hik.jtable.prototype._onRecordUpdated,
        _changeColumnVisibilityInternal: $.hik.jtable.prototype._changeColumnVisibilityInternal
    };

    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /************************************************************************
         * DEFAULT OPTIONS / EVENTS                                              *
         *************************************************************************/
        options: {
            footer: false
        },
        /************************************************************************
         * PRIVATE FIELDS                                                        *
         *************************************************************************/

        _$tfoot: null, //Reference to the footer area in bottom panel

        /************************************************************************
         * OVERRIDED METHODS                                                     *
         *************************************************************************/

        /* Overrides base method to create footer constructions.
         *************************************************************************/
        _create: function() {
            base._create.apply(this, arguments);
            if (this.options.footer) {
                this._createTableFoot();
            }
        },
        /* Overrides _onRecordAdded method to re-load table when a new row is created.
         *************************************************************************/
        _onRecordAdded: function(data) {
            if (this.options.footer) {
                this._updateTableFoot();
            }
            base._onRecordAdded.apply(this, arguments);
        },
        /* Overrides _onRecordUpdated method to re-load table when a new row is created.
         *************************************************************************/
        _onRecordUpdated: function($row, options) { 
            if (this.options.footer) {
                this._updateTableFoot();
            }
            base._onRecordUpdated.apply(this, arguments);
        },
        updateTableFooter: function(){
          if (this.options.footer) {
                this._updateTableFoot();
            }  
        },
        /* Overrides _onRowsRemoved method to re-load table when a row is removed from table.
         *************************************************************************/
        _onRowsRemoved: function($rows, reason) {
            if (this.options.footer && reason != 'reloading') {
                this._updateTableFoot();
            }
            base._onRowsRemoved.apply(this, arguments);
        },
        /* Overrides _onRecordsLoaded method to to render footer row.
         *************************************************************************/
        _onRecordsLoaded: function(data) {
            if (this.options.footer) {
                this._updateTableFoot(data);
            }
            base._onRecordsLoaded.apply(this, arguments);
        },
        /* Overrides _changeColumnVisibilityInternal method to change visibility a footer row
         **************************************************************************/
        _changeColumnVisibilityInternal: function(columnName, visibility) {

            var columnIndex = this._columnList.indexOf(columnName);
            if (columnIndex < 0) {
                this._logWarn('Column "' + columnName + '" does not exist in fields!');
                return;
            }

            //Check if visibility value is valid
            if (['visible', 'hidden', 'fixed'].indexOf(visibility) < 0) {
                this._logWarn('Visibility value is not valid: "' + visibility + '"! Options are: visible, hidden, fixed.');
                return;
            }

            //Get the field
            var field = this.options.fields[columnName];
            if (field.visibility == visibility) {
                return; //No action if new value is same as old one.
            }

            //Hide or show the column if needed
            var columnIndexInTable = this._firstDataColumnOffset + columnIndex + 1;
            if (field.visibility != 'hidden' && visibility == 'hidden') {
                this._$table
                        .find('>tfoot >tr >th:nth-child(' + columnIndexInTable + ')')
                        .hide();
            } else if (field.visibility == 'hidden' && visibility != 'hidden') {
                this._$table
                        .find('>tfoot >tr >th:nth-child(' + columnIndexInTable + ')')
                        .show()
                        .css('display', 'table-cell');
            }

            base._changeColumnVisibilityInternal.apply(this, arguments);
        }, 
        /************************************************************************
         * PRIVATE METHODS                                                       *
         *************************************************************************/
        _updateTableFoot: function(data) {
            var self = this;

            // If no data was provided, retrieve data from table rows
            if (data === undefined) {
                data = {Records: []};
                $.each(this._$tableRows, function(index, row) {
                    data.Records.push(row.data('record'));
                });
            }

            this._$tfoot.find('th').each(function(index, cell) {
                var $cell = $(cell);
                var fieldName = $cell.data('fieldName');
                if (fieldName && self.options.fields[fieldName].footer)
                {
                    $cell.find('span')
                            .empty()
                            .append(self.options.fields[fieldName].footer(data));
                }
            });
        },
        /* Creates footer (all column footers) of the table.
         *************************************************************************/
        _createTableFoot: function() {
            this._$tfoot = $('<tfoot></tfoot>').appendTo(this._$table);
            this._addRowToTableFoot(this._$tfoot);
        },
        /* Adds tr element to given tfoot element
         *************************************************************************/
        _addRowToTableFoot: function($tfoot) {
            var $tr = $('<tr></tr>').appendTo($tfoot);
            this._addColumnsToFooterRow($tr);
        },
        /* Adds column footer cells to given tr element.
         *************************************************************************/
        _addColumnsToFooterRow: function($tr) {
            //If set selectionWithCheckboxes, show an aditional column on footer
            if (this.options.selecting && this.options.selectingCheckboxes)
                this._createFooterCellForField(null, {})
                        .appendTo($tr);

            for (var i = 0; i < this._columnList.length; i++) {
                var fieldName = this._columnList[i];
                var $footerCell = this._createFooterCellForField(fieldName, this.options.fields[fieldName]);
                $footerCell.data('fieldName', fieldName).appendTo($tr);
            }

            if (this.options.actions.updateAction !== undefined) {
                this._createFooterCellForField(null, {})
                        .appendTo($tr);
            }

            if (this.options.actions.deleteAction !== undefined) {
                this._createFooterCellForField(null, {})
                        .appendTo($tr);
            }
        },
        /* Creates a header cell for given field.
         *  Returns th jQuery object.
         *************************************************************************/
        _createFooterCellForField: function(fieldName, field) {

            field.width = field.width || '0.5%'; //default column width: 1%.

            var $footerTextSpan = $('<span />')
                    .addClass('jtable-column-footer-text')
                    .html('');

            var $footerContainerDiv = $('<div />')
                    .addClass('jtable-column-footer-container')
                    .append($footerTextSpan);

            var $th = $('<th></th>')
                    .addClass('jtable-column-footer')
                    .addClass(field.listClass)
                    .css('width', field.width)
                    .data('fieldName', fieldName)
                    .append($footerContainerDiv);

            this._jqueryuiThemeAddClass($th, 'ui-state-default');

            //Hide column if needed
            if (field.visibility == 'hidden') {
                $th.hide();
            }

            return $th;
        }
    });

})(jQuery);