<?php

namespace App\Support;

use App\Support\App;
use Illuminate\Contracts\Cache\Repository;

trait CommandHelperTrait
{

    protected function getUserData()
    {
        return $this->getStorage()->get($this->getUserKey());
    }

    protected function getUserKey(): string
    {
        return App::getUserKey($this->getUpdate());
    }

    protected function setUserState(?array $state)
    {
        $key = $this->getUserKey();
        $storage = $this->getStorage();
        if ($state == null) {
            return $storage->delete($key);
        }
        return $storage->set($key, $state);
    }

    protected function getStorage(): Repository
    {
        return app(Repository::class);
    }

    protected function answerCallbackQuery(array $params)
    {
        $callbackQuery = $this->getCallbackQuery();
        $params = array_merge(['callback_query_id' => $callbackQuery->getId(),], $params);
        $this->getTelegram()->answerCallbackQuery($params);
    }

    protected function getCallbackQuery()
    {
        return $this->getUpdate()->getCallbackQuery();
    }
}
