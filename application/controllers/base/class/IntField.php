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

class IntField extends TextField {

    var $size = 12;
    var $onblur = '';
    var $negative = false;

    function IntField($pName, $pValue, $pLabel, $pClass = 'amountfield', $pAttributes = '', $pEnabled = true, $pVisible = true, $pNegative = false) {
        parent::TextField($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'text';
        $this->negative = $pNegative;
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
        $result = '<input onkeypress="just_number_int(this,event);" onblur="dot_at_the_end(this,event); ' . $this->onblur . '" maxlength="' . $this->size . '" type="' . $this->_type . '" ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' />';

        //El siguiente script valida que los ingresado sea numero. aparte deja utilizar las teclas punto, flechas tab y borrar.  
        $result .= "
        <script type=\"text/javascript\">
        function just_number_int(input,event){   
// permitimos teclas de navegación y eliminación
if((event.charCode ==0)){
}else{
// permitimos solo numeros
";

        if ($this->negative) {
            $result .= "if ( (event.which >= 48 && event.which <= 57)||event.which==45) ";
        } else {
            $result .= "if ( (event.which >= 48 && event.which <= 57)) ";
        }
        $result .="
            {               
            // obtenemos el valor del input
            var strvalue = $('#' + input.id).val();
            // no permitimos que se ingresen dos puntos
            if ( event.which == 46 && (strvalue.split('.').length)>1 ) 
                {
                event.preventDefault(); 
                }
           return true;             
            } else {        
                event.preventDefault();                  
            }
}     
}

 function dot_at_the_end(input,event){ 
 var strvalue = $('#' + input.id).val();
 
 //verificamos si tiene punto
 if(strvalue.split('.').length>1)
 {
    //comprobamos si el punto esta al final
    var splitted_string=strvalue.split('.');
    if(splitted_string[1].length==0)
        {
            //suprimimos el punto final            
            $('#' + input.id).attr('value', strvalue.substring(0, strvalue.length-1));            
        }
}

 //verificamos si tiene signo negativo
 if(strvalue.split('-').length>1)
 {
    //el signo negativo solo al comienzo
    var negative = '-';    
    $('#' + input.id).attr('value', negative.concat(strvalue.replace(/-/g,'')));   
}
}

</script>";

        return $result;
    }

    public function setSize($pNumber) {
        $this->size = $pNumber;
    }

    public function setOnblur($function) {
        $this->onblur = $function;
    }

}

?>
