
/************************************************************************
 * Change Look and feel extension for jTable
 * Author: Cristian Da Silva
 * Rev. 1.0
 * Date: 2015-03-10
 *************************************************************************/

(function($) {
    //reference to base object members
    var base = {
        _addRowToTableHead: $.hik.jtable.prototype._addRowToTableHead,
        _refreshRowStyles: $.hik.jtable.prototype._refreshRowStyles,
        _selectRows: $.hik.jtable.prototype._selectRows,
        _deselectRows: $.hik.jtable.prototype._deselectRows,
        _refreshSelectAllCheckboxState: $.hik.jtable.prototype._refreshSelectAllCheckboxState
    };
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Render checkboxs
         *************************************************************************/
        _renderCheckboxs: function(ubication) {
            $(this.options.tableId + ' ' + ubication + ' input[type=checkbox]').iCheck({
                checkboxClass: 'icheckbox_square-grey',
                checkedClass: 'checked'
            });
        },
        /* Add events to rows checkboxs
         *************************************************************************/
        _addRowCheckboxEvents: function() {
            var self = this;

            $(self.options.tableId + ' tbody input[type=checkbox]').on('ifChanged', function() {
                var $row = $(this).closest('tr');

                self._invertRowSelection($row);
            });
        },
        /* Add events to head checkbox
         *************************************************************************/
        _addHeadCheckboxEvents: function() {
            var self = this;

            $(self.options.tableId + ' thead input[type=checkbox]').on('ifChanged', function() {
                if (self._$tableRows.length <= 0) {
                    self._$selectAllCheckbox.attr('checked', false).iCheck('update');
                    return;
                }

                var allRows = self._$tableBody.find('>tr.jtable-data-row');

                if (self._$selectAllCheckbox.is(':checked')) {
                    self._selectRows(allRows);
                } else {
                    self._deselectRows(allRows);
                }

                self._onSelectionChanged();
            });
        },
        /* Creates header (all column headers) of the table.
         *************************************************************************/
        _addRowToTableHead: function() {
            base._addRowToTableHead.apply(this, arguments);

            this._renderCheckboxs('thead');
            this._addHeadCheckboxEvents();
        },
        /* Refreshes styles of all rows in the table
         *************************************************************************/
        _refreshRowStyles: function() {
            base._refreshRowStyles.apply(this, arguments);

            this._renderCheckboxs('tbody');
            this._addRowCheckboxEvents();
        },
        /* Makes row/rows 'selected'.
         *************************************************************************/
        _selectRows: function($rows) {
            if (!this.options.multiselect) {
                this._deselectRows(this._getSelectedRows());
            }

            $rows.addClass('jtable-row-selected');
            this._jqueryuiThemeAddClass($rows, 'ui-state-highlight');

            if (this.options.selectingCheckboxes) {
                $rows.find('>td.jtable-selecting-column input').prop('checked', true).iCheck('update');
            }

            this._refreshSelectAllCheckboxState();
        },
        /* Makes row/rows 'non selected'.
         *************************************************************************/
        _deselectRows: function($rows) {
            $rows.removeClass('jtable-row-selected ui-state-highlight');
            if (this.options.selectingCheckboxes) {
                $rows.find('>td.jtable-selecting-column input').prop('checked', false).iCheck('update');
            }

            this._refreshSelectAllCheckboxState();
        },
        /* Updates state of the 'select/deselect' all checkbox according to count of selected rows.
         *************************************************************************/
        _refreshSelectAllCheckboxState: function() {
            if (!this.options.selectingCheckboxes || !this.options.multiselect) {
                return;
            }

            var totalRowCount = this._$tableRows.length;
            var selectedRowCount = this._getSelectedRows().length;

            if (selectedRowCount == 0) {
                this._$selectAllCheckbox.prop('checked', false).iCheck('update');
            } else if (selectedRowCount == totalRowCount) {
                this._$selectAllCheckbox.prop('checked', true).iCheck('update');
            } else {
                this._$selectAllCheckbox.prop('checked', false).iCheck('update');
            }
        }
    });

})(jQuery);