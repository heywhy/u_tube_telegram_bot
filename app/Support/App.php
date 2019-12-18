<?php

namespace App\Support;

use App\Telegram\AnswerQueryCallbackBus;
use App\Telegram\Commands\RouteCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class App
{

    /**
     * Process the pending bot chat/commands.
     *
     * @param  bool  $process
     * @return string
     */
    static function process($process = true): string
    {
        /** @var \Telegram\Bot\Objects\Update  $update */
        $update = Telegram::commandsHandler($process);

        if (is_array($update)) {
            $mapper = function ($data) {
                return static::routeUpdate($data);
            };
            array_map($mapper, $update);
        } else {
            static::routeUpdate($update);
        }

        return 'ok';
    }

    /**
     * Perform necessary in case the message is not a command.
     *
     * @param  \Telegram\Bot\Objects\Update  $update
     */
    static protected function routeUpdate(Update $update)
    {
        /** @var Api */
        $telegram = app(Api::class);

        if (!is_null($update->getCallbackQuery())) {
            return static::processCallbackQuery($telegram, $update);
        }

        $entities = $update->getMessage() != null
            ? $update->getMessage()->getEntities() : null;

        if ($entities == null || $entities->contains('type', '!=', 'bot_command')) {
            /** @var App\Telegram\Commands\RouteCommand */
            $router = app(RouteCommand::class);
            $arguments = $update->getMessage()->getText();

            $router->make($telegram, $arguments, $update);
        }
    }

    static protected function processCallbackQuery(Api $telegram, Update $update)
    {
        $callbackQuery = $update->getCallbackQuery();
        $data = $callbackQuery->getData();

        $telegram->getCommandBus()->handler($data, $update);
    }

    /**
     *
     * @param  \Telegram\Bot\Objects\Update  $update
     */
    static function getUserKey(Update $update): string
    {
        return 'user_' . $update->getChat()->getId();
    }
}
