<?php

namespace App\Telegram\Commands;

use App\Enums\NavigationActions;
use App\Enums\YoutubeSearchStates;
use App\Support\CommandHelperTrait;
use Google_Service_YouTube;
use Illuminate\Cache\Repository;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class RouteCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'command:name';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var Repository
     */
    protected $cacheRepo;

    /**
     * @var Google_Service_YouTube
     */
    protected $youtube;


    public function __construct(
        Repository $cacheRepo,
        Google_Service_YouTube $youtube_service
    ){
        $this->cacheRepo = $cacheRepo;
        $this->youtube = $youtube_service;
    }

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $data = $this->getUserData();

        if ($data == null) {
            $this->triggerCommand('start', $arguments);
        } else {
            $this->process($arguments, $data);
        }
    }

    protected function process($arguments, $data)
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        switch ($data['state']) {
            case YoutubeSearchStates::Navigation:
                if ($arguments === NavigationActions::Cancel) {
                    $this->cacheRepo->delete($this->getUserKey());
                    $this->replyWithMessage([
                        'text' => 'Search cancelled',
                        'reply_markup' => Keyboard::make(['remove_keyboard' => true,]),
                    ]);
                    break;
                }

                $token = $arguments === NavigationActions::Next
                    ? $data['nextToken'] : $data['previousToken'];
                $this->cacheRepo->set($this->getUserKey(), array_merge($data, ['pageToken' => $token]));

                $this->triggerCommand('youtube_search', $data['query']);
                break;
        }
        dump(['on routing => ', $arguments, $data]);
    }
}
