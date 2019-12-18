<?php

namespace App\Support;

class Helper
{

    static function cleanArray($array): array
    {
        $result = [];
        foreach ($array as $item) {
            array_push($result, $item);
        }

        return $result;
    }
}
