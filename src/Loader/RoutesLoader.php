<?php

namespace Loader;

use Controller\ApiController;
use Silex\Application;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();
    }

    private function instantiateControllers()
    {
        $this->app['api.controller'] = function() {
            return new ApiController($this->app['authors.service']);
        };
    }
    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];
        $api->get('/authors', "api.controller:getAll");
        $api->get('/authors/{id}', "api.controller:getOne");
        $api->post('/authors', "api.controller:save");
        $api->put('/authors/{id}', "api.controller:update");
        $api->patch('/authors/{id}', "api.controller:updatePatch");
        $api->delete('/authors/{id}', "api.controller:delete");
        $this->app->mount('api/v1/', $api);
    }

}