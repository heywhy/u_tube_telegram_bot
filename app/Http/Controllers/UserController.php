<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     *
     * @param  Request  $request
     */
    public function me(Request $request)
    {
        return $request->user();
    }
}
