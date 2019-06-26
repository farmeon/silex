<?php

namespace Loader;

use Silex\Application;
use Service\AuthorsService;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        $this->app['authors.service'] = function() {
            return new AuthorsService($this->app["db"]);
        };
    }
}