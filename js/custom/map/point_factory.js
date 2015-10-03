/*
 Clase que permite punteaer en el mapa y ejecutar una función luego de confirmar el punto seleccionado
 */

function Point_Factory(p_google_map, p_marker) {
    //CONSTANTES
    //-----------------------
    //@Nothing

    //ATRIBUTOS DE INSTANCIA
    //-----------------------
    //Marcador temporal
    this.marker = null;
    //Ventana de Confirmación
    this.window = null;

    //Referencia al mapa
    this.map = null;

    this.APROX = 1;
    this.EXACT = 2;


    //LOGICA DEL CONSTRUCTOR
    //-----------------------
    {
        this.map = p_google_map;
        this.window = new google.maps.InfoWindow({content: ''});

        if (typeof p_marker != 'undefined') {
            this.marker = p_marker;
            this.map.setCenter(this.marker.getPosition());
        }

    }

    //METODOS
    //-----------------------

    //Permite al usuario seleccionar un punto del mapa
    /*
     * Devuelve un Google Marker
     */
    this.getManualPoint = function(p_callback, p_cancel_callback) {
        var center;
        if (this.marker != null) {
            center = this.marker.getPosition();
            this.marker.setMap(null);
        } else {
            center = this.map.getCenter();
        }
        this.map.setCenter(center);

        this.marker = new google.maps.Marker({
            position: center,
            map: this.map,
            icon: this.getIcon(),
            animation: google.maps.Animation.BOUNCE,
            draggable: true
        });
        var self = this;
        var infow = this.window;

        var div = $('<div class="group_btns" style="text-align:center;">' + language.line('message_marcker_here_question') + '<br/></div>');

        //Boton de punteo manual aproximado
        var aproxButton = $('<a>', {
            id: 'confirm-manual-aprox-point',
            text: language.line('label_approximate'),
            title: language.line('label_confirm_approximate'),
            "class": 'btn btn-primary btn-sm'}).on('click', function() {
            self.cancelManualPoint(p_cancel_callback);
        });
        $(document).off('click', '#confirm-manual-aprox-point').on('click', '#confirm-manual-aprox-point', function() {
            $('#confirm-manual-aprox-point').addClass('disabled').text('Guardando...');
            self.confirmManualPoint(p_callback, self.APROX);
        });
        //Boton de punteo manual exacto
        var exactButton = $('<a>', {
            id: 'confirm-manual-exact-point',
            text: language.line('label_exact'),
            title: language.line('label_confirm_exact'),
            "class": 'btn btn-warning btn-sm'}).on('click', function() {
            self.cancelManualPoint(p_cancel_callback);
        });
        $(document).off('click', '#confirm-manual-exact-point').on('click', '#confirm-manual-exact-point', function() {
            $('#confirm-manual-exact-point').addClass('disabled').text(language.line('message_saving'));
            self.confirmManualPoint(p_callback, self.EXACT);
        });

        var cancelButton = $('<a>', {
            id: 'cancel-manual-point',
            text: language.line('label_cancel'),
            title: language.line('label_cancel_location'),
            "class": 'btn btn-default btn-sm'});
        $(document).off('click', '#cancel-manual-point').on('click', '#cancel-manual-point', function() {
            self.cancelManualPoint(p_cancel_callback);
        });

        div.append(aproxButton);
        div.append(exactButton);
        div.append(cancelButton);

        //Armo el infoWindow de confirmación
        google.maps.event.addListener(this.marker, 'mouseup', function() {
            infow.setContent(div.html() + '<br/><br/>');
            infow.open(p_google_map, self.marker);
        });
    };

    //Confirma el punto manual
    this.confirmManualPoint = function(p_callback, pType) {

        this.marker.setAnimation(google.maps.Animation.DROP);
        this.marker.setDraggable(false);
        this.marker.setVisible(true);

        this.window.close();

        var mark = this.marker;
        this.marker.setMap(null);
        google.maps.event.clearInstanceListeners(this.marker);
        this.marker = null;
        if (typeof p_callback != 'undefined')
            p_callback(mark, pType);
    };

    //Cancela el punteo manual
    this.cancelManualPoint = function(p_callback) {
        this.window.close();
        if (this.marker != null) {
            this.marker.setMap(null);
            google.maps.event.clearInstanceListeners(this.marker);
        }
        if (typeof p_callback != 'undefined')
            p_callback();
    };

    this.getIcon = function() {
        var icon_url = baseUrl + '/img/custom/map/icon/';
        var image_icon = icon_url + '/pointing.png';

        var image = {
            url: image_icon,
            size: new google.maps.Size(32, 32),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(16, 32)
        };
        return image;
    };

    //Devuelve un address a partir de un geocodeResult
    this.getAddressFromGeocodeResult = function(p_result) {
        console.log(p_result);
        var address = {};
        //Calle
        var street = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("street_address", value.types) >= 0 || jQuery.inArray("route", value.types) >= 0;
        });
        //Numero
        var street_number = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("street_number", value.types) >= 0;
        });

        address.address = street[0] != null ? (street[0].long_name + ' ' + (street_number[0] != null ? street_number[0].long_name : '')) : '';
        address.street = street[0] != null ? (street[0].long_name) : '';
        address.number = street_number[0] != null ? (street_number[0].long_name) : '';
        address.coord_x = p_result.geometry.location.lat();
        address.coord_y = p_result.geometry.location.lng();

        //Codigo postal
        var postal_code = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("postal_code", value.types) >= 0;
        });
        if (postal_code != null && postal_code != '')
            address.zip_code = postal_code[0].long_name;
        else
            address.zip_code = '';

        //Pais
        var country = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("country", value.types) >= 0;
        });
        address.country = country[0].long_name;
        //Ciudad
        var city = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("locality", value.types) >= 0;
        });
        if (typeof (city[0]) != 'undefined') {
            address.city = city[0].long_name;
        } else {
            address.city = '';
        }
        //Provincia
        var state = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("administrative_area_level_1", value.types) >= 0;
        });
        try {
            address.state = state != null ? state[0].long_name : '';
        } catch (e) {
            address.state = '';
        }
        //Partido/Departamento
        var department = jQuery.grep(p_result.address_components, function(value) {
            return  jQuery.inArray("administrative_area_level_2", value.types) >= 0 || jQuery.inArray("political", value.types) >= 0;
        });
        try {
            address.department = department != null ? department[0].long_name : '';
        } catch (e) {
            address.department = '';
        }

        address.notes = language.line('label_information_from_google_maps');

        return address;
    };
}
