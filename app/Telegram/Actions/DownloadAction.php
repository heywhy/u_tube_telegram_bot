<?php

namespace App\Telegram\Actions;

use App\Enums\VideoPlatform;
use App\Exceptions\InvalidVideoPlatformException;
use App\Support\CommandHelperTrait;
use App\Support\Helper;
use App\Support\VideoDownloader;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use Telegram\Bot\Actions;
use Telegram\Bot\Keyboard\Keyboard;

class DownloadAction extends Action
{

    use CommandHelperTrait;

    /**
     * @var Repository
     */
    protected $cacheRepo;

    /**
     * @var VideoDownloader
     */
    protected $videoDownloader;

    /**
     * @param  \Illuminate\Contracts\Cache\Repository  $cacheRepo
     * @param  \App\Support\VideoDownloader            $videoDownloader
     */
    public function __construct(Repository $cacheRepo, VideoDownloader $videoDownloader)
    {
        $this->cacheRepo = $cacheRepo;
        $this->videoDownloader = $videoDownloader;
    }


    /**
     *
     */
    public function call($argument)
    {
        try {
            $supportedPlatforms = implode(', ', VideoPlatform::getValues());
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            if (empty($argument)) {
                $message = <<<MSG
                Usage: /download [service] [url]

                Supported platforms: $supportedPlatforms
                MSG;
                return $this->replyWithMessage(['text' => $message,]);
            }

            [$service, $url] = $this->videoDownloader->getDownloadInfo($argument);

            $this->answerCallbackQuery(['text' => 'Processing...',]);

            if ($service->is(VideoPlatform::Youtube())) {
                return $this->processYoutubeDownload($url);
            }
            // $this->telegram->replyKeyboardHide();
        } catch (InvalidVideoPlatformException $e) {
            $this->replyWithMessage([
                'text' => 'Unknown video provider e.g. /download (youtube) url',
            ]);
        }

    }


    protected function processYoutubeDownload(string $url)
    {
        $resolutions = $this->videoDownloader
            ->getResolutions(VideoPlatform::Youtube(), $url);

        if (empty($resolutions)) {
            return $this->replyWithMessage([
                'text' => "Video can't be downloaded"
            ]);
        }

        $this->replyWithMessage([
            'text' => 'Choose resolution/format',
            'reply_markup' => $this->getResolutionsButtons($resolutions),
        ]);
    }

    protected function getResolutionsButtons(array $resolutions)
    {
        $buttons = [];

        $callback = function (array $resolution) {
            return Arr::only($resolution, ['url', 'format']);
        };

        $this->addUserState([
            'video_resolutions' => array_map($callback, Helper::cleanArray($resolutions))
        ]);

        foreach ($resolutions as $resolution) {
            array_push($buttons, [
                'text' => $resolution['format'],
                'callback_data' => '/pull_video ' . $resolution['format']
            ]);
        }

        return Keyboard::make([
            'inline_keyboard' => [$buttons]
        ]);
    }
}
