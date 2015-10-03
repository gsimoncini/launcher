<?php

/*
 * Campo para la carga de archivos.
 */

/**
 * Description of FileUploadFied
 *
 * @author Mirco Bombieri
 */
class FileUploadField extends TextField {

    private $progressbar = true;
    private $thumbnail = true;
    private $delete = true;
    private $callback = '';
    private $image = '';

    function FileUploadField($pName, $pLabel, $pImage = false, $pCallback = '', $pClass = 'fileinput-button', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::TextField($pName, null, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->setCallback($pCallback);
        $this->setImage($pImage);
        $this->_type = 'file';
    }

    public function generate() {
        $defaults = array('name' => 'files[]', 'id' => $this->getName());

        $defaults['class'] = $this->getClass();

        $html = '';

        $html .= '<div class="panel panel-default">';
        $html .= '<div class="panel-body">';

        //Thumbnail
        $html .= $this->getThumbnailHTML();
        if (!$this->static) {
            $html.=' <div class="caption text-center">';
            //Button
            $html .= $this->getButtonHTML($defaults);
            //Progessbar
            $html .= $this->getProgressBarHTML();
            $html .= '</div>';
        }
        //Script para ejecucion
        $html .= $this->getScript();

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    //Devuelve el contenido del boton
    private function getButtonHTML($defaults) {
        return '<div>
                    <span class="btn btn-primary btn-sm fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span style="text-overflow: ellipsis;">' . $this->getLabel() . '</span>
                        <input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . '/>
                   </span>
                   <input type="hidden" name="' . $this->getName() . '" value="' . ($this->getImage() ? $this->getImage() : null) . '"/>
                   <button type="button" class="btn btn-danger btn-sm delete delete-file hide">
                       <i class="glyphicon glyphicon-trash"></i>
                       <span></span>
                    </button>
                </div>';
    }

    //Devuelve el apartardo del Thumbnail
    private function getThumbnailHTML() {
        if ($this->getThumbnail()) {
            return '<div id="media-' . $this->getName() . '">
                        <a href="#" target="_blank">
                            <img class="media-object" src="' . ($this->getImage() ? $this->getImage() : base_url('img/base/no-img.png') ) . '" alt="" style="margin: auto; max-width: 100%;">
                        </a>
                        <div class="caption">
                            <h6 class="text-center" style="text-overflow: ellipsis; overflow: hidden; line-height: 20px;"></h6>
                        </div>
                   </div>';
        }

        return '';
    }

//Devuelve el codigo de la barra de progreso
    private function getProgressBarHTML() {
        if ($this->getProgessbar()) {
            return '<div class="progress" id="progress-' . $this->getName() . '" style="margin-bottom: 0; margin-top: 15px;"> '
                    . '<div class="progress-bar  progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'
                    . '</div>';
        }
        return '';
    }

//Devuelve la formacion del script que le da vida al componente
    private function getScript() {
        $CI = &get_instance();

        $CI->load->model('custom/multimedia_model', 'Multimedia_Model');

        $multimedia_object = $CI->Multimedia_Model->get_by_id($CI->config->item('default_image_id'));


        if (!is_array($multimedia_object))
            return "<script>
                $('.delete-file').on('click',function(){
                    bootbox.confirm('" . $CI->lang->line('message_clear_image_question') . "',function(){
                        $('#media-" . $this->getName() . " .media-object').attr('src', '" . site_url($multimedia_object->url) . "');
                        $('input[name=\"" . $this->getName() . "\"]').val('" . site_url($multimedia_object->url) . "');
                        $('#progress-" . $this->getName() . " .progress-bar').attr('aria-valuenow','0');
                        $('#progress-" . $this->getName() . " .progress-bar').css('width','0%');
                        $('#media-" . $this->getName() . " .caption h6').empty();
                        $('.delete-file').addClass('hide');
                    })
                });

               if ($('input[name=\"" . $this->getName() . "\"]').val() != '" . site_url($multimedia_object->url) . "' && $('input[name=\"" . $this->getName() . "\"]').val() != '') {
                   $('.delete-file').removeClass('hide');
               }

                    $(function () {
                        $('#" . $this->getName() . "').fileupload({
                            url: '" . site_url('base/upload') . "',
                            dataType: 'json',
                            always: function(e,data) { },
                            done: function (e, data) {

                                $('input[name=\"" . $this->getName() . "\"]').val('');
                                $.each(data.result.files, function (index, file) {" .
                    ($this->getThumbnail() ?
                            "$('#media-" . $this->getName() . " .media-object').attr('src',file.url);
                                    $('#media-" . $this->getName() . " h6').html(file.name);
                                    $('#media-" . $this->getName() . " a').attr('href',file.url);" : '') .
                    "$('input[name=\"" . $this->getName() . "\"]').val(file.url); " .
                    "$('.delete-file').removeClass('hide');" .
                    $this->getCallback() . "
                    });
                    },
                            progressall: function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10); " .
                    ( $this->getProgessbar() ? "$('#progress-" . $this->getName() . " .progress-bar').css(
                                    'width',
                                    progress + '%'
                                ).attr('aria-valuenow',progress);" : '' ) .
                    "
                            }
                        });
                    });
                </script>";
    }

    public function setThumbnail($thumb) {
        $this->thumbnail = $thumb;
    }

    public function getThumbnail() {
        return $this->thumbnail;
    }

    public function setProgressbar($bool) {
        $this->progressbar = $bool;
    }

    public function getProgessbar() {
        return $this->progressbar;
    }

    public function setCallback($callback) {
        $this->callback = $callback;
    }

    public function getCallback() {
        return $this->callback;
    }

    public function setDelete($delete) {
        $this->delete = $delete;
    }

    public function getDelete() {
        return $this->delete;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getImage() {
        return $this->image;
    }

    public function printLabel() {
        return '';
    }

}

?>
