<?php

namespace App\Telegram\Commands;

use App\Support\CommandHelperTrait;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Exceptions\TelegramResponseException;

class PullCommand extends Command
{

    use CommandHelperTrait;

    /**
     * The name the command.
     *
     * @var string
     */
    protected $name = 'pull_video';

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
        $resolution = null;
        try {
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            $data = $this->getUserData();
            $resolutions = collect(isset($data['video_resolutions']) ? $data['video_resolutions'] : []);

            $callback = function (array $data) use ($arguments) {
                return $data['format'] == $arguments;
            };
            $resolution = $resolutions->first($callback);

            if (!is_null($resolution)) {
                $this->replyWithChatAction(['action' => Actions::UPLOAD_VIDEO]);
                $this->replyWithVideo([
                    'video' => $resolution['url'],
                    'supports_streaming' => true,
                ]);
            } else {
                $this->replyWithMessage([
                    'text' => "Can't download video",
                    'reply_markup' => $this->telegram->replyKeyboardHide(),
                ]);
            }
        } catch (TelegramOtherException|TelegramResponseException $e) {
            if ($e->getCode() == 413 && !is_null($resolution)) {
                $this->replyWithMessage([
                    'text' => <<<MSG
                    File too large, you can download at <a href="{$resolution['url']}">link</a>
                    MSG,
                    'parse_mode' => 'HTML',
                ]);
            }
        }

    }
}
