<?php

namespace App\Contracts;

interface DownloaderService
{
    function download(string $url);
}
