<?php

namespace App\Telegram\Commands;

use App\Enums\NavigationActions;
use App\Enums\YoutubeSearchStates;
use App\Support\CommandHelperTrait;
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
                    $this->setUserState(null);
                    $this->replyWithMessage([
                        'text' => 'Search cancelled',
                        'reply_markup' => Keyboard::make(['remove_keyboard' => true,]),
                    ]);
                    break;
                }

                $token = $arguments === NavigationActions::Next
                    ? $data['nextToken'] : $data['previousToken'];

                $this->setUserState(array_merge($data, ['pageToken' => $token]));
                $this->triggerCommand('youtube_search', $data['query']);
                break;
        }
        dump(['on routing => ', $arguments, $data]);
    }
}
