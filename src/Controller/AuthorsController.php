<?php

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


class AuthorsController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = 'testing author';

        return $app['twig']->render('authors.html.twig', [
            'data' => $data
        ]);
    }
}