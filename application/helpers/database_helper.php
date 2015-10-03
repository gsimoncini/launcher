<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Implementa algunas funciones que facilitan el tratamiento de datos con base de datos.-
 */


//Prepara un campo booleano para almacenarlo en base de datos.
if (!function_exists('prep_field_null')) {

    //Tipos permitidos: str(string), numeric, bool y date.
    //El tipo ORIGINAL-DATE corresponde a una fecha a la cual no debe alterarse su formato.
    function prep_field_null($pField, $pType = 'str', $pAceptNull = true) {

        if ($pType == 'string')
            $pType = 'str';
        if ($pType == 'boolean')
            $pType = 'bool';
        if (in_array($pType, array('numeric', 'integer', 'double')))
            $pType = 'numeric';

        $not_defined = ($pField == null || $pField === false);
        if ($not_defined && $pAceptNull) {
            //Si la variable es null y el campo admite null
            $pField = "NULL";
        } else {
            switch ($pType) {
                case 'str':
                    if ($not_defined) {
                        //Si es un string y el campo no admite null
                        $pField = "''";
                    } else {
                        $pField = "'" . $pField . "'";
                    }
                    break;
                case 'numeric':
                    if ($not_defined) {
                        //Si es un número y el campo no admite null
                        $pField = 0;
                    }
                    break;
                case 'bool':
                    if ($not_defined) {
                        //Si es un booleano y el campo no admite null
                        $pField = "FALSE";
                    }
                    break;
                case 'date':
                    //Si es una fecha y el campo no admite null
                    $pField = "'" . to_mysql_date($pField) . "'";
                    break;
                case 'original-date':
                    //Si es una fecha lista para base de datos y el campo no admite null
                    $pField = "'" . $pField . "'";
                    break;
            }
        }

        return $pField;
    }

//Prepara el sql para un update según los atributos que tiene el objeto
    if (!function_exists('generate_update_sql')) {


        function generate_update_sql($pTableName, $pObject, $pKey) {

            $sql = 'UPDATE ' . $pTableName . ' SET ';
            $separator = ' ';
            $refObject = new ReflectionObject($pObject);
            foreach ($refObject->getProperties() AS $property) {
                if ($property->isPublic() && $property->getName() != $pKey) {
                    $attr = $property->getName();
                    $sql .= $separator . $property->getName() . ' = ' . prep_field_null($pObject->$attr, gettype($pObject->$attr));
                    $separator = ' , ';
                }
            }
            $sql .= ' WHERE ' . $pKey . ' = ' . $pObject->$pKey . ' ; ';

            return $sql;
        }

    }
}
?>
