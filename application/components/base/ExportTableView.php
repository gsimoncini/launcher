<?php

class ExportTableView {

    var $target;
    var $url;
    var $id = '';
    var $tooltip = '';
    var $withDetail = false;
    public static $counterInstance = 0;

    function ExportTableView($url, $pController, $pTarget = 'tableTarget', $pWithDetail = false) {
        $this->setTarget($pController . '.' . $pTarget);
        $this->setUrl($url);

        //Genera id dinamico
        self::$counterInstance++;
        $id = 'exportTable_' . self::$counterInstance;
        $this->setId($id);
        $this->setWithDetail($pWithDetail);
    }

    function generate() {
        $html = '';

        $html .= '<div class="btn-group btn-group-sm">';
        $html .= '<button id="' . $this->id . '" type="button" class="btn btn-inverse dropdown-toggle" data-toggle="dropdown"><i class="fa fa-download"></i></button>';
        $html .= '<ul class="dropdown-menu" role="menu">';

        if ($this->pWithDetail) {
            $html .= '<li><a onclick="baseController.chooseExportMode(\'' . $this->url . '\',\'Excel2007\');"><i class="fa fa-file-excel-o"></i> Excel</a></li>';
            $html .= '<li><a onclick="baseController.chooseExportMode(\'' . $this->url . '\',\'PDF\');" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>';
            $html .= '<li><a onclick="baseController.chooseExportMode(\'' . $this->url . '\',\'CSV\');" ><i class="fa fa-file-text-o "></i> CSV</a></li>';
        } else {
            $html .= '<li><a href="' . $this->url . '/Excel2007" ><i class="fa fa-file-excel-o"></i> Excel</a></li>';
            $html .= '<li><a href="' . $this->url . '/PDF" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>';
            $html .= '<li><a href="' . $this->url . '/CSV" ><i class="fa fa-file-text-o "></i> CSV</a></li>';
        }

        $html .= '</ul>';
        if ($this->tooltip != '') {
            $html .='<script>
                    $(document).ready(function(){
                        $("#' . $this->id . '").tooltip({title:"' . ($this->tooltip) . '", placement: "bottom"});
                    });
                </script>';
        }
        $html .= '</div>';



        return $html;
    }

    function setTarget($pTarget) {
        $this->target = $pTarget;
    }

    function setItems($pItems) {
        $this->items = $pItems;
    }

//
    function getTarget() {
        return $this->target;
    }

//
    function getItems() {
        return $this->items;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getId() {
        return $this->id;
    }

    public function getTooltip() {
        return $this->tooltip;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;
    }

    public function getWithDetail() {
        return $this->pWithDetail;
    }

    public function setWithDetail($pWithDetail) {
        $this->pWithDetail = $pWithDetail;
    }

}

?>