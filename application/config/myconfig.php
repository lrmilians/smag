<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/* Definicion de Expresiones regulares */

$config['expresiones_regulares']['codigoProducto']                  = "/^[a-zA-Z0-9\.\_\-\s]{1,100}$/";
$config['expresiones_regulares']['codigoBarrasProducto']            = "/^[a-zA-Z0-9\.\_\-\s]{1,100}$/";
$config['expresiones_regulares']['nombreProducto']                  = "/^[a-zA-Z0-9\.\_\-\s]{1,250}$/";
$config['expresiones_regulares']['referenciaProducto']              = "/^[a-zA-Z0-9\.\_\-\s]{0,200}$/";
$config['expresiones_regulares']['descripcionProducto']             = "/^[a-zA-Z0-9\(\)\#\@\!\<\>\?\-\$\%\_\,\;\.\s]{0,10000}$/";

$config['expresiones_regulares']['texto']                           = "/^[a-zA-Z0-9\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\(\)\&\#\@\!\<\>\?\-\$\%\_\,\;\.\s]{0,10000}$/";
$config['expresiones_regulares']['decimal186']                      = "/^[0-9]{1,12}(\.[0-9]{2,6})$/";
$config['expresiones_regulares']['decimal126']                      = "/^[0-9]{1,6}(\.[0-9]{2,6})$/";
$config['expresiones_regulares']['decimal102']                      = "/^[0-9]{1,8}(\.[0-9]{2,2})$/";
$config['expresiones_regulares']['decimal166']                      = "/^[0-9]{1,10}(\.[0-9]{2,6})$/";
$config['expresiones_regulares']['decimal62']                       = "/^[0-9]{1,4}(\.[0-9]{2,2})$/";

