<?php

namespace App\Telegram\Commands;

use App\Support\CommandHelperTrait;
use App\Telegram\Actions\DownloadAction;
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

        $this->addUserState(['route' => 'download']);
        if (is_null($arguments)) {
            $this->replyWithMessage(['text' => 'Enter video url']);
        } else {
            /** @var \App\Telegram\Commands\RouteCommand */
            $command = app(RouteCommand::class);

            $command->make($this->telegram, $arguments, $this->getUpdate());
        }
    }
}
