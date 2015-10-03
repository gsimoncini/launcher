/**********************************/
/* Visualizador de de Imagenes    */
/**********************************/

function ImagesGallery() {
//####Atributos
    this.files = [];
    this.classMinImage = null;
    this.classWrapperMinImage = null;
    this.wapperGallery = null;
    this.classBigImage = null;


//El magico self
    var self = this;
    {

    }
//####Metodos
    this.setFiles = function(files) {
        self.files = files;
    };

    this.setClassMinImage = function(classMinImage) {
        self.classMinImage = classMinImage;
    };

    this.setWrapperMinImage = function(classWrapperMinImage) {
        self.classWrapperMinImage = classWrapperMinImage;
    };

    this.setWapperGallery = function(wapperGallery) {
        self.wapperGallery = wapperGallery;
    };

    this.setClassBigImage = function(classBigImage) {
        this.classBigImage = classBigImage;
    };

    this.generate = function(contentTarguet) {
        var html = '';

        html += ' <div class="' + self.wapperGallery + '">';
        html += ' <div class="row">';
        html += '    <div class="col-md-10 ' + self.classWrapperMinImage + '">';
        html += '        <div class="row">';

        if (!self.files['status']) {
            html += '<span class="alert alert-warning"><i class="fa fa-warning"></i> ' + self.files['message'] + '</span><br/>';
        }

        if (self.files['response'].length > 0)
            $.each(self.files['response'], function(index, value) {
                html += '<img id="' + value.name + '_' + index + '" class="' + self.classMinImage + '"  src="' + value.url + '" data-index="' + index + '">';
            });

        html += '        </div>';
        html += '      </div>';
        html += '    <div class="col-md-10 wrapper-big-image">';
        html += '        <img class="' + self.classBigImage + '"  src="" alt="">';
        html += '  </div>';
        html += ' </div>';
        html += '</div>';

        $('#' + contentTarguet).append(html);


        $('.' + self.classMinImage).click(function() {
            self.viewBigImage(this);
        });

        return true;
    };

    this.viewBigImage = function(element) {
        var index = $(element).attr('data-index');
        var url = self.files['response'][index].url;
        $('.' + self.classBigImage).attr('src', url);
    };

}


