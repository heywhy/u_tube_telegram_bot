<?php

namespace App\Telegram;

use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;

class AnswerQueryCallbackBus extends Command
{

    /**
     * @inheritDoc
     */
    public function make(Api $telegram, $arguments, Update $update)
    {
        $this->telegram = $telegram;
        $this->arguments = $arguments;
        $this->update = $update;

        return $this->handle($update->getCallbackQuery(), $arguments);
    }


    /**
     * @param  \Telegram\Bot\Objects\CallbackQuery  $query
     * @param  mixed  Mostly strings
     */
    public function handle(CallbackQuery $query, $arguments)
    {
        $this->telegram->answerCallbackQuery([
            'callback_query_id' => $query->getId(),
            'text' => 'downloading...'
        ]);
    }


    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 9);
        if ($action === 'replyWith') {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }
    }
}
