<?php

namespace interactivesolutions\honeycombresources\app\providers;

use interactivesolutions\honeycombcore\providers\HCBaseServiceProvider;
use interactivesolutions\honeycombresources\app\console\commands\HCGenerateThumbs;
use Intervention\Image\ImageServiceProvider;

class HCResourcesServiceProvider extends HCBaseServiceProvider
{
    protected $homeDirectory = __DIR__;
    
    protected $commands = [
        HCGenerateThumbs::class
    ];

    protected $namespace = 'interactivesolutions\honeycombresources\app\http\controllers';

    /**
     * Registering external providers
     */
    protected function registerProviders ()
    {
        $this->app->register (ImageServiceProvider::class);
    }
}


