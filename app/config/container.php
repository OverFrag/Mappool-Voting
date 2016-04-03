<?php
$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../db.sqlite3'
    ]
]);

$app->register(new \Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views'
]);

$app['hybrid_auth'] = function() {
    return new Hybrid_Auth(__DIR__ . '/hybridauth.php');
};

$app['steam'] = function() use ($app) {
    return $app['hybrid_auth']->getAdapter('Steam');
};

$app['voting'] = function() use ($app) {
    return new \App\Voting($app['db']);
};

$app['vote_parser'] = function() use ($app) {
    /** @var Symfony\Component\HttpFoundation\Request $request */
    $votes = $app['request']->request->get('map');

    foreach ($votes as $gametype => $pool) {

    }
};
return $app;
