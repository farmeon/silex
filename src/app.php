<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Service\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\MonologServiceProvider;
use Loader\RoutesLoader;
use Loader\ServicesLoader;

$app = new Application();

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

$app->register(new DoctrineServiceProvider, [
    'db.options' => [
        'driver' => 'pdo_pgsql',
        'host' => '192.168.48.3',
        'dbname' => 'db_silex',
        'user' => 'silex',
        'password' => 'silex',
        'port' => '5432'
    ],
]);

$app->register(new DoctrineORMServiceProvider(), array(
    'db.orm.class_path'            => __DIR__.'/../vendor/doctrine/orm/lib',
    'db.orm.proxies_dir'           => __DIR__.'/../var/cache/doctrine/Proxy',
    'db.orm.proxies_namespace'     => 'DoctrineProxy',
    'db.orm.auto_generate_proxies' => true,
    'orm.em.options' => [
        'mappings' => [
            [
                'type' => 'annotation',
                'use_simple_annotation_reader' => false,
                'namespace' => 'Entity',
                'path' => __DIR__.'/Entity',
            ],
        ],
    ],
));

$app->register(new LocaleServiceProvider());

$app->register(new Silex\Provider\FormServiceProvider(), []);
$app->extend('form.extensions', function($extensions, $app) {
    if (isset($app['form.doctrine.bridge.included'])) return $extensions;
    $app['form.doctrine.bridge.included'] = 1;

    $mr = new ManagerRegistry(
        null, array(), array('em'), null, null, '\Doctrine\ORM\Proxy\Proxy'
    );
    $mr->setContainer($app);
    $extensions[] = new DoctrineOrmExtension($mr);

    return $extensions;
});

$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['en'],
]);

$app->register(new FormServiceProvider());

$app->register(new ValidatorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$servicesLoader = new ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

$routesLoader = new RoutesLoader($app);
$routesLoader->bindRoutesToControllers();



return $app;
