<?php

/*
 * Crea un dropdown que sirve para poder ordenar una jtable por sus respectivas columnas.
 */

class SortTableDropdown {

    public static $counterInstance = 0;
    private $target;
    private $id = '';
    private $tooltip = '';
    private $icon = 'fa-sort-amount-asc';
    private $items = array();
    private $visible = false;

    function SortTableDropdown($pItems, $pController, $pTarget = 'tableTarget') {
        $this->setTarget($pController . '.' . $pTarget);
        $this->setItems($pItems);

        //Genera id dinamico
        self::$counterInstance++;
        $id = 'sortTable_' . self::$counterInstance;

        $this->setId($id);
    }

    function generate() {
        $html = '';

        $html .= '<div class="btn-group btn-group-sm ' . ($this->getVisible() ? 'hide' : '') . '">';
        $html .= '<button id ="' . $this->id . '" type="button" class="btn btn-inverse dropdown-toggle" data-toggle="dropdown"><i class="fa ' . $this->icon . '"></i></button>';
        $html .= '<ul class="dropdown-menu" role="menu">';

        foreach ($this->getItems() as $item)
            $html .= '<li><a href="#" onclick="baseController.sortTable(event, this, ' . $this->target . ', \'' . $item->id . '\');">' . $item->name . ' <i class="fa"></i></a></li>';

        $html .= '</ul>';

        if ($this->tooltip != '') {
            $html .= '<script>
                          $(document).ready(function(){
                              $("#' . $this->id . '").tooltip({title:"' . ($this->tooltip) . '", placement: "bottom"});
                          });
                      </script>';
        }

        $html .= '</div>';

        return $html;
    }

    function compare($pX, $pY) {
        if (strtoupper($pX->name) == strtoupper($pY->name))
            return 0;
        else if (strtoupper($pX->name) < strtoupper($pY->name))
            return -1;
        else
            return 1;
    }

    function setTarget($pTarget) {
        $this->target = $pTarget;
    }

    public function setId($pId) {
        $this->id = $pId;
    }

    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;
    }

    function setIcon($pIcon) {
        $this->icon = $pIcon;
    }

    function setItems($pItems) {
        $this->items = $pItems;
    }

    public function setVisible($visible) {
        $this->visible = $visible;
    }

    function getTarget() {
        return $this->target;
    }

    public function getId() {
        return $this->id;
    }

    public function getTooltip() {
        return $this->tooltip;
    }

    function getItems() {
        uasort($this->items, array($this, 'compare'));

        return $this->items;
    }

    public function getVisible() {
        return $this->visible;
    }

}
