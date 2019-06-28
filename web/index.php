<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';

/**
 * Groups Controllers for admin panel
 */
$app->mount('/admin/authors', new \Controller\AuthorsController());
$app->mount('/admin/books', new \Controller\BooksController());
$app->mount('/patch', new \Controller\PatchController());

$app->run();