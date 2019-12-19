<?php

namespace App\Telegram\Commands;

use App\Support\CommandHelperTrait;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

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
    protected $description = 'Search youtube for videos matching the query';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $this->replyWithChatAction([
            'action' => Actions::TYPING
        ]);

        $this->replyWithMessage(['text' => 'Enter search query']);
        $this->addUserState(['route' => 'search-youtube']);
    }
}
