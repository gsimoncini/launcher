/*
 * ++++++++++++++++++++++++++++++
 * RMAP
 * ++++++++++++++++++++++++++++++
 */

function Generic_RMap(p_name, p_canvas, p_options, p_map_manager) {
    //CONSTANTES
    //-----------------------
    //@Nothing

    //ATRIBUTOS DE INSTANCIA
    //-----------------------

    this.container = null;
    this.name = null;

    //Mapa de Google
    this.map = null;
    //DOM id tag para el mapa
    this.canvasTag;
    //Controles del mapa
    this.mapOptions = null;
    //Menu Contextual en mapa
    this.contextMenuClass = 'map-contextmenu';
    //Controles del mapa
    this.mapControls = {};

    //InfoWindow
    this.infoWindow = null;

    //Modo de seleccion.
    this.selectionMode = 0; //0 = Simple - 1 = por Poligono - 2 = Múltiple por Click
    this.selectionPath = new google.maps.MVCArray;
    this.selectionPolygon = null;
    this.selectionInfowindow = new google.maps.InfoWindow({content: ''});

    //Servicios para Recorridos
    this.directionsService = new google.maps.DirectionsService();
    this.directionsDisplay = new google.maps.DirectionsRenderer();
    this.routePath = new Array();
    this.routeMarkers = new Array();
    this.routeId = null;
    this.onlyThisRoute = false; //Muestra solo los puntos del recorrido con RouteId

    //Capas
    this.layers = {};

    //centro del mapa
    this.center = new google.maps.LatLng(-34.60371833631762, -58.381569916288356);
    //zoom
    this.zoom = 15;
    //Geocodificador
    this.geocoder = null;
    //Limites del mapa
    this.bounds = null;
    //Estado del mapa (1 = Activo/ 0 = Inactivo)
    this.status = 1;

    //Marcadores incluidos en el mapa
    this.markers = new Array();

    //Zonas a dibujar (ZONE = id, zone)
    this.zones = new Array();
    this.zoneMode = 2; //2: Muestra Zonas de Georeferenciación - 1: Muestra todas las zonas
    this.ALL_ZONES = 1;
    this.GEO_ZONES = 2;

    //Controles personalizados
    this.controls = {
        geocode: false
        , simpleSelection: true
        , multiSelection: false
        , polygonSelection: true
        , clearSelection: true
        , refresh: true
        , fit: true
        , enable: true
        , iconFamilySet: false
    };

    //Pack de iconos
    this.iconFamilySet = {
        marker: true
        , distributor: true
        , route: true
        , term: true
    };

    this.geocodeFx = function() {
    };

    this.markersMustShowInfoWindow = true;

    //LOGICA DEL CONSTRUCTOR
    //-----------------------
    {
        if (typeof (p_map_manager) != 'undefined')
            this.container = p_map_manager;
        this.name = p_name;

        //Construyo el mapa
        this.canvasTag = p_canvas;
        this.mapOptions = {
            zoom: this.zoom
            , center: this.center
            , panControl: false
            , zoomControl: true
            , zoomControlOptions: {
                style: google.maps.ZoomControlStyle.DEFAULT,
                position: google.maps.ControlPosition.LEFT_CENTER
            }
            , mapTypeControl: false
            , scaleControl: false
            , streetViewControl: false
            , overviewMapControl: false
        };

        this.mapControls = {
            refreshControl: true
            , disableControl: true
            , fitControl: true
            , selectionModeControl: true
        };

        if (typeof (p_options) != 'undefined') {
            this.controls = p_options.controls;
            this.iconFamilySet = p_options.iconFamilySet;
        }

        this.geocoder = new google.maps.Geocoder();
        this.bounds = new google.maps.LatLngBounds();

    }

    //MÉTODOS
    //-----------------------

    //Construye los controles
    this._constructControls = function() {
        var self = this;

        /* Toolbar */
        $('#google_map_toolbar').remove();
        $('#' + this.canvasTag).before('<div id="google_map_toolbar" class="hide"><div class="btn-group"></div></div>');

        /* Icons Sets */

        if (this.controls.iconFamilySet) {
            $('#google_map_toolbar').removeClass('hide');
            $('#google_map_toolbar .btn-group:last').append('<a href="#" class="dropdown-toggle btn btn-default" data-toggle="dropdown" title="' + language.line('label_icon_family') + '" >'
                    + '<span class=""></span>'
                    + '</a>'
                    + '<ul class="dropdown-menu"></ul>');
            if (this.iconFamilySet.marker) {
                $('#google_map_toolbar .dropdown-menu').append('<li><a class="btn btn-default btn-sm map-marker-family"><i class="icon-family-marker icons-menu-item"></i>' + language.line('label_for_marck') + '</a></li>');
                $('.map-marker-family').on('click', function() {
                    self.container.setIconsFamily('marker');
                });

            }
            if (this.iconFamilySet.distributor) {
                $('#google_map_toolbar .dropdown-menu').append('<li><a class="btn btn-default btn-sm map-distributor-family" ><i class="icon-family-distributor icons-menu-item"></i>' + language.line('label_for_distributor_type') + '</a></li>');
                $('.map-distributor-family').on('click', function() {
                    self.container.setIconsFamily('distributor');
                });
            }
            if (this.iconFamilySet.term) {
                $('#google_map_toolbar .dropdown-menu').append('<li><a class="btn btn-default btn-sm map-term-family" "><i class="icon-family-term icons-menu-item"></i>' + language.line('label_for_term') + '</a></li>');
                $('.map-term-family').on('click', function() {
                    self.container.setIconsFamily('term');
                });
            }
            if (this.iconFamilySet.route) {
                $('#google_map_toolbar .dropdown-menu').append('<li><a class="btn btn-default btn-sm map-route-family" ><i class="icon-family-route icons-menu-item"></i>' + language.line('label_for_route') + '</a></li>');
                $('.map-route-family').on('click', function() {
                    self.container.setIconsFamily('route');
                });
            }
        }


        $('#google_map_toolbar').append('<div class="btn-group-vertical"></div>');

        /* Geocoder */
        if (this.controls.geocode) {

            /* Geocoder */
            $('#google_map_georeference .col-md-10').remove();
            $('#' + this.canvasTag).before('<div id="google_map_georeference" style="left: -8px; top: 35px; position: relative; z-index: 100;"></div>');

            var html = '<div class="col-md-10">' +
                    '<div class="input-group">' +
                    '<input type="text" value="" class="form-control input-sm georeference" onkeyup="" placeholder="' + language.line('label_enter_your_address_for_search') + '" /><span id="georeference-search-clear" class="glyphicon glyphicon-remove"></span>' +
                    '<span class="input-group-btn geo-btn">' +
                    '</span>' +
                    '</div>' +
                    '</div>';
            $('#google_map_georeference').append(html);

            var link = $('<a>', {
                id: 'geocode_manual_address',
                text: '',
                title: 'Georeferenciar',
                'class': 'btn btn-primary btn-sm btn-icon'}).on('click', function() {
                self.geocodeFx();
            });
            $(link).html('<b class="glyphicon glyphicon-search"></b>');

            $('.geo-btn').append(link);

            $('#google_map_georeference input').keyup(function() {
                $('#georeference-search-clear').toggle(Boolean($(this).val()));
            });
            $('#georeference-search-clear').toggle(Boolean($('#georeference-search-clear').val()));
            $('#georeference-search-clear').click(function() {
                $('#google_map_georeference input').val('').focus();
                $(this).hide();
            });

            $('.georeference').on('keyup', function(event) {
                if (event.keyCode == 13) {
                    self.geocodeFx();
                }
            });
        }

        /* Selección de Puntos */
        if (this.controls.simpleSelection) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="handmode_map" class="btn btn-default active map-select-mode-simple" title="' + language.line('label_simple_selection') + '"></a>');
            $('.map-select-mode-simple').on('click', function() {
                self.setSimpleSelectionMode();
            });
        }
        /* Seleccion por poligono */
        if (this.controls.polygonSelection) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="polyselectmode_map" class="btn btn-default map-select-mode-polygon" title="' + language.line('label_select_for_polygon') + '"></a>');
            $('.map-select-mode-polygon').on('click', function() {
                self.setSelectionByPolygonMode();
            });
        }
        /* Seleccion Múltiple*/
        if (this.controls.multiSelection) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="multimode_map" class="btn btn-default map-select-mode-multiple"  title="' + language.line('label_multi_selection') + '"></a>');
            $('.map-select-mode-multiple').on('click', function() {
                self.setMultiSelectionMode();
            });
        }
        /* Limpiar seleccion */
        if (this.controls.clearSelection) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="clear_selection" class="btn btn-default map-unselect" title="' + language.line('label_deselect') + '"></a>');
            $('.map-unselect').on('click', function() {
                self.unselectMarkers();
            });
        }

        $('#google_map_toolbar').append('<div class="btn-group-vertical"></div>');
        /* Refresh Map */
        if (this.controls.refresh) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="regenerate_map" class="btn btn-default map-refresh" title="' + language.line('label_refresh_map') + '" ></a>');
            $('.map-refresh').on('click', function() {
                self.container.showAllTransactions();
            });
        }
        /* Fit */
        if (this.controls.fit) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="fit_map" class="btn btn-default map-fit" title="' + language.line('label_adjust_map') + '"></a>');
            $('.map-fit').on('click', function() {
                self.fit();
            });
        }
        /* Enable/Disable Map */
        if (this.controls.enable) {
            $('#google_map_toolbar .btn-group-vertical:last').append('<a id="maps_visible" class="btn btn-default map-enable" title="' + language.line('label_disable') + '"></a>'
                    + '<a id="maps_no_visible" class="btn btn-default map-disable" style="display:none;" title="' + language.line('label_enable') + '"></a>');
            $('.map-enable, .map-disable').on('click', function() {
                self.setEnabled();
            });
        }

    };

    //Muestra la información (markers y zones) en el mapa de google.
    this.render = function() {
        if (this.status) {
            var self = this;
            $.each(this.markers, function(i, value) {
                value.googleMarker.setMap(null);
                //Si se muestran solo markers de un recorrido
                if (self.routeId != null && self.onlyThisRoute) {
                    if (self.routeId == value.entity.route)
                        value.googleMarker.setMap(self.map);
                } else
                    value.googleMarker.setMap(self.map);
            });

            this.fit();
        }
    };


    //Agrega un marcador al mapa
    this.addMarker = function(p_marker) {
        if (p_marker != null) {

            //Eliminamos un elemento si ya está en la lista
            this.removeMarkerById(p_marker.entity.id, false);
            p_marker.showInfoWindow = this.markersMustShowInfoWindow;
            this.markers.push(p_marker);
            var latlng = new google.maps.LatLng(p_marker.lat, p_marker.lng);
            this.bounds.extend(latlng);
        }
    };

    //Regenera el mapa de google
    this.regenerate = function() {
        if (this.status) {
            var self = this;
            this.unselectMarkers();
            $('.map-mode').remove();
            this.map = new google.maps.Map(document.getElementById(this.canvasTag), this.mapOptions);
            $('#' + self.canvasTag).before('<div class="map-mode" style="margin-top: -20px; float: left;"></div>');
            google.maps.event.addListener(this.map, 'click', function(evt) {
                console.log('click en mapa.');
                // self.closeContextualMenu();
                //Si está en modo seleccion por poligono agrego el marker para formar poligono de seleccion
                if (self.selectionMode == 1)
                    self.__addPointToSelectionPolygon(evt);
            });

            //Evaluo su mostrar o no el menu de opciones
            $('#fit_map').css('display', this.mapControls.fitControl ? 'block' : 'none');
            $('#regenerate_map').css('display', this.mapControls.refreshControl ? 'block' : 'none');
            $('#maps_visible').css('display', this.mapControls.disableControl ? 'block' : 'none');
            $('#handmode_map, #polyselectmode_map').css('display', this.mapControls.selectionModeControl ? 'block' : 'none');

            this.render();
        }
    };

    //Refresca el mapa
    this.refresh = function() {
        google.maps.event.trigger(this.map, 'resize');
    };

    //Setea el centro del mapa de acuerdo a los puntos mostrados.
    this.fit = function() {
        if (this.status) {
            if (this.routePath.length == 0) {
                //recalculo los bounds
                this.bounds = new google.maps.LatLngBounds();
                var self = this;
                $.each(this.markers, function(i, m) {
                    self.bounds.extend(m.googleMarker.getPosition());
                });
                if (this.markers.length > 0)
                    this.map.fitBounds(this.bounds);
            } else {
                this.fitToRoute();
            }
        }
    };

    //Busca el marcador en el array
    this.markerById = function(pId) {
        var found = null;
        $.each(this.markers, function(index, mark) {
            if (mark.entity.id == pId) {
                found = mark;
            }
        });
        return found;
    };

    //Devuelve todos los markers seleccionados
    this.getSelectedMarkers = function() {
        var selected = new Array();
        $.each(this.markers, function(index, mark) {
            if (mark.selected) {
                selected.push(mark);
            }
        });

        return selected;
    };

    //Marca todos los markadores
    this.selectMarkers = function(p_array_of_ids) {

        if (this.status) {
            $.each(this.markers, function(index, mark) {
                if ($.inArray(mark.entity.id, p_array_of_ids)) {
                    mark.setSelected(true);
                } else {
                    mark.setSelected(false);
                }
            });
        }
    };
    //desmarca todos los markadores
    this.unselectMarkers = function() {
        if (this.status) {
            $.each(this.getSelectedMarkers(), function(index, mark) {
                mark.setSelected(false);
            });
        }
    };

    //Elimina un marcador
    this.removeMarkerById = function(p_id, p_remove_google_marker) {

        if (typeof p_remove_google_marker == "undefined") {
            var m = this.markerById(p_id);
            if (m != null) {
                m.googleMarker.setVisible(false);
                m.googleMarker.setMap(null);
            }
        }
        this.markers = jQuery.grep(this.markers, function(value) {
            return value.entity.id != p_id;
        });
    };

    //Elimina todos los marcadores
    this.removeAllMarkers = function() {
        $.each(this.markers, function(i, value) {
            value.googleMarker.setVisible(false);
            value.googleMarker.setMap(null);
        });
        this.markers = [];
    };

    //Habilita/Deshabilita el Mapa
    this.setEnabled = function(p_bool) {
        if (typeof p_bool != 'undefined')
            this.status = p_bool;

        if (this.status) {

            var styles = [
                {
                    stylers: [
                        {hue: "#00ffe6"},
                        {saturation: -20}
                    ]
                }, {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [
                        {lightness: 100},
                        {visibility: "simplified"}
                    ]
                }, {
                    featureType: "road",
                    elementType: "labels",
                    stylers: [
                        {visibility: "off"}
                    ]
                }
            ];
            this.map.setOptions({styles: styles});
            this.status = false;
            $('#maps_visible').hide();
            $('#maps_no_visible').show();
        } else {
            this.map.setOptions({styles: []});
            $('#maps_visible').show();
            $('#maps_no_visible').hide();
            this.status = true;
        }
    };

    //Oculta los marcadores que están en el mapa
    this.hiddeAllMarkers = function() {
        $(this.markers).each(function(i, marker) {
            marker.googleMarker.setVisible(false);
        });
    };

    //Muestra todos los marcadores
    this.showAllMarkers = function() {
        $(this.markers).each(function(i, marker) {
            marker.googleMarker.setVisible(true);
        });
    };

    //Actualiza los iconos de acuerdo a la familia de iconos
    this.refreshIcons = function() {
        $(this.markers).each(function(index, value) {
            value.googleMarker.icon = value.getIcon();
        });
        this.regenerate();
    };

    //Iconos de punteo - devuelve el html correspondiente al valor del parametro
    this.getMarkTypeIcon = function(pMarkType) {

        var icon_url = baseUrl + '/img/custom/map/icon/';

        switch (pMarkType) {
            case 1:
                return ['<div style="text-align:center" title="' + language.line('system_approximate_manual') + '"><img src="' + icon_url + '/marker_1.png' + '" style="width:20px;"></div>', language.line('label_approximate_manual_plotting')];
                break;
            case 2:
                return ['<div style="text-align:center" title="' + language.line('system_exact_manual') + '"><img src="' + icon_url + '/marker_2.png' + '" style="width:20px;"></div>', language.line('label_accurate_manual_plotting')];
                break;
            case 3:
                return ['<div style="text-align:center" title="' + language.line('system_automatic') + '"><img src="' + icon_url + '/marker_3.png' + '" style="width:20px;"></div>', language.line('label_automatic_plotting') + ' (Google)'];
                break;
            case 4:
                return  ['<div style="text-align:center" title="' + language.line('label_out_zone') + '"><img src="' + icon_url + '/out_zone.png' + '" style="width:20px;"></div>', language.line('label_out_zone')];
                break;
            case 5:
                return ['<div style="text-align:center" title="' + language.line('label_without_associate_zone') + '"><img src="' + icon_url + '/without_zone.png' + '" style="width:20px;"></div>', language.line('label_without_associate_zone')];
                break;
            case 6:
                return ['<div style="text-align:center" title="' + language.line('label_no_automatic_plotting') + '"><img src="' + icon_url + '/no-mark.png' + '" style="width:20px;"></div>', language.line('label_not_automatic_plotting')];
                break;
            default:
                return ['<div style="text-align:center" title="' + language.line('label_unlabeled') + '"><img src="' + icon_url + '/disable.png' + '" style="width:20px;"></div>', language.line('label_unlabeled')];
                break;
        }
    };

    //######### MENU CONTEXTUAL #########
    /*
     //Devuelve la X e Y donde se hizo el click convertido a LAT y LNG de google
     this.__getClickedPosition = function(caurrentLatLng) {
     var scale = Math.pow(2, this.map.getZoom());
     var nw = new google.maps.LatLng(
     this.map.getBounds().getNorthEast().lat(),
     this.map.getBounds().getSouthWest().lng()
     );
     var worldCoordinateNW = this.map.getProjection().fromLatLngToPoint(nw);
     var worldCoordinate = this.map.getProjection().fromLatLngToPoint(caurrentLatLng);
     var caurrentLatLngOffset = new google.maps.Point(
     Math.floor((worldCoordinate.x - worldCoordinateNW.x) * scale),
     Math.floor((worldCoordinate.y - worldCoordinateNW.y) * scale)
     );
     return caurrentLatLngOffset;
     };

     //Calcula el X e Y para colocarlo en el CSS del menu contextual
     this.__setXYContextMenu = function(caurrentLatLng) {
     var mapWidth = $(this.canvasTag).width();
     var mapHeight = $(this.canvasTag).height();
     var menuWidth = $('.' + this.contextMenuClass).width();
     var menuHeight = $('.' + this.contextMenuClass).height();
     var clickedPos = this.__getClickedPosition(caurrentLatLng);
     var x = clickedPos.x;
     var y = clickedPos.y;

     if ((mapWidth - x) < menuWidth)//if to close to the map border, decrease x position
     x = x - menuWidth;
     if ((mapHeight - y) < menuHeight)//if to close to the map border, decrease y position
     y = y - menuHeight;

     $('.' + this.contextMenuClass).css('left', x);
     $('.' + this.contextMenuClass).css('top', y);
     };

     //Despliega un menu contextual en el mapa
     this.showContextualMenu = function(caurrentLatLng, p_items, p_marker) {
     if (this.status) {
     $('.' + this.contextMenuClass).remove();
     var contextMenuDiv = $('<div class="' + this.contextMenuClass + ' btn-group-vertical"></div>');

     var closeButton = $('<a>', {
     text: 'Cerrar',
     title: 'Cerrar',
     class: 'btn btn-default btn-sm',
     onclick: '$(\'.' + this.contextMenuClass + '\').remove();'
     });

     var self = this;
     $.each(p_items, function(i, menu_item) {
     var item = $('<a class="btn btn-sm">', menu_item);
     item.html(menu_item.text);
     item.off('click').on('click', function() {
     menu_item.clickAction(p_marker);
     self.closeContextualMenu();
     });
     contextMenuDiv.append(item);
     });
     contextMenuDiv.append(closeButton);

     // contextMenuDiv.innerHTML = html;
     $(this.map.getDiv()).append(contextMenuDiv);

     //Define la ubicación del menu
     this.__setXYContextMenu(caurrentLatLng);
     //Muestra el menu
     contextMenuDiv.css('visibility', "visible");
     }
     };

     //Oculta el menu contextual en el mapa
     this.closeContextualMenu = function() {
     $('.' + this.contextMenuClass).remove();
     };*/

    //######### SELECCION POR POLIGONO #########

    //Coloca al mapa en modo de selección por Polígono
    this.setSelectionByPolygonMode = function() {
        if (this.status) {
            this.selectionMode = 1; //Por poligono
            $('#handmode_map').removeClass('active');
            $('#multimode_map').removeClass('active');
            $('#polyselectmode_map').addClass('active');

            this.selectionPolygon = new google.maps.Polygon({
                strokeWeight: 3,
                editable: true,
                zIndex: 100,
                fillColor: '#888888'
            });
            this.selectionPolygon.setMap(this.map);
            this.selectionPolygon.setPaths(new google.maps.MVCArray([this.selectionPath]));

            this.__generateSelectionInfoWindow();
            var self = this;
            google.maps.event.addListener(this.selectionPolygon, 'click', function(evt) {
                self.__infowindowToCenter();
                self.selectionInfowindow.open(self.map);
            });
        }
    };

    //Coloca al mapa en modo de selección simple
    this.setSimpleSelectionMode = function() {

        this.selectionMode = 0; //Simple
        $('#handmode_map').addClass('active');
        $('#multimode_map').removeClass('active');
        $('#polyselectmode_map').removeClass('active');

        if (this.selectionPolygon != null) {
            this.selectionPolygon.setMap(null);
            this.selectionPolygon = null;
            this.selectionInfowindow.close();
        }
        this.selectionPath = new google.maps.MVCArray;

    };

    //Coloca al mapa en modo de selección múltiple
    this.setMultiSelectionMode = function() {

        this.selectionMode = 2; //Múltiple
        $('#handmode_map').removeClass('active');
        $('#multimode_map').addClass('active');
        $('#polyselectmode_map').removeClass('active');

        if (this.selectionPolygon != null) {
            this.selectionPolygon.setMap(null);
            this.selectionPolygon = null;
            this.selectionInfowindow.close();
        }
        this.selectionPath = new google.maps.MVCArray;

    };

    //Metodo privaro para agregar un marker al poligono de seleccion
    this.__addPointToSelectionPolygon = function(event) {
        this.selectionPath.insertAt(this.selectionPath.length, event.latLng);
    };

    //Genera el infoWindow para el poligono de seleccion
    this.__generateSelectionInfoWindow = function() {
        var infow = this.selectionInfowindow;
        var self = this;
        var div = $('<div class="group_btns" style="text-align:center;">' + language.line('message_select_plotting_in_polygon_active_question') + '<br/></div>');
        var confirmButton = $('<a>', {
            id: 'confirm-polygon-selection',
            text: language.line('label_yes_select'),
            title: language.line('label_select_include_point'),
            "class": 'btn btn-primary btn-sm'});
        $(document).off('click', '#confirm-polygon-selection').on('click', '#confirm-polygon-selection', function() {
            self.__confirmPolygonSelection();
        });

        var cancelButton = $('<a>', {
            id: 'cancel-polygon-selection',
            text: language.line('label_not_yet'),
            title: language.line('label_not_select_yet'),
            "class": 'btn btn-default btn-sm'});
        $(document).off('click', '#cancel-polygon-selection').on('click', '#cancel-polygon-selection', function() {
            self.__cancelPolygonSelection();
        });

        div.append(confirmButton);
        div.append(cancelButton);

        infow.setContent(div.html() + '<br/><br/>');
    };

    //Coloca el infoWindow en el centro del poligono
    this.__infowindowToCenter = function() {
        //Coloco el infowindow al centro del polygon
        var bounds = new google.maps.LatLngBounds();
        $.each(this.selectionPath.getArray(), function(index, value) {
            bounds.extend(new google.maps.LatLng(value.lat(), value.lng()));
        });
        this.selectionInfowindow.setPosition(bounds.getCenter());
    };

    //Confirma la selección por polígono.
    this.__confirmPolygonSelection = function() {

        //Desselecciono todos los marcadores.
        this.unselectMarkers();

        var self = this;
        //Por cara marker controlo si está dentro del polígono o no.
        $.each(this.markers, function(i, mark) {
            if (google.maps.geometry.poly.containsLocation(mark.googleMarker.getPosition(), self.selectionPolygon)) {
                //Si incluye, lo tengo en cuenta para seleccionar
                mark.setSelected(true);
            }
        });

        //Salgo de la opción marcado por polígono
        this.setSimpleSelectionMode();

    };

    //Cancela la selección por polígono
    this.__cancelPolygonSelection = function() {
        this.selectionInfowindow.close();
    };

    //####### RUTAS ########
    /*
     //Muestra en el mapa una ruta con punto de inicio, punto de fin y puntos intermedios
     this.showRoute = function(p_start, p_end, p_waypoints, pTableContainer, pCalcule) {
     var self = this;

     this.disposeRoute();
     this.hiddeAllMarkers();

     this.directionsDisplay.setMap(this.map);

     //Genero los puntos intermedios
     var waypts = this.__generateWaypointsToRoute(p_start, p_end, p_waypoints);

     //Dibujo los puntos de la ruta
     this.__drawPointsOfRoute(p_start, p_end, p_waypoints);

     //Modo de viaje
     var selectedMode = google.maps.TravelMode.DRIVING;
     //unidad de medida
     var unitSystem = google.maps.DirectionsUnitSystem.METRIC;


     //Para calculo de distancias y tiempos
     GLOBAL_DISTANCE_ARRAY = new Array(waypts.length);
     GLOBAL_TIME_ARRAY = new Array(waypts.length);
     var global_ix = 1;


     //Genero infoWindow para el primer punto
     var res = jQuery.grep(self.routeMarkers, function(val, ix) {
     return (val.transactionMarker.transaction.id == p_waypoints[0].transaction.id);
     });
     var object = res[0];
     this.__prepareRouteMarkerPopups(object);

     $(pTableContainer).jtable('changeCellValue', 'orden', object.transactionMarker.transaction.id, '<b>' + (global_ix) + '</b>');
     $(pTableContainer).jtable('changeCellValue', 'distance', object.transactionMarker.transaction.id, '-');
     $(pTableContainer).jtable('changeCellValue', 'distance_sum', object.transactionMarker.transaction.id, '-');
     $(pTableContainer).jtable('changeCellValue', 'duration', object.transactionMarker.transaction.id, '-');
     $(pTableContainer).jtable('changeCellValue', 'duration_sum', object.transactionMarker.transaction.id, '-');

     var distance_sum = 0;
     var duration_sum = 0;

     // Loop through all destinations in groups of 10, and find route to display.
     for (var idx1 = 0; idx1 < waypts.length - 1; idx1 += 9)
     {
     // Setup options.
     var idx2 = Math.min(idx1 + 9, waypts.length - 1);
     var request = {
     origin: waypts[idx1].location,
     destination: waypts[idx2].location,
     travelMode: google.maps.DirectionsTravelMode[selectedMode],
     unitSystem: unitSystem,
     waypoints: waypts.slice(idx1 + 1, idx2),
     optimizeWaypoints: false
     };


     var self = this;
     this.directionsService.route(request, function(response, status) {
     if (status == google.maps.DirectionsStatus.OK) {
     //Calculo tiempos y distancias entre puntos de la ruta
     var routes = response.routes;
     for (var rte in routes)
     {
     var legs = routes[rte].legs;
     self.__addRoutePath(routes[rte].overview_path);
     for (var leg = 0; leg < legs.length; leg++) {

     $(pTableContainer).jtable('changeCellValue', 'orden', p_waypoints[global_ix].transaction.id, '<b>' + (global_ix + 1) + '</b>');

     //calcular distancias y tiempos
     if (pCalcule) {
     GLOBAL_DISTANCE_ARRAY[p_waypoints[global_ix].transaction.id] = legs[leg].distance.value;
     GLOBAL_TIME_ARRAY[p_waypoints[global_ix].transaction.id] = legs[leg].duration.value;

     distance_sum += legs[leg].distance.value;
     duration_sum += legs[leg].duration.value;

     $(pTableContainer).jtable('changeCellValue', 'distance', p_waypoints[global_ix].transaction.id, '<b>' + Math.round((legs[leg].distance.value / 1000) * 100) / 100 + " km" + '</b>');
     $(pTableContainer).jtable('changeCellValue', 'distance_sum', p_waypoints[global_ix].transaction.id, '<b>' + Math.round((distance_sum / 1000) * 100) / 100 + " km" + '</b>');
     $(pTableContainer).jtable('changeCellValue', 'duration', p_waypoints[global_ix].transaction.id, '<b>' + self.secs_to_hms(legs[leg].duration.value) + '</b>');
     $(pTableContainer).jtable('changeCellValue', 'duration_sum', p_waypoints[global_ix].transaction.id, '<b>' + self.secs_to_hms(duration_sum) + '</b>');
     }
     //Genero el infoWindow y actualizo el title
     var res = jQuery.grep(self.routeMarkers, function(val, ix) {
     return (val.transactionMarker.transaction.id == p_waypoints[global_ix].transaction.id);
     });
     var object = res[0];
     self.__prepareRouteMarkerPopups(object);
     //Aumento el indice
     global_ix++;
     }
     }

     //Ajusto la vista dle mapa a la ruta.
     self.fitToRoute();

     }
     else if (status == google.maps.DirectionsStatus.ZERO_RESULTS) {
     alert('Google no pudo generar la ruta.');
     } else {
     alert('Error desconocido.');//@TODO: Revisar el mensaje de error
     }
     });
     }

     };

     //Prepara el infoWindow y el Title para los marcadores de una ruta
     this.__prepareRouteMarkerPopups = function(pObject) {
     //Obtengo distancia y tiempo entre puntos
     var distance = Math.round((GLOBAL_DISTANCE_ARRAY[pObject.transactionMarker.transaction.id] / 1000) * 100) / 100;
     var duration = GLOBAL_TIME_ARRAY[pObject.transactionMarker.transaction.id];

     var title = 'Orden: ' + pObject.transactionMarker.transaction.orden + ' Pedido: ' + pObject.transactionMarker.transaction.id;

     if (!isNaN(distance))
     title += ' Distancia: ' + distance + " km";
     if (!isNaN(duration))
     title += ' Tiempo: ' + this.secs_to_hms(duration);

     //Actualizo el title del marker
     pObject.routeGoogleMarker.setTitle(title);

     //Preparo el infoWindow
     var contentString = '<div>'
     + '<b>Nro Pedido</b>:' + pObject.transactionMarker.transaction.id
     + '<br/><b>Orden</b>:' + pObject.transactionMarker.transaction.orden
     + '<br/><b>Dirección</b>:' + pObject.transactionMarker.transaction.original_address
     + '<br/><b>Código de Servicio</b>:' + pObject.transactionMarker.transaction.service_code
     + '<br/><br/>';

     if (distance)
     contentString += '<br/><b>Distancia</b>:' + distance + " km";
     if (duration)
     contentString += '<br/><b>Tiempo</b>:' + this.secs_to_hms(duration);
     contentString += '</div>';

     this.__prepareInfoWindow(pObject.routeGoogleMarker, this.map, contentString);
     };

     //Prepara el infoWindow para un marker
     this.__prepareInfoWindow = function(pMarker, pMap, pString) {
     var infoWindow = new google.maps.InfoWindow({
     content: pString
     });
     google.maps.event.addListener(pMarker, 'click', function() {
     infoWindow.open(pMap, pMarker);
     });
     };

     //Genera los waypoints
     this.__generateWaypointsToRoute = function(p_start, p_end, p_waypoints) {
     var waypts = [];
     if (p_start != null)
     waypts.push({
     location: p_start,
     stopover: true});
     $.each(p_waypoints, function(i, value) {
     waypts.push({
     location: value.googleMarker.getPosition(),
     stopover: true});
     });
     if (p_end != null)
     waypts.push({
     location: p_end,
     stopover: true});
     return waypts;
     };

     //Dibujo los puntos intermedios
     this.__drawPointsOfRoute = function(p_start, p_end, p_waypoints) {
     //Dibujo el punto de salida y llegada
     if (p_start != null) {
     var mark = new google.maps.Marker({
     position: p_start,
     map: this.map,
     title: 'Salida',
     icon: urlImg + '/icons/start_flag.png'
     });
     var rmark = {routeGoogleMarker: mark, transactionMarker: null, distance: 0, duration: 0};
     this.routeMarkers.push(rmark);
     }

     //Colores segun tipo de distribuidor
     var colors = {
     1: '0059B2',
     2: '006600',
     3: '8C4600',
     4: 'B20000',
     5: '5900B2',
     6: 'D9D900'
     };

     var self = this;
     //Coloca cada punto intermedio de la ruta en el mapa
     $.each(p_waypoints, function(i, value) {
     var letter = i + 1;
     var mark = new google.maps.Marker({
     position: value.googleMarker.getPosition(),
     map: self.map,
     title: 'Orden: ' + value.transaction.orden + ' Pedido: ' + value.transaction.id,
     icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + letter + "|" + colors[value.transaction.distributor_type] + "|BBBBBB"
     });
     var rmark = {routeGoogleMarker: mark, transactionMarker: value, distance: 0, duration: 0};
     self.routeMarkers.push(rmark);
     });

     if (p_end != null) {
     //llegada
     mark = new google.maps.Marker({
     position: p_end,
     map: this.map,
     title: 'Llegada',
     icon: urlImg + '/icons/end_flag.png'
     });
     var rmark = {routeGoogleMarker: mark, transactionMarker: null, distance: 0, duration: 0};
     this.routeMarkers.push(rmark);
     }
     };

     //Agregar un punto a una ruta
     this.__addRoutePath = function(path) {
     this.routePath.push(new google.maps.Polyline({
     path: path,
     map: this.map,
     strokeColor: "#0059B2",
     strokeOpacity: 0.7,
     strokeWeight: 4}));
     };

     //Ajusta el mapa a la ruta mostrada
     this.fitToRoute = function() {
     if (this.status) {
     // Set zoom and center of map to fit all paths, and display directions.
     var latlngbounds = new google.maps.LatLngBounds();
     for (var leg in this.routePath) {
     path = this.routePath[leg].getPath();
     for (var i = 0; i < path.length; i++)
     latlngbounds.extend(path.getAt(i));
     }

     this.map.fitBounds(latlngbounds);
     }
     };

     //Resetea ls campos para rutas
     this.disposeRoute = function() {
     this.directionsDisplay.setMap(null);
     console.log(this.routePath);
     for (var x in this.routePath) {
     this.routePath[x].setMap(null);
     }

     for (var x in this.routeMarkers) {
     this.routeMarkers[x].routeGoogleMarker.setMap(null);
     }
     this.routePath = new Array();
     this.routeMarkers = new Array();
     this.showAllMarkers();
     };
     */
    //Formatea una dracion de google en tiempo
    this.secs_to_hms = function(time) {
        var hours = Math.floor(time / 3600);
        var minutes = Math.floor((time % 3600) / 60);
        //var seconds = time % 60;

        var result = ' ';
        if (minutes > 0)
            result = minutes < 10 ? '0' + minutes : minutes;
        else
            result = '00' + result;
        if (hours > 0)
            result = hours + ':' + result;
        else
            result = '00:' + result;
        return result;
    };
    /*
     //Muestra unicamente los puntos vinculados a un ID de ruta
     this.showOnlyWithRoute = function(pRouteId, pApply) {
     this.onlyThisRoute = pApply;
     this.routeId = pRouteId;
     //this.regenerate();
     this.refreshIcons();
     };
     */
    //Determina si los markers debe mostrar o no un infowindow
    this.markersMustShowInfoWindow = function(aBool) {
        this.markersMustShowInfoWindow = aBool;
        for (var x in this.markers) {
            x.showInfoWindow = this.markersMustShowInfoWindow;
        }
    };

    {
        //Construyo controles personalizados
        this._constructControls();
    }


}



