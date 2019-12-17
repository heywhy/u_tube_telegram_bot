<?php

namespace App\Telegram\Commands;

use App\Enums\NavigationActions;
use App\Enums\YoutubeSearchStates;
use App\Support\CommandHelperTrait;
use Google_Service_YouTube;
use Google_Service_YouTube_SearchListResponse;
use Google_Service_YouTube_SearchResult;
use Illuminate\Cache\Repository;
use Illuminate\Support\Arr;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class YoutubeSearchCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'youtube_search';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Searches youtube for videos matching the given name';

    /**
     *
     */
    protected Google_Service_YouTube $youtube;

    /**
     *
     */
    protected Repository $cacheRepo;

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
        $this->replyWithChatAction([
            'action' => Actions::TYPING
        ]);

        $results = $this->youtube->search->listSearch('id,snippet', [
            'q' => $this->getArguments(),
            'pageToken' => $this->getPageToken()
        ]);
        $limit = count($results->getItems());

        $this->setState($this->getArguments(), $results);

        foreach ($results->getItems() as $key => $item) {
            $snippet = $item->snippet;

            $text = <<<E
            Title: <b>{$snippet->title}</b>

            <a href="{$this->watchUrl($item)}">Watch</a>
            E;

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $this->watchButton($item),
            ]);
        }

        $this->replyWithMessage([
            'text' => 'Done',
            'reply_markup' => $this->getPaginationKeyboard($results)
        ]);
    }

    protected function watchUrl(Google_Service_YouTube_SearchResult $item): string
    {
        return "https://youtube.com/watch?v={$item->id->videoId}";
    }

    protected function watchButton(Google_Service_YouTube_SearchResult $item)
    {
        return Keyboard::make([
            'inline_keyboard' => [[
                ['text' => 'Watch', 'url' => $this->watchUrl($item)]
            ]]
        ]);
    }

    protected function getPaginationKeyboard()
    {
        $data = $this->getUserData();
        $keyboard = [];
        if (Arr::has($data, 'previousToken') && $data['previousToken'] != null)
            array_push($keyboard, NavigationActions::Previous);
        $keyboard = [...$keyboard, NavigationActions::Cancel];
        if (Arr::has($data, 'nextToken') && $data['nextToken'] != null)
            array_push($keyboard, NavigationActions::Next);

        // $this->telegram->replyKeyboardHide()

        return Keyboard::make([
            'keyboard' => [$keyboard],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
    }

    protected function setState(string $q, Google_Service_YouTube_SearchListResponse $response)
    {
        $this->cacheRepo->set($this->getUserKey(), [
            'query' => $q,
            'nextToken' => $response->getNextPageToken(),
            'previousToken' => $response->getPrevPageToken(),
            'state' => YoutubeSearchStates::Navigation,
        ]);
    }

    protected function getPageToken(): ?string
    {
        $data = $this->getUserData();
        if ($data == null || $data['state'] != YoutubeSearchStates::Navigation) {
            return null;
        }
        return Arr::has($data, 'pageToken') ? $data['pageToken'] : null;
    }
}
