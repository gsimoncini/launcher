
/************************************************************************
 * Change Row index extension for jTable                                           
 * Author: Bombieri Mirco                                           
 * Rev. 0.1
 * Date: 2013-05-21
 *************************************************************************/


/************************************************************************
 * ORDER ROWS extension for jTable                                          *
 *************************************************************************/
(function($) {
    //extension members
    $.extend(true, $.hik.jtable.prototype, {
        /* Up row
         *************************************************************************/
        upRow: function(pKey) {
            var $row = this.getRowByKey(pKey);
            var actualIndex = this._findRowIndex($row);
            if (actualIndex > 0) {
                this._$tableRows.splice(actualIndex, 1);
                this._changeRowIndex($row, actualIndex - 1);
            }
        },
        /* Down row
         *************************************************************************/
        downRow: function(pKey) {
            var $row = this.getRowByKey(pKey);
            var actualIndex = this._findRowIndex($row);
            if (actualIndex < (this._$tableRows.length - 1)) {
                this._$tableRows.splice(actualIndex, 1);
                this._changeRowIndex($row, actualIndex + 1);
            }
        },
        /* Set First
         *************************************************************************/
        firstRow: function(pKey) {
            var $row = this.getRowByKey(pKey);
            var actualIndex = this._findRowIndex($row);
            if (actualIndex > 0) {
                this._$tableRows.splice(actualIndex, 1);
                this._changeRowIndex($row, 0);
            }
        },
        /* Set Last
         *************************************************************************/
        lastRow: function(pKey) {
            var $row = this.getRowByKey(pKey);
            var actualIndex = this._findRowIndex($row);
            if (actualIndex < (this._$tableRows.length - 1)) {
                this._$tableRows.splice(actualIndex, 1);
                this._changeRowIndex($row, this._$tableRows.length);
            }
        },
        /* Set Last
         *************************************************************************/
        _changeRowIndex: function($row, pIndex) {

            if (pIndex == this._$tableRows.length) {
                //add as last row
                this._$tableBody.append($row);
                this._$tableRows.push($row);
            } else if (pIndex == 0) {
                //add as first row
                this._$tableBody.prepend($row);
                this._$tableRows.unshift($row);
            } else {
                //insert to specified index
                this._$tableRows[pIndex - 1].after($row);
                this._$tableRows.splice(pIndex, 0, $row);
            }

            this._refreshRowStyles();
        }
    });

})(jQuery);