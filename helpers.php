<?php

if (!function_exists('contabo')) {
    function contabo()
    {
        return new App\Services\Contabo\ContaboAPI();
    }
}