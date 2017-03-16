<?php

namespace interactivesolutions\honeycombresources\app\providers;

use Illuminate\Support\ServiceProvider;
//use interactivesolutions\honeycombscripts\app\console\commands\HCGenerateThumbs;
use interactivesolutions\honeycombresources\app\console\commands\HCGenerateThumbs;
use Intervention\Image\ImageServiceProvider;

class HCResourcesServiceProvider extends ServiceProvider
{
    /**
     * Register commands
     *
     * @var array
     */
    protected $commands = [
        HCGenerateThumbs::class
    ];

    protected $namespace = 'interactivesolutions\honeycombresources\app\http\controllers';

    /**
     * Bootstrap the application services.
     */
    public function boot ()
    {
        // register artisan commands
        $this->commands ($this->commands);

        // loading views
        $this->loadViewsFrom (__DIR__ . '/../../resources/views', 'HCResources');

        // loading translations
        $this->loadTranslationsFrom (__DIR__ . '/../../resources/lang', 'HCResources');

        // registering elements to publish
        $this->registerPublishElements ();

        // registering helpers
        $this->registerHelpers ();

        // registering routes
        $this->registerRoutes ();

        $this->registerProviders();
    }

    /**
     * Register helper function
     */
    private function registerHelpers ()
    {
        $filePath = __DIR__ . '/../http/helpers.php';

        if (\File::isFile ($filePath))
            require_once $filePath;
    }

    /**
     *  Registering all vendor items which needs to be published
     */
    private function registerPublishElements ()
    {
        $directory = __DIR__ . '/../../database/migrations/';

        // Publish your migrations
        if (file_exists ($directory))
            $this->publishes ([
                __DIR__ . '/../../database/migrations/' => database_path ('/migrations'),
            ], 'migrations');

        $directory = __DIR__ . '/../public';

        // Publishing assets
        if (file_exists ($directory))
            $this->publishes ([
                __DIR__ . '/../public' => public_path ('honeycomb'),
            ], 'public');
    }

    /**
     * Registering routes
     */
    private function registerRoutes ()
    {
        $filePath = __DIR__ . '/../../app/honeycomb/routes.php';

        if (file_exists($filePath))
            \Route::group (['namespace' => $this->namespace], function ($router) use ($filePath) {
                require $filePath;
            });
    }

    /**
     * Registering external providers
     */
    private function registerProviders ()
    {
        $this->app->register(ImageServiceProvider::class);
    }
}


