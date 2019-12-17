<?php

namespace App\Support;

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
            array_map(fn($data) => static::routeUpdate($data), $update);
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
        $entities = $update->getMessage() != null
            ? $update->getMessage()->getEntities() : null;

        if ($entities == null || $entities->contains('type', '!=', 'bot_command')) {
            /** @var App\Telegram\Commands\RouteCommand */
            $router = app(RouteCommand::class);
            $arguments = $update->getMessage()->getText();

            $router->make(app(Api::class), $arguments, $update);
        }
    }

    /**
     *
     * @param  \Telegram\Bot\Objects\Update  $update
     */
    static function getUserKey(Update $update): string
    {
        return 'user_' . $update->getMessage()->getFrom()->getId();
    }
}
