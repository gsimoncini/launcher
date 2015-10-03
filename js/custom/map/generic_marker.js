/*
 * +++++++++++++++++++++++++++++
 * MARKER
 * +++++++++++++++++++++++++++++
 */

function Generic_Marker(p_lat, p_lng, p_entity, p_geo_type) {
    //CONSTANTES
    //-----------------------
    this.NORMAL_MODE = 1;
    this.LETTER_MODE = 2;

    this.COLOR_ORANGE = 'FF9326';
    this.COLOR_BLUE = '006DD9';

    //ATRIBUTOS DE INSTANCIA
    //-----------------------
    //coordendadas
    this.lat = null;
    this.lng = null;
    //Marcador de Google
    this.googleMarker = null;

    //Letra para mostrar
    this.letter = '?';
    //Color del marker
    this.bgColor = '006DD9';
    //Color del texto
    this.textColor = 'BBBBBB';
    //Modo - 1: normal - 2: con letra
    this.mode = 1;

    //Instancia objeto
    this.entity = null;
    this.transaction = null; //por compatibilidad hacia RoutingManager y RoutingMap
    this.address = '';
    this.onClickCallback = function() {
    };

    //Ventana de información
    //  this.infoWindow = null;
    //Muestro o no muestro burbuja con datos al hacer click
    this.showInfoWindow = true;

    //Instancia del mapa asociadio (Routing_Map)
    this.rmap = null;

    //Estado frente al mapa
    /*
     * NULL – por default, significa que nunca fue punteado.
     1 – Aproximado: El pedido fue colocado de manera manual aproximada.
     2 – Exacto: El pedido fue colocado de manera manual exacta.
     3 – Automático: El pedido fue punteado por google de manera exitosa.
     4 – Fuera de Zona: El pedido fue punteado por google fuera de la zona a la que corresponde.
     5 – Sin zona: El pedido no tiene zona asociada.
     6 – No punteado: El pedido no pudo ser punteado por google.
     */
    this.type = null;

    //Seleccionado (en el mapa)
    this.selected = false;

    //LOGICA DEL CONSTRUCTOR
    //-----------------------
    {
        this.lat = p_lat;
        this.lng = p_lng;
        this.type = p_geo_type;
        if (p_entity != null) {
            this.entity = p_entity;
            this.transaction = p_entity;
        }
    }

    //MÉTODOS
    //-----------------------

    //Devuelve el icono segun sus datos
    this.getIcon = function() {
        var icon_url = baseUrl + '/img/custom/map/icon/';
        if (this.mode == 1) {
            //Obtengo la configuración de iconos a utilizar desde una cookie
            var icons_family = $.cookie('type_markers_' + this.rmap.name);
            var image_icon;
            var markToUse = 3; //Por defecto entiende que es una marca AUTOMATICA.

            var image_icon = icon_url + icons_family + '_';

            switch (icons_family) {
                case 'marker':
                    {
                        if (this.transaction.mark_type == 4) {
                            image_icon = icon_url + '/out_zone.png';
                        } else {
                            markToUse = this.transaction.mark_type;
                            image_icon += markToUse + '.png';
                        }
                        break;
                    }
                case 'distributor':
                    {
                        image_icon = 'data:image/png;base64,' + this.entity.icon_distributor_type;
                        break;
                    }
                case 'route':
                    {
                        //Si tiene o no vinculado un recorrido
                        if (this.entity.route == 0) {
                            markToUse = 'without'; //sin ruta
                        } else {
                            //Ruta no seleccionada
                            markToUse = 'no_select';
                            if (this.entity.route == this.rmap.routeId) {
                                //Ruta seleccionada
                                markToUse = 'select';
                            }
                        }
                        image_icon += markToUse + '.png';
                        break;
                    }
                case 'service':
                    {
                        image_icon = 'data:image/png;base64,' + this.entity.icon_service_type;
                        break;
                    }
                case 'term':
                    {
                        image_icon += this.entity.term_id + '.png';
                        break;
                    }
                case 'route-color':
                    {
                        var col = this.entity.route_color;
                        if (typeof (col) == 'undefined' || col == null)
                            col = 'FCFCFC';
                        image_icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + (this.entity.route_number == null ? '' : this.entity.route_number) + "|" + col + "|" + this.textColor;
                        break;
                    }
                default:
                    {
                        markToUse = this.type != null ? this.type : 3;
                        icons_family = 'marker';
                        image_icon += markToUse + '.png';
                    }
            }
        } else {
            if (typeof (this.bgColor) == 'undefined')
                this.bgColor = '006DD9';
            image_icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + this.letter + "|" + this.bgColor + "|" + this.textColor;
        }

        var image = {
            url: image_icon,
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 32)
        };
        return image;
    };

    //Devuelve el marcador de google correspondiente
    this.setMap = function(p_routing_map, pContextMenuItems) {
        if (this.googleMarker == null) {
            this.rmap = p_routing_map;
            this.googleMarker = new google.maps.Marker({
                position: new google.maps.LatLng(this.lat, this.lng),
                map: p_routing_map.map,
                icon: this.getIcon(),
                title: this.address
            });
            p_routing_map.addMarker(this);
        }
        this.googleMarker.setMap(p_routing_map.map);


        google.maps.event.clearInstanceListeners(this.googleMarker);
        var self = this;
        google.maps.event.addListener(this.googleMarker, 'click', function() {
            self.onClick();
        });
        google.maps.event.addListener(this.googleMarker, 'rightclick', function(evt) {
            self.onRightClick(evt, pContextMenuItems);
        });
        google.maps.event.addListener(this.googleMarker, 'mouseup', function() {
            //do nothing
        });

        return this.googleMarker;
    };

    //Permite mover el marcador
    this.setDraggable = function(p_bool) {
        this.googleMarker.setDraggable(p_bool);
    };

    //on click event
    this.onClick = function() {
        var self = this;

        //Si está en modo SIMPLE, deselecciona los demás markers.
        if (self.rmap.selectionMode == 0 && !self.selected)
            self.rmap.unselectMarkers();


        if (self.showInfoWindow) {
            if (self.rmap.infoWindow != null)
                self.rmap.infoWindow.close();

            var contentString = self.entity.toInfoWindow();

            self.rmap.infoWindow = new google.maps.InfoWindow({
                content: contentString
            });
            if (self.rmap.infoWindow != null) {
                self.rmap.infoWindow.open(self.rmap.map, self.googleMarker);
            }
        }

        //  this.rmap.closeContextualMenu();
        this.onClickCallback(this);
        this.setSelected();
    };

    //on right click event
    this.onRightClick = function(evt, pContextMenuItems) {
        console.log('righclick on point');
        console.log(this.entity.id);

        if (this.entity.route != null) {
            pContextMenuItems = jQuery.grep(pContextMenuItems, function(value) {
                return value.id != 'remove_from_route';
            });
        }

        //   this.rmap.showContextualMenu(evt.latLng, pContextMenuItems, this);
    };

    //Lo marca como seleccionado
    this.setSelected = function(p_bool) {
        if (typeof p_bool != 'undefined')
            this.selected = !p_bool;

        if (this.selected) {
            this.selected = false;
            if (this.rmap.infoWindow != null)
                this.rmap.infoWindow.close();
            this.googleMarker.setAnimation(google.maps.Animation.DROP);
        } else {
            //coloco animación
            this.selected = true;
            this.googleMarker.setAnimation(google.maps.Animation.BOUNCE);
        }

    };

    //Cambia el tipo
    this.setType = function(p_type) {
        this.type = p_type;

    };
    //Cambia el Modo a Normal
    this.setNormalMode = function() {
        this.mode = this.NORMAL_MODE;
        this.refresh();
    };
    //Cambia el Modo a Letras
    this.setLetterMode = function(pLetter, pBgColor, pTextColor) {
        this.mode = this.LETTER_MODE;
        this.letter = pLetter;
        this.bgColor = pBgColor;
        this.textColor = pTextColor;
        this.refresh();
    };
}

function TransactionOrder(p_object) {
    this.internalObject = p_object;
    this.transaction = function() {
        return this.internalObject;
    };
    this.toInfoWindow = function() {
        var self = this;
        var contentString = '<div>'
                + '<br/><b>' + language.line('label_nro') + '</b>: ' + self.id
                + (typeof (self.destination_name) == 'undefined' ? '' : '<br/><b>' + language.line('label_destination_name') + '</b>: ' + self.destination_name)
                + (typeof (self.original_address) == 'undefined' ? '' : '<br/><b>' + language.line('label_address') + '</b>: ' + self.original_address)
                + (typeof (self.original_city) == 'undefined' ? '' : '<br/><b>' + language.line('label_locality') + '</b>: ' + self.original_city)
                + (typeof (self.zone_code) == 'undefined' ? '' : '<br/><b>' + language.line('label_zone') + '</b>: ' + self.zone_code)
                + (typeof (self.service_name) == 'undefined' ? '' : '<br/><b>' + language.line('label_client') + '</b>: (' + self.service_code + ') ' + self.service_name)
                + (typeof (self.service_type_description) == 'undefined' ? '' : '<br/><b>' + language.line('label_client_type') + '</b>: ' + self.service_type_description)
                + (typeof (self.distributor_type_description) == 'undefined' ? '' : '<br/><b>' + language.line('label_distributor_type') + '</b>: ' + self.distributor_type_description)
                + (typeof (self.transaction_type) == 'undefined' ? '' : '<br/><b>' + language.line('label_transaction_type') + '</b>: ' + self.transaction_type)
                + '<br/><b>' + language.line('label_route') + '</b>: ' + (self.route == '' || self.route == null ? language.line('label_without_associate_route') : self.route)
                + '<br/><b>' + language.line('label_order') + '</b>: ' + (self.orden == '' || self.orden == null ? ' ? ' : self.orden)
                + '</div>';
        return contentString;
    };
    return $.extend(this.internalObject, this);
}


//ENTIDAD PARA CLIENT
function Client(p_object) {
    this.internalObject = p_object;
    this.logisticPoint = function() {
        return this.internalObject;
    };
    this._getMarkerHTMLDetail = function() {
        var self = this;

        var color = self.getHeadWindowBorderColor();
        var html = '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="padding-left: 3px; padding-top: 1px; padding-bottom: 0px; ' + color + ' ">';
        html += '<h5 style="margin: 5px;"><b>' + self.id + '</b> - ' + (typeof (self.name) == 'undefined' ? '' : self.name) + '</h5>';
        html += '</div>';
        html += '<div class="panel-body" style="padding: 5px;">';
        //Domicilio
        html += '<h6>' + (typeof (self.street) == 'undefined' ? '' : self.street)
                + ' ' + (typeof (self.number) == 'undefined' ? '' : self.number)
                + ' ' + (typeof (self.floor) == 'undefined' ? '' : self.floor)
                + ' ' + (typeof (self.apartment) == 'undefined' ? '' : self.apartment)
                + ', ' + (typeof (self.zip_code) == 'undefined' ? '' : '(' + self.zip_code) + ')'
                + ' ' + (typeof (self.locality) == 'undefined' ? '' : self.locality)
                + ', ' + (typeof (self.estate) == 'undefined' ? '' : self.estate)
                + '</h6>';
        //Datos del servicio, tipo y tipo distribuidor
        html += '<h6 style="margin:0px">' + (typeof (self.client_group) == 'undefined' ? '' : self.client_group) + '</h6>';
        html += '<h6 style="margin:0px"><small>' + (typeof (self.description) == 'undefined' ? '' : self.description) + ' | '
                + (typeof (self.code_client) == 'undefined' ? '' : self.code_client) + '</small></h6>';
        html += '<h6 style="margin:0px">' + (typeof (self.status) == 'undefined' ? '' : self.status) + '</h6>';

        html += '</div>';
        html += '</div>';
        return html;
    };

    this.toInfoWindow = function() {
        var html = '<div style="max-width: 330px; max-height: 250px">';
        html += this._getMarkerHTMLDetail();
        html += '</div>';
        return html;
    };

//Devuelve el color del borde del encabezado del info window
    this.getHeadWindowBorderColor = function() {

        var color = 'border-bottom: 3px solid ';
        switch (this.mark_type) {
            case '1':
                color += 'blue';
                break;
            case '2':
                color += 'orange';
                break;
            case '3':
                color += 'green';
                break;
            case '4':
                color += 'magenta';
                break;
            default:
                color = '';
        }
        return color;
    };
    return $.extend(this.internalObject, this);
}


//ENTIDAD PARA SUPPLIER
function Supplier(p_object) {
    this.internalObject = p_object;

    this.logisticPoint = function() {
        return this.internalObject;
    };

    this._getMarkerHTMLDetail = function() {
        var self = this;
        var html = '';

        html += '<h6>' + (typeof (self.street) == 'undefined' || self.street == '' ? '' : self.street + ' ')
                + (typeof (self.number) == 'undefined' || self.number == '' ? '' : self.number + ' ')
                + (typeof (self.floor) == 'undefined' || self.floor == '' ? '' : self.floor + ' ')
                + (typeof (self.apartment) == 'undefined' || self.apartment == '' ? '' : self.apartment)
                + ', ' + (typeof (self.zip_code) == 'undefined' || self.zip_code == '' ? '' : '(' + self.zip_code + ')')
                + ' ' + (typeof (self.locality) == 'undefined' || self.locality == '' ? '' : self.locality)
                + (typeof (self.state) == 'undefined' || self.state == '' ? '' : ', ' + self.state)
                + '</h6>';

        return html;
    };

    this.toInfoWindow = function() {
        return '<div style="max-width: 330px; max-height: 250px">' + this._getMarkerHTMLDetail() + '</div>';
    };

    //Devuelve el color del borde del encabezado del info window
    this.getHeadWindowBorderColor = function() {
        var color = 'border-bottom: 3px solid ';

        switch (this.mark_type) {
            case '1':
                color += 'blue';
                break;
            case '2':
                color += 'orange';
                break;
            case '3':
                color += 'green';
                break;
            case '4':
                color += 'magenta';
                break;
            default:
                color = '';
        }

        return color;
    };

    return $.extend(this.internalObject, this);
}

//ENTIDAD PARA SHIPPING_COMPANY
function ShippingCompany(p_object) {
    this.internalObject = p_object;

    this.logisticPoint = function() {
        return this.internalObject;
    };

    this._getMarkerHTMLDetail = function() {
        var self = this;
        var html = '';

        html += '<h6>' + (typeof (self.street) == 'undefined' || self.street == '' ? '' : self.street + ' ')
                + (typeof (self.number) == 'undefined' || self.number == '' ? '' : self.number + ' ')
                + (typeof (self.floor) == 'undefined' || self.floor == '' ? '' : self.floor + ' ')
                + (typeof (self.apartment) == 'undefined' || self.apartment == '' ? '' : self.apartment)
                + ', ' + (typeof (self.zip_code) == 'undefined' || self.zip_code == '' ? '' : '(' + self.zip_code + ')')
                + ' ' + (typeof (self.locality) == 'undefined' || self.locality == '' ? '' : self.locality)
                + (typeof (self.state) == 'undefined' || self.state == '' ? '' : ', ' + self.state)
                + '</h6>';

        return html;
    };

    this.toInfoWindow = function() {
        return '<div style="max-width: 330px; max-height: 250px">' + this._getMarkerHTMLDetail() + '</div>';
    };

    //Devuelve el color del borde del encabezado del info window
    this.getHeadWindowBorderColor = function() {
        var color = 'border-bottom: 3px solid ';

        switch (this.mark_type) {
            case '1':
                color += 'blue';
                break;
            case '2':
                color += 'orange';
                break;
            case '3':
                color += 'green';
                break;
            case '4':
                color += 'magenta';
                break;
            default:
                color = '';
        }

        return color;
    };

    return $.extend(this.internalObject, this);
}

//ENTIDAD PARA WAREHOUSE
function Warehouse(p_object) {
    this.internalObject = p_object;

    this.logisticPoint = function() {
        return this.internalObject;
    };

    this._getMarkerHTMLDetail = function() {
        var self = this;
        var html = '';

        html += '<h6>' + (typeof (self.street) == 'undefined' || self.street == '' ? '' : self.street + ' ')
                + (typeof (self.number) == 'undefined' || self.number == '' ? '' : self.number + ' ')
                + (typeof (self.floor) == 'undefined' || self.floor == '' ? '' : self.floor + ' ')
                + (typeof (self.apartment) == 'undefined' || self.apartment == '' ? '' : self.apartment)
                + ', ' + (typeof (self.zip_code) == 'undefined' || self.zip_code == '' ? '' : '(' + self.zip_code + ')')
                + ' ' + (typeof (self.locality) == 'undefined' || self.locality == '' ? '' : self.locality)
                + (typeof (self.state) == 'undefined' || self.state == '' ? '' : ', ' + self.state)
                + '</h6>';

        return html;
    };

    this.toInfoWindow = function() {
        return '<div style="max-width: 330px; max-height: 250px">' + this._getMarkerHTMLDetail() + '</div>';
    };

    //Devuelve el color del borde del encabezado del info window
    this.getHeadWindowBorderColor = function() {
        var color = 'border-bottom: 3px solid ';

        switch (this.mark_type) {
            case '1':
                color += 'blue';
                break;
            case '2':
                color += 'orange';
                break;
            case '3':
                color += 'green';
                break;
            case '4':
                color += 'magenta';
                break;
            default:
                color = '';
        }

        return color;
    };

    return $.extend(this.internalObject, this);
}
