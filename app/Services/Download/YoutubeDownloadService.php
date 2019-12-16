<?php

namespace App\Services\Download;

use Google_Service_YouTube;
use App\Contracts\DownloaderService;

class YoutubeDownloadService implements DownloaderService
{

    public Google_Service_YouTube $client;

    /**
     * @param Google_Service_YouTube $youtube
     */
    public function __construct(Google_Service_YouTube $youtube)
    {
        $this->client = $youtube;
    }


    public function download(string $url)
    {
        return $this->client->videos->getRating($url, [
            'key' =>  env('YOUTUBE_API_KEY')
        ]);
    }
}
