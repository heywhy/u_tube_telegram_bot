<?php

namespace App\Telegram\Actions;

use Exception;
use Telegram\Bot\Commands\Command;

/**
 * Class Action.
 *
 *
 * @method mixed replyWithMessage($use_sendMessage_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 * @method mixed replyWithPhoto($use_sendPhoto_parameters)           Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($use_sendAudio_parameters)           Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($use_sendVideo_parameters)           Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($use_sendVoice_parameters)           Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($use_sendDocument_parameters)     Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($use_sendSticker_parameters)       Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($use_sendLocation_parameters)     Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($use_sendChatAction_parameters) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 */
abstract class Action
{

    /**
     * @var \Telegram\Bot\Commands\Command
     */
    protected $commander;


    /**
     *
     */
    function setCommander(Command $commander)
    {
        $this->commander = $commander;
    }

    /**
     *
     */
    function getCommander(): Command
    {
        return $this->commander;
    }

    /**
     *
     */
    function __call($name, $arguments)
    {
        if ($this->commander == null) {
            throw new Exception('Commander not found');
        }
        return $this->commander->{$name}(...$arguments);
    }

    /**
     *
     */
    abstract function call($argument);
}
