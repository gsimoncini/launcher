/*
 * *****************************
 * Manejador de Mapa genérico.
 * *****************************
 * Implementa un punteo manual aprox. y exacto. Además un punteo Automático
 */


function Generic_Map_Manager(p_name, p_canvas, p_options) {

    //CONSTANTES
    //-----------------------
    this.MODE_STANDBY = 0;
    this.MODE_APPROX = 1;
    this.MODE_EXACT = 2;
    this.MODE_AUTO = 3;
    this.MODE_EDIT = 4;

    //ATRIBUTOS DE INSTANCIA
    //-----------------------
    //RMap
    this.rmap = null;
    this.name = null;

    //Array de marcadores para punteo automatico
    this.automaticsPointsToSend = [];
    //Array que almacena los SetTimeOut para hacer punteo automatico
    this.timeOuts = [];

    //Marker para georeferencia
    this.manualMarker = null;
    this.confirmPointCallback = function(x, y, type) {
    };

    //PointFactory para Punteo Manual
    this.mode = this.MODE_STANDBY; // 0: En espera | 1: Punteo manual aprox | 2: Punteo manual exacto | 3: Georeferencia x Input | 4: Modificar Punto
    this.tempEntity = null;
    this.pointFactory = null;

    //familia de iconos
    this.icons_family = null;

    //Items del menú contextual del marker
    this.contextMenuItems = [];

    //LOGICA DEL CONSTRUCTOR
    //-----------------------
    {
        var self = this;
        this.rmap = new Generic_RMap(p_name, p_canvas, p_options);
        this.name = p_name;

        this.rmap.geocodeFx = function() {
            self.geocodeAddress();
        };

        this.icons_family = $.cookie('type_markers_' + this.name);
        if (this.icons_family == null) {
            this.icons_family = 'marker';
            $.cookie('type_markers_' + this.name, this.icons_family, {path: '/'});
        }

        $('.toolbar-content .btn_google.dropdown-toggle span').removeClass().addClass('icon-family-' + this.icons_family);
    }



    this.setMode = function(pMode) {
        var self = this;

        this.mode = pMode;
        var html = '';

        var cancel = $('<a>', {
            id: 'reset-map-mode',
            text: ' ' + language.line('label_cancel'),
            title: language.line('label_cancel'),
            "class": 'text-danger'}).on('click', function() {
            self.resetMode();
        });

        switch (pMode) {
            case this.MODE_APPROX:
                html = language.line('label_mode') + ': <strong class="text-primary">' + language.line('label_approximate_manual_plotting') + '</strong>';
                break;
            case this.MODE_EXACT:
                html = language.line('label_mode') + ': <strong class="text-warning">' + language.line('label_accurate_manual_plotting') + '</strong>';
                break;
            case this.MODE_AUTO:
                html = language.line('label_mode') + ': <strong>' + language.line('label_georeferencing_for_address') + '</strong>';
                break;
            case this.MODE_EDIT:
                html = language.line('label_mode') + ': <strong>' + language.line('label_modify_point') + '</strong>';
                break;
        }
        $('.map-mode').html(html);
        $('.map-mode strong').after(cancel);
    };

    this.setManualPoint = function(pGeoType, pObject) {
        this.tempEntity = pObject;
        //Coloco el domicilio visible.
        $('#google_map_georeference input.georeference').val(pObject.address);
        this.setMode(pGeoType);
    };

    //Confirma la operacion de punteo manual
    this.confirmManualPointing = function(p_entity, p_new_marker) {
        var self = this;

        //agrego el marcador al mapa
        p_entity.lat = p_new_marker.getPosition().lat();
        p_entity.lng = p_new_marker.getPosition().lng();

        if (p_entity.googleMarker != null) {
            p_entity.googleMarker.setMap(null);
            p_entity.googleMarker = null;
        }
        p_entity.setMap(this.rmap, this.contextMenuItems);

        this.confirmPointCallback(p_entity.googleMarker.getPosition().lat(), p_entity.googleMarker.getPosition().lng(), p_entity.type);
        self.resetMode(false);
    };

    this.cancelManualPointing = function(p_entity) {
        var self = this;
        var tm = self.rmap.markerById(p_entity.id);
        if (tm != null) {
            tm.googleMarker.setAnimation(google.maps.Animation.DROP);
            //Vuelvo a mostrar la original
            tm.googleMarker.setVisible(true);
            tm.googleMarker.setMap(self.rmap.map);
        }
        self.pointFactory = null;
        this.setMode(this.MODE_STANDBY);
        this.rmap.unselectMarkers();
    };

    this.setAutomaticPoint = function(pObject) {

        var add = pObject.address;
        this.setMode(this.MODE_AUTO);

        if (add.toLowerCase().indexOf('argentina') == -1) {
            add += ', Argentina ';
        }
        this.tempEntity = pObject;

        var self = this;
        this.rmap.geocoder.geocode({address: add}, function(results, status) {
            if (google.maps.GeocoderStatus.OK == status) {
                //console.log(results[0].geometry.location);
                var lat = results[0].geometry.location.lat();
                var lng = results[0].geometry.location.lng();
                var marker = new Generic_Marker(lat, lng, self.tempEntity, 3);
                marker.address = add;
                marker.setMap(self.rmap);
                self.confirmPointCallback(lat, lng, 3);
                self.rmap.fit();
                self.resetMode(false);
            } else {
                bootbox.alert(language.line('message_google_error'));
            }
        });

    };

    //Libera el modo del mapa
    this.resetMode = function(pConfirm) {
        var self = this;
        if (typeof (pConfirm) == 'undefined')
            pConfirm = true;

        if (pConfirm) {
            bootbox.dialog({
                message: '¿Seguro que desea cancelar la operación actual?<br/> <i>Perderá el punto ubicado en el mapa.</i>',
                title: "Mapa",
                buttons: {
                    success: {
                        label: "Cancelar operación",
                        className: "btn-danger btn-sm",
                        callback: function() {
                            self._resetMode();
                        }
                    },
                    cancel: {
                        label: "Continuar",
                        className: "btn-default btn-sm",
                        callback: function() {
                        }
                    }
                }
            });
        } else
            self._resetMode();

    };

    this._resetMode = function() {
        var self = this;
        self.setMode(self.MODE_STANDBY);
        $('#google_map_georeference input.georeference').val('');
        if (self.pointFactory != null) {
            self.pointFactory.cancelManualPoint();
            self.cancelManualPointing(self.tempEntity);
            self.pointFactory = null;
        }
        if (self.manualMarker != null) {
            self.manualMarker.setMap(null);
            self.manualMarker = null;
        }
    };

    //Ubica el domicilio segun input.
    this.geocodeAddress = function() {
        if (this.mode == this.MODE_APPROX || this.mode == this.MODE_EXACT) {
            var add = $('#google_map_georeference input.georeference').val();
            if (add != null && add != '') {
                if (this.manualMarker != null) {
                    this.manualMarker.setMap(null);
                    this.manualMarker = null;
                }

                if (add.toLowerCase().indexOf('argentina') == -1) {
                    add += ', Argentina ';
                }

                var self = this;
                this.rmap.geocoder.geocode({address: add}, function(results, status) {
                    if (google.maps.GeocoderStatus.OK == status) {

                        //VIENE DE GEOREFERENCIA MANUAL
                        if (self.mode == self.MODE_APPROX || self.mode == self.MODE_EXACT) {
                            if (self.pointFactory != null) {
                                self.pointFactory.cancelManualPoint();
                            }
                            var lat = results[0].geometry.location.k;
                            var lng = results[0].geometry.location.A;
                            var transactionMarker = new Generic_Marker(lat, lng, self.tempEntity, null);
                            transactionMarker.address = add;
                            var googleMarker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: self.rmap.map
                            });
                            transactionMarker.googleMarker = googleMarker;
                            self.pointFactory = new Point_Factory(self.rmap.map, transactionMarker.googleMarker);

                            self.rmap.map.setCenter(results[0].geometry.location);
                            self.rmap.map.setZoom(15);

                            self.pointFactory.getManualPoint(function(p_marker, pType) {
                                //confirma
                                transactionMarker.type = pType; //self.mode;
                                self.confirmManualPointing(transactionMarker, p_marker);
                            }, function() {
                                self.resetMode();
                            });
                        }

                    } else {
                        bootbox.alert(language.line('message_google_error'));
                    }
                });
            }
            else
                bootbox.alert(language.line('message_have_not_data'));
        } else
            bootbox.alert(language.line('message_select_method_plotting'));
    };

    //Define la familia de iconos
    this.setIconsFamily = function(type) {
        if (this.rmap.status && this.mode == 0) {
            $('#google_map_toolbar .dropdown-toggle span').removeClass().addClass('icon-family-' + type);
            $('.map-legend > div').removeClass('active');
            $('.map-legend .legend-group-' + type).addClass('active');
            $.cookie('type_markers_' + this.name, type, {path: '/'});
            this.rmap.refreshIcons();
        }
    };

}

