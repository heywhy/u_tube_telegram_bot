<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class KeyboardCommand extends Command
{
    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'keyboard';

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

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $keyboard = [
            ['7', '8', '9'],
            ['4', '5', '6'],
            ['1', '2', '3'],
                 ['0']
        ];

        $reply_markup = $this->telegram->replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $this->replyWithMessage([
            'text' => 'Hello World',
            'reply_markup' => $reply_markup
        ]);
    }
}
