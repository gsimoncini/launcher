/*
 Autor: Mirco Bombieri
 07-04-2015
 
 Componente que permite manipular la lectura de códigos de barras y busqueda de objetos a partir del mismo
 
 */

function BarcodeReader() {

    /* ######## VARIABLES DE INSTANCIA ######## */

    this.options = {
        message_selector: '#result-message',
        panel_selector: '#panel',
        yellow_class: 'background-yellow',
        red_class: 'background-red',
        orange_class: 'background-orange',
        action_url: '', //URL de confirmacion de cambio
        search_url: '', //URL para busqueda
        barcode_selector: '#barcode', //Selector visual de donde obtener la lectura
        id_selector: '#order_id',
        origin_states_id: [],
        destination_state_id: null,
        objects_list: [],
        barcode_attribute: 'barcode',
        automatic_execute: false,
        load_data_callback: function(pObject, pValidation) {
        },
        clean_callback: function() {
        },
        finish_callback: function() {

        },
        table_target: '#table',
        catalog_list: null,
        extra_view_target: '.extra-view',
        pre_send_hook: function() {

        }
    };

    var self = this;

    this.record = null;
    this.last_reads = [];
    this.extra_data_to_send = {};

    this.lastValidation = null;
    this.alerType = '';

    this.initialize = function(pOptions) {
        this.options = $.extend(this.options, pOptions);
        this.registerEvents();
        this.initializeLastReadsTable();
    };
    //Registra los eventos
    this.registerEvents = function() {
        var self = this;
        $(this.getBarcodeSelector()).keyup(function(event) {
            if (event.keyCode == 13) {
                //despacho la busqueda
                self.searchHandler();

            }
        });
    };

    //Limpia la vista
    this._clean = function() {
        $(this.getPanelSelector()).removeClass(this.options.yellow_class + ' ' + this.options.orange_class + ' ' + this.options.red_class);
        this.options.clean_callback();
//        this.setMessage('');
        this.record = null;
    };

    //Dispara la busqueda
    this.searchHandler = function(pField) {
        this._clean();

        var barcode = $(this.getBarcodeSelector()).val();
        var result = this.searchInList(barcode);

        //Si no lo encuentra en la lista salgo a buscarlo por AJAX
        if (result == null) {

//            this.setMessage('Buscando...');
            result = this.searchByAjax(barcode, pField);

        }

        this.lastValidation = this._parseResult(result);

        //cargo los datos a la vista
        if (result != null)
            this.options.load_data_callback(result, this.lastValidation.status);

        //Proceso por color
        if (this.lastValidation.message != '')
            this.setMessage(this.lastValidation.message);
        if (!this.lastValidation.status) {
            $(this.getPanelSelector()).addClass(this.lastValidation.color);
            $(this.getBarcodeSelector()).focus().select();
            return;
        }

        this.record = result;
        this._okFocusSet();
    };

    //Coloca el foco por OK
    this._okFocusSet = function() {
        this.setBarcodeFocus();
    };

    //Determina el color segun el resultado
    this._parseResult = function(pResult) {
        $(this.getMessageSelector()).addClass('hidden');
        var validationResult = {status: false, message: ''};
        if (pResult == null) {
            //No se encontró resultado
            validationResult.color = this.options.red_class;
            validationResult.message = 'No se encuentra orden con el código dado.';
            this.setMessage('No se encuentra orden con el código dado.', false);
            return validationResult;
        }

        validationResult.status = true;
        return validationResult;
    };

    //Ejecuta el cambio de estado
//    this.executeChangeStatus = function() {
//        var self = this;
//        if (this.record != null) {
//            var record = this.record;
//
//            self.options.pre_send_hook();
//
//            var dataToSend = $.extend(self.extra_data_to_send, {order_id: record.id, origin_states_id: this.options.origin_states_id, destination_state_id: this.options.destination_state_id});
//
//            var ajaxOptions = {
//                url: this.options.action_url,
//                type: 'post',
//                dataType: 'json',
//                data: dataToSend
//            };
//            $.ajax(ajaxOptions).done(function(data) {
//                if (data.status) {
//                    //actualizo en la lista
//                    self._updateStatus(data);
//                    self.setMessage('Listo!', data.status);
//                    $(self.getBarcodeSelector()).focus().val('');
//                    self.options.finish_callback();
//                } else {
//                    self.setMessage(data.message, data.status);
//                }
//            }).fail(function() {
//                self.setMessage('Ocurrio un error. La aplicación no responde.', false);
//
//            });
//
//        }
//    };

    //Busca el campo
    this.searchByAjax = function(pBarcode, pField) {
        var result = null;
        if (pBarcode != null) {
            var ajaxOptions = {
                url: this.options.search_url,
                type: 'post',
                dataType: 'json',
                async: false,
                data: {value: pBarcode, field: pField}
            };
            $.ajax(ajaxOptions).done(function(data) {
                if (data.status)
                    result = data.object;
                else
                    result = null;
            }).fail(function() {
                result = null;
            });

        }
        return result;
    };

    this._updateStatus = function(pRecord) {
        var self = this;
        var toAdd = self.record;
        toAdd.origin_state = toAdd.status_name;
        toAdd.status_name = pRecord.status_name;
        self.addLastRead(toAdd);
        return toAdd;
    };

    //Busca un elemento por barcode
    this.searchInList = function(pBarcode) {
        var self = this;
        var result = null;
        $.each(this.getList(), function(ix, value) {
            if (value[self.getBarcodeAttr()] == pBarcode) {
                result = value;
            }
        });
        return result;
    };

    this.addLastRead = function(pRecord) {
        var last = pRecord;
        console.log(pRecord);
        last.destination_state = pRecord.status_name;
        this.last_reads.unshift(last);
        $(this.options.table_target).jtable('load');
    };

    this.lastReadsList = function() {
        return this.last_reads;
    };

    this.initializeLastReadsTable = function() {
        var self = this;
        $(this.options.table_target).jtable({
            tableId: self.options.table_target,
            footer: false,
            paging: false,
            pageSize: 25,
            listActionMode: 1,
            selecting: true, //Enable selecting
            multiselect: false, //Allow multiple selecting
            selectingCheckboxes: false, //Show checkboxes on first column
            sorting: false,
            messages: table.messages,
            actions: {
                listAction: function() {
                    var result = {};
                    result.Result = 'OK';
                    result.Records = self.lastReadsList();
                    result.TotalRecordCount = self.lastReadsList().lenght;
                    return result;
                }
            },
            fields: {
                barcode: {
                    title: 'Barcode',
                    width: '1%',
                    listClass: 'text-center',
                    key: true
                },
                purchase_order_number: {
                    title: 'Tracker',
                    width: '1%',
                    listClass: 'text-center'
                },
                destination_name: {
                    title: 'Destinatario',
                    width: '1%',
                    listClass: 'text-left'
                },
                address: {
                    title: 'Domicilio',
                    width: '1%',
                    listClass: 'text-left'
                },
                origin_state: {
                    title: 'Estado origen',
                    width: '1%',
                    listClass: 'text-center'
                },
                destination_state: {
                    title: 'Estado destino',
                    width: '1%',
                    listClass: 'text-center'
                }
            }
        });
    };


    this.setBarcodeFocus = function() {
        $(this.getBarcodeSelector()).focus().select();
    };
    this.getList = function() {
        return this.options.objects_list;
    };
    this.getBarcodeAttr = function() {
        return this.options.barcode_attribute;
    };
    this.getBarcodeSelector = function() {
        return this.options.barcode_selector;
    };
    this.getPanelSelector = function() {
        return this.options.panel_selector;
    };
    this.getMessageSelector = function() {
        return this.options.message_selector;
    };
    this.setMessage = function(pMsg, pStatus) {
        var color = '#000000';
        var alertType = 'alert-success';

        if (pStatus != undefined)
        {
            if (pStatus) {

                color = '#008800';
                alertType = 'alert-success';
            } else {
                color = '#cc0000';
                alertType = 'alert-danger';
            }
        }

        $(this.getMessageSelector()).removeClass('hidden');
        $(this.getMessageSelector()).addClass(alertType);
        $(this.getMessageSelector() + ' p').text(pMsg);/*.css('color', color);*/
        $('html, body').stop().animate({
            scrollTop: ($(this.getMessageSelector()).offset().top) - 50
        }, 500);
    };
    this.setExtraDataToSend = function(pData) {
        this.extra_data_to_send = pData;
    };


    //Inicializa
    {

    }
}
    