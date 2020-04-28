<?php

namespace Rafter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class RafterCommandRunController extends Controller
{
    public function __invoke(Request $request)
    {
        Artisan::call($request->command);
        return Artisan::output();
    }
}
