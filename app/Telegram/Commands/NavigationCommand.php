<?php

namespace App\Telegram\Commands;

use App\Enums\NavigationActions;
use App\Support\CommandHelperTrait;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Objects\Message;

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
        $message = $this->getMessage();

        if (NavigationActions::getInstance($arguments)->is(NavigationActions::Cancel())) {
            $this->setUserState(null);

            return $this->telegram->editMessageText([
                'text' => 'Search cancelled',
                'message_id' => !is_null($message) ? $message->getMessageId() : null,
                'chat_id' => !is_null($message) ? $message->getChat()->getId() : null,
            ]);
        }

        $this->addUserState(['navigation' => $arguments,]);
        $this->answerCallbackQuery([
            'text' => 'Navigating...',
        ]);
        $this->triggerCommand('route');
        $this->deleteMessage($message);
    }

    /**
     * Return the message attached to the update.
     *
     * @return Message|null
     */
    protected function getMessage(): ?Message
    {
        return !is_null($callbackQuery = $this->getCallbackQuery($this->getUpdate()))
            ? $callbackQuery->getMessage() : $this->getUpdate()->getMessage();
    }

    /**
     * Delete the navigation message.
     *
     * @param Message|null $message
     * @return void
     */
    protected function deleteMessage(?Message $message)
    {
        try {
            $params = [
                'message_id' => !is_null($message) ? $message->getMessageId() : null,
                'chat_id' => !is_null($message) ? $message->getChat()->getId() : null,
            ];

            $this->telegram->deleteMessage($params);
        } catch (TelegramOtherException|TelegramResponseException $e) {
            logger()->error($e);
        }
    }
}
