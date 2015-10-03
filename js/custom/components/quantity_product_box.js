/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//productId: productId
//identifier: identificador en el html, string ,
//id: debe contener: #identifier
//class: .identifier

function QuantityProductBox(productId, identifier) {
    this.productId;
    this.identifier;
    this.pendingEntry = null;
    this.currentStock = null;
    this.noSelectedProductQuantity = null;
    this.selectedProductQuantity = null;
    this.committedQuantity = null;
    this.availableQuantity = null;
    this.arrayQuantities = [];
    this.width = 3;

    var self = this;
    {
        this.productId = productId;
        this.identifier = identifier;
    }

//obtener datos por AJAX
    this.getQuantityByProduct = function(callback) {
        var ajaxOptions = {
            type: 'post',
            url: quantityProductBoxUrl,
            dataType: 'json',
            data: {id: this.productId}
        };

        $.ajax(ajaxOptions).done(function(response) {
            self.pendingEntry = response['pending_entry'];
            self.currentStock = response['current_stock'];
            self.noSelectedProductQuantity = response['no_selected_product_quantity'];
            self.selectedProductQuantity = response['selected_product_quantity'];
            self.committedQuantity = response['committed_quantity'];
            self.availableQuantity = response['available_quantity'];

            self.arrayQuantities = response;

            if (typeof (callback) !== 'undefined')
                callback();

        }).fail(function() {
            self.errorMensaje();
        });
    };

//generar html
    this.generateHtml = function() {
        $(self.identifier).empty();
        var html = '';
        html += '<div class="row">';
        html += '   <div class="col-md-' + self.width + '">';
        html += '       <div class="panel panel-default quantity-product-box">';

        if (self.pendingEntry != '' && self.pendingEntry != 'undefined' && self.pendingEntry != null)
            html += '           <p><label>' + language.line('label_pending_entry') + ':</label> <span>  ' + self.pendingEntry + '</span></p>';

        if (self.currentStock != '' && self.currentStock != 'undefined' && self.currentStock != null)
            html += '           <p><label>' + language.line('label_current_stock') + ':</label> <span>  ' + self.currentStock + '</span></p>';

        if (self.noSelectedProductQuantity != '' && self.noSelectedProductQuantity != 'undefined' && self.noSelectedProductQuantity != null)
            html += '           <p><label>' + language.line('label_no_selected_product_quantity') + ':</label> <span>  ' + self.noSelectedProductQuantity + '</span></p>';

        if (self.selectedProductQuantity != '' && self.selectedProductQuantity != 'undefined' && self.selectedProductQuantity != null)
            html += '           <p><label>' + language.line('label_selected_product_quantity') + ':</label> <span>  ' + self.selectedProductQuantity + '</span></p>';

        if (self.committedQuantity != '' && self.committedQuantity != 'undefined' && self.committedQuantity != null)
            html += '           <p><label>' + language.line('label_committed_quantity') + ':</label> <span>  ' + self.committedQuantity + '</span></p>';

        if (self.availableQuantity != '' && self.availableQuantity != 'undefined' && self.availableQuantity != null)
            html += '           <p><label>' + language.line('label_available_quantity') + ':</label> <span>  ' + self.availableQuantity + '</span></p>';

        html += '       </div>';
        html += '   </div>';
        html += '</div>';

        $(self.identifier).append(html);


        if (self.pendingEntry == '' && self.noSelectedProductQuantity == '' && self.noSelectedProductQuantity == '' && self.selectedProductQuantity == '' && self.committedQuantity == '' && self.availableQuantity == '') {
            self.errorMensaje();
        }
        else if (self.pendingEntry == null && self.noSelectedProductQuantity == null && self.noSelectedProductQuantity == null && self.selectedProductQuantity == null && self.committedQuantity == null && self.availableQuantity == null) {
            self.errorMensaje();
        }
    };


    this.generate = function() {
        self.getQuantityByProduct(self.generateHtml);
    };

    this.refresh = function() {
        $(self.identifier).empty();
        self.generate();
    };

    this.errorMensaje = function() {
        $(self.identifier).empty();
        var html = '';
        html += '<div class="row">';
        html += '   <div class="col-md-' + self.width + ' branch-error">';
        html += '           <p><span>' + language.line('message_error_get_quantity') + '</span></p>';
        html += '   </div>';
        html += '</div>';

        $(self.identifier).append(html);
    };

    this.setWidth = function(width) {
        this.width = width;
    };
}
;
