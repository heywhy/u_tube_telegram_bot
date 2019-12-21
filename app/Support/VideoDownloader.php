<?php

namespace App\Support;

use App\Enums\VideoPlatform;
use App\Exceptions\InvalidVideoPlatformException;
use App\Services\Video\YoutubeService;

class VideoDownloader
{

    /**
     * @var YoutubeService
     */
    protected $youtube;

    /**
     * @param YoutubeService $youtube
     */
    public function __construct(YoutubeService $youtube)
    {
        $this->youtube = $youtube;
    }

    /**
     * Return the resolutions for the given url.
     *
     * @param VideoPlatform $videoPlatform
     * @param string $url
     * @return array|null
     */
    public function getResolutions(VideoPlatform $videoPlatform, string $url): ?array
    {
        $resolutions = null;

        switch ($videoPlatform->value) {
            case VideoPlatform::Youtube:
                $resolutions = $this->youtube->getResolutions($url);
                break;
        }

        return $resolutions;
    }

    /**
     * Get download information from the given string, it auto
     * detect the service from the url if no service is specified.
     *
     * @param string $arguments
     * @return array
     */
    public function getDownloadInfo(string $arguments): array
    {
        $url = null;
        $service = $arguments;
        $args = preg_split("/\s/", $arguments);

        if (count($args) > 1) {
            $service = $args[0];
            unset($args[0]);
            $url = implode(' ', $args);
        }

        if (is_null($url) && is_array($info = parse_url($service))) {
            $url = $service;
            $service = $this->matchingVideoService($info['host']);
        }

        if (VideoPlatform::hasValue($service = mb_strtolower($service))) {
            return [VideoPlatform::getInstance($service), $url];
        }

        throw new InvalidVideoPlatformException("[{$service}] is an unsupported platform.");
    }

    /**
     * Deduce the service from the url.
     *
     * @param string $host
     * @return string
     */
    protected function matchingVideoService(string $host): string
    {
        switch (mb_strtolower($host)) {
            case 'youtu.be':
                return 'youtube';
            default:
                return $host;
        }
    }
}
