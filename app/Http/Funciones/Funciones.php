<?php
//Funciones Personalizadas para el Proyecto

function formatoMillares($cantidad, $decimal = 2): string
{
    if (!is_numeric($cantidad)){
        $cantidad = 0;
    }
    return number_format($cantidad, $decimal, ',', '.');
}
