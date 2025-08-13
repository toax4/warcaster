<?php

if (!function_exists('assetJs')) {
    function assetJs($path)
    {
        return asset('/assets/js/' . ltrim($path, '/'));
    }
}
if (!function_exists('assetPlugin')) {
    function assetPlugin($path)
    {
        return asset('/assets/plugins/' . ltrim($path, '/'));
    }
}
if (!function_exists('assetImg')) {
    function assetImg($path)
    {
        return asset('/assets/media/' . ltrim($path, '/'));
    }
}
if (!function_exists('assetFonts')) {
    function assetFonts($path)
    {
        return asset('/assets/fonts/' . ltrim($path, '/'));
    }
}
if (!function_exists('assetCss')) {
    function assetCss($path)
    {
        return asset('/assets/css/' . ltrim($path, '/'));
    }
}

// if (!function_exists('asset')) {
//     function asset($path, $secure = null)
//     {
//         // Exemple : forcer un chemin custom
//         return \Illuminate\Support\Facades\URL::asset('assets/' . ltrim($path, '/'), $secure);
//     }
// }
