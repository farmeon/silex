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



$app = new Application();

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
        'host' => '192.168.48.2',
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

    $mr = new \Service\ManagerRegistry(
        null, array(), array('em'), null, null, '\\Doctrine\\ORM\\Proxy\\Proxy'
    );
    $mr->setContainer($app);
    $extensions[] = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension($mr);

    return $extensions;
});

$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['en'],
]);

$app->register(new FormServiceProvider());

$app->register(new ValidatorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

return $app;
