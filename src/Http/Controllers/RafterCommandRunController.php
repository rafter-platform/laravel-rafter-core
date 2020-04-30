<?php

namespace Rafter\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class RafterCommandRunController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            Artisan::call($request->command);
            return Artisan::output();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
