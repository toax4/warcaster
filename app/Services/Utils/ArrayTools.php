<?php

namespace App\Services\Utils;

class ArrayTools
{
    /**
     * Trouve la première clé correspondant à un filtre dans un tableau.
     *
     * @param array $array Le tableau à parcourir
     * @param callable $callback La fonction de filtre (reçoit chaque élément)
     * @return int|string|null La clé si trouvée, sinon null
     */
    public static function findKey(array $array, callable $callback)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return null;
    }
}
