<?php

namespace App\Telegram\Commands;

use App\Support\CommandHelperTrait;
use App\Support\CommandRoute;
use Exception;
use Telegram\Bot\Commands\Command;

class RouteCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'route';

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

    /**
     * Process the string command.
     *
     * @param  mixed  $arguments
     * @param  mixed  $data
     */
    protected function process($arguments, $data)
    {
        try {
            $callback = CommandRoute::fromRoute($data);
            if ($callback != null) $callback($this, $arguments);
        } catch (Exception $e) {
            logger()->error($e);
            $this->replyWithMessage(['text' => 'Unknown command']);
        }
    }
}
