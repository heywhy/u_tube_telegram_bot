<?php

namespace App\Services\Video;

use Google_Service_YouTube;
use App\Contracts\VideoProviderSevice;
use YouTubeDownloader;

class YoutubeService implements VideoProviderSevice
{

    /**
     * @var Google_Service_YouTube
     */
    protected $youtube;

    /**
     * @var YouTubeDownloader
     */
    protected $downloader;

    /**
     * @param  Google_Service_YouTube  $youtube
     * @param  YouTubeDownloader       $downloader
     */
    public function __construct(Google_Service_YouTube $youtube, YouTubeDownloader $downloader)
    {
        $this->youtube = $youtube;
        $this->downloader = $downloader;
    }

    public function search(array $params)
    {
        return $this->youtube->search->listSearch('id,snippet', $params);
    }


    public function download(string $url)
    {
        return $this->client->videos->getRating($url, [
            'key' =>  env('YOUTUBE_API_KEY')
        ]);
    }

    public function getResolutions(string $url)
    {
        return $this->downloader->getDownloadLinks($url);
    }
}
