<?php

namespace Rafter;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Rafter\Http\Middleware\EnsureRafterWorker;
use Rafter\Http\Middleware\VerifyGoogleOidcToken;
use Rafter\Queue\RafterConnector;
use Rafter\Queue\RafterWorker;

class RafterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (! $this->runningOnRafter()) return;

        $this->ensureRoutesAreDefined();

        Queue::extend('rafter', function () {
            return new RafterConnector;
        });
    }

    public function register()
    {
        if (! $this->runningOnRafter()) return;

        $this->ensureQueueIsConfigured();
        $this->ensureCacheIsConfigured();
    }

    /**
     * Whether the current environment is running on Rafter
     *
     * @return boolean
     */
    protected function runningOnRafter()
    {
        return $_ENV['IS_RAFTER'] ?? false;
    }

    /**
     * Define internal routes for Rafter
     */
    protected function ensureRoutesAreDefined()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        // Handle queue jobs
        Route::group(['middleware' => [VerifyGoogleOidcToken::class, EnsureRafterWorker::class]], function () {
            Route::post(Rafter::QUEUE_ROUTE, 'Rafter\Http\Controllers\RafterQueueWorkerController');
            Route::post(Rafter::SCHEDULE_ROUTE, 'Rafter\Http\Controllers\RafterScheduleRunController');
        });
    }

    /**
     * Ensure Rafter queue is configured.
     */
    protected function ensureQueueIsConfigured()
    {
        Config::set('queue.connections.rafter', [
            'driver' => 'rafter',
            'queue' => $_ENV['RAFTER_QUEUE'],
            'project_id' => $_ENV['RAFTER_PROJECT_ID'],
            'region' => $_ENV['RAFTER_REGION'],
        ]);

        if ($this->app->bound(RafterWorker::class)) {
            return;
        }

        $this->app->singleton(RafterWorker::class, function () {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };

            return new RafterWorker(
                $this->app['queue'],
                $this->app['events'],
                $this->app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });
    }

    /**
     * Ensure Firestore cache is configured.
     */
    protected function ensureCacheIsConfigured()
    {
        Config::set('cache.stores.firestore', [
            'driver' => 'firestore',
            'collection' => 'cache', // Firestore collection name.
        ]);
    }
}
