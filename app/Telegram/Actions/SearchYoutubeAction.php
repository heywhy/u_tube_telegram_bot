<?php

namespace App\Telegram\Actions;

use App\Enums\NavigationActions;
use App\Enums\YoutubeSearchStates;
use App\Services\Video\YoutubeService;
use App\Support\CommandHelperTrait;
use Google_Service_YouTube_SearchListResponse;
use Google_Service_YouTube_SearchResult;
use Illuminate\Support\Arr;
use Telegram\Bot\Keyboard\Keyboard;


class SearchYoutubeAction extends Action
{

    use CommandHelperTrait;

    /**
     * @var YoutubeService
     */
    protected $youtube;

    /**
     * @param  \App\Services\Video\YoutubeService  $youtube
     */
    public function __construct(YoutubeService $youtube)
    {
        $this->youtube = $youtube;
    }


    /**
     *
     * @param  mixed  $argument
     */
    function call($argument)
    {
        $query = $this->isOnNavigation()
            ? Arr::get($this->getUserData(), 'query') : $argument;
        $results = $this->youtube->search([
            'q' => $query,
            'pageToken' => $this->getPageToken(),
        ]);

        $this->setState($query, $results);

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
        $watchUrl = $this->watchUrl($item);
        return Keyboard::make([
            'inline_keyboard' => [[
                ['text' => 'Watch', 'url' => $watchUrl],
                ['text' => 'Download', 'callback_data' => '/download youtube ' . $watchUrl],
            ]]
        ]);
    }

    protected function getPaginationKeyboard()
    {
        $data = $this->getUserData();
        $keyboard = [];
        if (Arr::has($data, 'previousToken') && $data['previousToken'] != null)
            $keyboard[] = ['text' => 'Previous', 'callback_data' => '/navigate ' . NavigationActions::Previous];
        $keyboard[] = ['text' => 'Cancel', 'callback_data' => '/navigate ' . NavigationActions::Cancel];
        if (Arr::has($data, 'nextToken') && $data['nextToken'] != null)
            $keyboard[] = ['text' => 'Next', 'callback_data' => '/navigate ' . NavigationActions::Next];

        return Keyboard::make([
            'inline_keyboard' => [$keyboard],
        ]);
    }

    protected function setState(string $q, Google_Service_YouTube_SearchListResponse $response)
    {
        $this->addUserState([
            'query' => $q,
            'navigation' => null,
            'nextToken' => $response->getNextPageToken(),
            'previousToken' => $response->getPrevPageToken(),
            'state' => YoutubeSearchStates::Navigation,
        ]);
    }

    protected function getPageToken(): ?string
    {
        if (!$this->isOnNavigation()) {
            return null;
        }
        $data = $this->getUserData();
        $navigation = NavigationActions::getInstance(Arr::get($data, 'navigation'));
        if ($navigation->is(NavigationActions::Next())) {
            return Arr::get($data, 'nextToken', null);
        }
        return Arr::get($data, 'previousToken', null);
    }

    protected function isOnNavigation()
    {
        $data = $this->getUserData();

        return !is_null($data) && !is_null(Arr::get($data, 'navigation', null));
    }
}
