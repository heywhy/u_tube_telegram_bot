<?php

namespace App\Support;

use Exception;
use Illuminate\Support\Arr;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;

class CommandRoute
{
    protected static $routes;

    static function fromRoute(array $param): callable
    {
        static::initRoutes();

        $route = $param['route'];
        if (!Arr::has(static::$routes, [$route])) {
            throw new Exception('Can\'t find route key "' . $route . '" in telegram routes');
        }
        $builder = static::$routes[$route];

        return function (Command $commander, $arguments) use (&$builder) {
            // This will update the chat status to typing...
            $commander->replyWithChatAction(['action' => Actions::TYPING]);

            if (is_callable($builder))
                return call_user_func_array($builder, [$commander, $arguments]);

            /**
             * @var \App\Telegram\Actions\Action
             */
            $action = app($builder);

            $action->setCommander($commander);

            return $action->call($arguments);
        };
    }

    /**
     *
     */
    protected static function initRoutes()
    {
        if (is_null(static::$routes)) {
            static::$routes = include_once base_path('routes/telegram.php');
        }
    }
}
