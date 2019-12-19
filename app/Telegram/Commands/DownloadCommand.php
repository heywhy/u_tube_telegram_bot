<?php

namespace App\Telegram\Commands;

use App\Services\Video\YoutubeService;
use App\Support\CommandHelperTrait;
use App\Support\Helper;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class DownloadCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'download';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Download video from the given url';

    /**
     * @var YoutubeService
     */
    protected $youtube;

    /**
     * @var Repository
     */
    protected $cacheRepo;

    /**
     * @param  YoutubeService  $youtube
     */
    public function __construct(Repository $cacheRepo, YoutubeService $youtube)
    {
        $this->youtube = $youtube;
        $this->cacheRepo = $cacheRepo;
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        if (empty($arguments)) return;

        [$service, $url] = preg_split("/\s/", $arguments, 2);

        if (!is_null($this->getCallbackQuery())) {
            $this->answerCallbackQuery(['text' => 'Processing...',]);
        }
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        switch (mb_strtolower($service)) {
            case 'youtube':
                $this->processYoutubeDownload($url);
                break;
            default:
                $this->replyWithMessage([
                    'text' => 'Unknown video provider e.g. /download (youtube) url',
                ]);
        }
        // $this->telegram->replyKeyboardHide();
    }

    protected function processYoutubeDownload(string $url)
    {
        $resolutions = $this->youtube->getResolutions($url);

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
