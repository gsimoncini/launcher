<?php

/*
 * Campo de ingreso de hora.
 */

/**
 * Description of AmountField
 *
 * @author Mirco Bombieri
 */
//TODO No se está contemplando si se pegan valores con clic derecho->pegar.

class TouchField extends TextField {

    var $numeric;
    var $dot;

    function TouchField($pName, $pValue, $pLabel, $pClass = '', $pAttributes = '', $pEnabled = true, $pVisible = true, $numeric = false, $dot = false) {
        parent::TextField($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        if (!$dot) {
            $this->_type = 'text';
        } else {
            $this->_type = 'password';
        }

        $this->numeric = $numeric;
        $this->dot = $dot;
    }

    public function generate() {
        $defaults = array('value' => $this->getValue(), 'name' => $this->getName(), 'id' => $this->getName());

        if ($this->isVisible()) {
            if (!$this->isEnabled()) {
                $this->addClass('disabled');
                $defaults['readonly'] = 'READONLY';
            }
        } else
            $this->addClass('invisible');

        $defaults['class'] = $this->getClass();
        //Define el input y el boton lateral para que abra el calendario
        $result = '<input type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' />';

        //El siguiente script valida que los ingresado sea numero. aparte deja utilizar las teclas punto, flechas tab y borrar.  

        if ($this->numeric) {

//            Teclado numerico
            $result.="
                <script>
                
                    $(function(){
                   
            $('#" . $this->name . "')
 .keyboard({
  layout : 'num',
  restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
  preventPaste : true,  // prevent ctrl-v and right click
  autoAccept : true
 })
 .addTyping();    });
            </script>";
        } else {

            //            Teclado texto
            $result.="<script>
		$(function(){
			$('#" . $this->name . "').keyboard();
		});
	</script>";
        }
        return $result;
    }

}

?>
