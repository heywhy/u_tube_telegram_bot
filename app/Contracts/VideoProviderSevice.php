<?php

namespace App\Contracts;

interface VideoProviderSevice
{
    function search(array $params);
    function download(string $url);
}
