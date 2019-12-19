<?php

namespace App\Telegram\Commands;

use App\Enums\NavigationActions;
use App\Support\CommandHelperTrait;
use Telegram\Bot\Commands\Command;

class NavigationCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'navigate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Set the navigation state and call the router to handle the request';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        if (NavigationActions::getInstance($arguments)->is(NavigationActions::Cancel())) {
            $this->setUserState(null);
            $this->replyWithMessage(['text' => 'Search cancelled',]);
            return $this->triggerCommand('help');
        }

        $this->addUserState(['navigation' => $arguments]);
        $this->answerCallbackQuery([
            'text' => 'Navigating...',
        ]);
        $this->triggerCommand('route');
    }
}
