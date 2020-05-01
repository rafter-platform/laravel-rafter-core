<?php

namespace Rafter\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Rafter\StreamingOutput;

class RafterCommandRunController extends Controller
{
    public function __invoke()
    {
        try {
            return response()->stream(function () {
                Artisan::call(request()->command, [], new StreamingOutput);
            }, 200, [
                // Disable output buffering inside Nginx
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
