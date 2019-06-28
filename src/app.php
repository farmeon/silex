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
use Radebatz\Silex\LdapAuth\LdapAuthenticationServiceProvider;
use Silex\Provider\SecurityServiceProvider;

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
        'host' => '192.168.96.2',
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

// register service with name LDAP-FORM
$app->register(new LdapAuthenticationServiceProvider('LDAP-FORM'), array(
    'security.ldap.LDAP-FORM.options' => array(
        'auth' => array(
            'entryPoint' => 'form',
        ),
        'ldap' => array(
            'host' => 'localhost',
            'username' => 'admin',
            'password' => 'admin',
        ),
    )
));

// configure firewalls
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
        ),
        'default' => array(
            'pattern' => '^.*$',
            'anonymous' => true,
            'LDAP-FORM' => array(
                // form options
                'check_path' => '/login_check_ldap',
                'require_previous_session' => false,
            ),
            'users' => function () use ($app) {
                // use the pre-configured Ldap user provider
                return $app['security.ldap.LDAP-FORM.user_provider'](array(
                    // configure LDAP attribute to use for auth bind call (dn is the default)
                    'authName' => 'dn',
                    'attr' => array(
                        // LDAP attribute => user property
                        // these require setter support in the user class
                        'sn' => 'lastName',
                    ),
                    'roles' => array(
                        'CN=Development,OU=Groups,DC=radebatz,DC=net'   => 'ROLE_USER',
                        'CN=Admins,OU=Groups,DC=radebatz,DC=net'        => 'ROLE_ADMIN',
                    ),
                    'baseDn' => 'DC=radebatz,DC=net',
                ));
            },
        ),
    )
));

$app->register(new JDesrosiers\Silex\Provider\JmsSerializerServiceProvider(), array(
    "serializer.srcDir" => __DIR__ . "/vendor/jms/serializer/src",
));


return $app;
