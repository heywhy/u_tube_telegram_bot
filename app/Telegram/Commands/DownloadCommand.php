<?php

namespace App\Telegram\Commands;

use App\Support\CommandHelperTrait;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

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
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $this->replyWithChatAction([
            'action' => Actions::TYPING
        ]);

        $this->replyWithMessage(['text' => 'Enter video url']);
        $this->addUserState(['route' => 'download']);
    }
}
