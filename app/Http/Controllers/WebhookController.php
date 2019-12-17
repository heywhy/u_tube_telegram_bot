<?php

namespace App\Http\Controllers;

use App\Support\App;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function __invoke()
    {
        return App::process();
    }
}
