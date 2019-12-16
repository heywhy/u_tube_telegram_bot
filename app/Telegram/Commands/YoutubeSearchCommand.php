<?php

namespace App\Telegram\Commands;

use Google_Service_YouTube;
use Google_Service_YouTube_SearchListResponse;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class YoutubeSearchCommand extends Command
{
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
    protected $description = 'Command description';

    protected Google_Service_YouTube $youtube;

    public function __construct(Google_Service_YouTube $youtube_service)
    {
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

        $results = $this->youtube->search
            ->listSearch('id,snippet', ['q' => $this->getArguments(),]);
        $limit = count($results->getItems());

        logger()->info('count', [count($results)]);

        foreach ($results->getItems() as $key => $item) {
            $snippet = $item->snippet;

            $text = <<<E
            Title: <b>{$snippet->title}</b>

            Description: {$snippet->description}

            <a href="https://youtube.com/watch?v={$item->id->videoId}">Watch</a>
            E;

            $replyKeyboard = ((int)$key + 1) >= $limit
                ? $this->getPaginationKeyboard($results) : null;

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $replyKeyboard,
            ]);
            // $this->replyWithPhoto([
            //     'photo' => $snippet->thumbnails->default->url
            // ]);
        }
    }

    protected function getPaginationKeyboard(Google_Service_YouTube_SearchListResponse $response)
    {
        return $this->telegram->replyKeyboardMarkup([
            'keyboard' => [['Previous', 'Next'],],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
    }
}
