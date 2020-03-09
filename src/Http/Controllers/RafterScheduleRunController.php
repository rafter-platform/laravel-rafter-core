<?php

namespace Rafter\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class RafterScheduleRunController extends Controller
{
    public function __invoke()
    {
        Artisan::call('schedule:run');
        return Artisan::output();
    }
}
