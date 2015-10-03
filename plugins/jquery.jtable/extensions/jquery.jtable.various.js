
/************************************************************************
 * VARIOUS extension for jTable                                           *
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2013-05-21
 *************************************************************************/

/************************************************************************
 * UNSELECT extension for jTable                                         *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Makes row/rows 'unselected'.
         *************************************************************************/
        unselectRows: function() {
            this._deselectRows(this._getSelectedRows());
            this._onSelectionChanged();
        }
    });

})(jQuery);


/************************************************************************
 * COLUMN extension for jTable                                         *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Allow change column title.
         *************************************************************************/
        changeColumnTitle: function(columnName, columnTitle) {

            var columnIndex = this._columnList.indexOf(columnName);

            //Hide or show the column if needed
            var columnIndexInTable = this._firstDataColumnOffset + columnIndex + 1;
            this._$table
                    .find('>thead >tr >th:nth-child(' + columnIndexInTable + ')').html(columnTitle);
        }
    });

})(jQuery);

/************************************************************************
 * COLUMN extension for jTable                                         *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Allow know visibility for a column title.
         *************************************************************************/
        columnVisibility: function(columnName) {
            var field = this.options.fields[columnName];
            if (field != null)
                return field.visibility;
            return null;
        },
        addColumns: function(columns) {
            var self = this;

            $.each(columns, function(index, value) {
                self._insertColumn(value);
            });

            self._regenerateTable();
        },
        removeColumns: function(columns) {
            var self = this;

            $.each(columns, function(index, value) {
                self._removeColumn(value);
            });

            self._regenerateTable();
        },
        _insertColumn: function(value) {
            var self = this;

            var name = value.name;
            var options = value.options;

            self._normalizeFieldOptions(name, options);
            self._fieldList.push(name);

            if (options.list != false && options.type != 'hidden')
                self._columnList.push(name);

            self.options.fields[name] = options;
        },
        _removeColumn: function(name) {
            var self = this;

            var fieldListIndex = self._fieldList.indexOf(name);
            var columnListIndex = self._columnList.indexOf(name);

            self._fieldList.splice(fieldListIndex, 1);
            self._columnList.splice(columnListIndex, 1);

            delete self.options.fields[name];
        },
        _removeTableHead: function() {
            this._$table.find('thead').empty().remove();
        },
        _removeTableBody: function() {
            this._$table.find('tbody').empty().remove();
        },
        _regenerateTable: function() {
            var self = this;

            self._removeTableHead();
            self._removeTableBody();

            self._createTableHead();
            self._createTableBody();

            self.refresh();
        }
    });

})(jQuery);


/************************************************************************
 * COLUMN extension for jTable                                          *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Permite cambiar el valor de una celda temporalmente.
         *************************************************************************/
        changeCellValue: function(columnName, keyValue, newValue) {
            //Look for column index
            var columnIndex = this._columnList.indexOf(columnName);
            var columnIndexInTable = this._firstDataColumnOffset + columnIndex + 1;

            //Set value on the model
            var $row = this._$table.find('tr[data-record-key=' + keyValue + ']');
            var record = $row.data('record');

            if (typeof (record) == 'undefined')
                return;

            record[columnName] = newValue;
            $row.data('record', record);

            //Search row by keyValue and column index to change html text in cell.
            var displayValue = this._getDisplayTextForRecordField(record, columnName);
            this._$table
                    .find('tr[data-record-key=' + keyValue + '] >td:nth-child(' + columnIndexInTable + ')').html(displayValue);
        }
    });

})(jQuery);



/************************************************************************
 * CLOSE CHILD TABLE BY KEY extension for jTable                                         *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Close child table by key
         *************************************************************************/
        closeChildRowByKey: function(pKey) {
            var $row = this.getRowByKey(pKey);
            if ($row != null)
                this.closeChildRow($row);
        }
    });

})(jQuery);


/************************************************************************
 * CHANGE MULTISELECT OPTION extension for jTable                                         *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Permite cambiar la multiselección en cualquier momento.
         *************************************************************************/
        multiselect: function(pValue) {
            this.options.multiselect = pValue;

            var $thead = this._$table.find('thead');

            this._addRowToTableHead($thead);
            this._deselectRows(this._getSelectedRows());

            this._$table.find('thead tr').first().empty().remove();
        }
    });

})(jQuery);