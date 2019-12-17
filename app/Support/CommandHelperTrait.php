<?php

namespace App\Support;

use App\Support\App;

trait CommandHelperTrait
{

    protected function getUserData()
    {
        return $this->cacheRepo->get($this->getUserKey());
    }

    protected function getUserKey(): string
    {
        return App::getUserKey($this->getUpdate());
    }
}
