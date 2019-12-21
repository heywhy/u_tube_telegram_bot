<?php

namespace App\Support;

use App\Support\App;
use Illuminate\Contracts\Cache\Repository;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;

trait CommandHelperTrait
{

    /**
     * Return the data attached to the chat.
     *
     * @return array
     */
    protected function getUserData(): array
    {
        return $this->getStorage()->get($this->getUserKey()) ?? [];
    }

    /**
     * Get a unique key attached to the chat.
     *
     * @return string
     */
    protected function getUserKey(): string
    {
        return App::getUserKey($this->getUpdate());
    }

    /**
     * Set the state by overriding existing data.
     *
     * @param array|null $state
     * @return void
     */
    protected function setUserState(?array $state)
    {
        $key = $this->getUserKey();
        $storage = $this->getStorage();
        if ($state == null) {
            return $storage->delete($key);
        }
        return $storage->set($key, $state);
    }

    /**
     * Add state by adding new state with existing state.
     *
     * @param array $data
     * @return void
     */
    protected function addUserState(array $data)
    {
        $current = $this->getUserData() ?? [];

        return $this->setUserState(array_merge($current, $data));
    }

    /**
     * Get the storage where chat is being saved.
     *
     * @return Repository
     */
    protected function getStorage(): Repository
    {
        return app(Repository::class);
    }

    /**
     * Answer the callback query if one exists.
     *
     * @param array $params
     * @param Update|null $update
     * @return void
     */
    protected function answerCallbackQuery(array $params, Update $update = null)
    {
        $callbackQuery = $this->getCallbackQuery($update);
        if (is_null($callbackQuery)) {
            return;
        }
        $params = array_merge(['callback_query_id' => $callbackQuery->getId(),], $params);
        $this->getTelegram()->answerCallbackQuery($params);
    }

    /**
     * Get the callback query from the given update.
     *
     * @param Update|null $update
     * @return CallbackQuery|null
     */
    protected function getCallbackQuery(Update $update = null): ?CallbackQuery
    {
        $update = $update ?? $this->getUpdate();
        return $update->getCallbackQuery();
    }
}
