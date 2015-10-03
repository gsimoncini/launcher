<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Id de base de datos de la imagen por defecto tomada por toda la aplicación
  |--------------------------------------------------------------------------
  |
 */
$config['default_image_id'] = 1;

/*
  |--------------------------------------------------------------------------
  | Campos de usuario obligatorios por defecto (true o false)
  |--------------------------------------------------------------------------
  |
 */
$config['required_name'] = TRUE;
$config['required_last_name'] = TRUE;
$config['required_username'] = TRUE;
$config['required_email'] = TRUE;
$config['required_birth_date'] = FALSE;
$config['required_doc_type'] = FALSE;
$config['required_doc_number'] = FALSE;
$config['required_phone'] = FALSE;
$config['required_user_clients'] = FALSE;

 
/*
  |--------------------------------------------------------------------------
  | Theme de la Aplicacion
  |--------------------------------------------------------------------------
  |
 */
$config['theme_name'] = 'bombieri';
$config['jtable_theme'] = 'disney';
 

/*
  |--------------------------------------------------------------------------
  | Logo
  |--------------------------------------------------------------------------
  |
 */

$config['default_logo'] = 'img/base/logo.png';
