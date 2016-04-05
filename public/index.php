<?php
date_default_timezone_set('Europe/Berlin');

require_once __DIR__ . '/../vendor/autoload.php';
use \Symfony\Component\HttpFoundation\JsonResponse;

/** @var \Silex\Application $app */
$app = require_once __DIR__ . '/../app/config/container.php';
$app['debug'] = true;

$app->get('/login', function() use ($app) {
    if ($app['request']->get('hauth_start') || $app['request']->get('hauth_done')) {
        Hybrid_Endpoint::process();
    }

    if (!$app['steam']->isUserConnected()) {
        $app['steam']->login();
    } else {
        return $app->redirect('/');
    }
});

$app->get('/logout', function() use ($app) {
    $app['steam']->logout();
    return $app->redirect('/');
});

$app->get('/', function() use ($app) {
    $results = $app['voting']->getResults();
    $loggedIn = $app['steam']->isUserConnected();

    return $app['twig']->render('index.twig', [
        'results' => $results,
        'loggedIn' => $loggedIn
    ]);
});

$app->post('/vote', function() use ($app) {
    $data = $app['request']->request->get('data');
    $data = json_decode($data, true);

    if (!array_key_exists('gametype', $data) || !array_key_exists('maps', $data) || !is_array($data['maps'])) {
        return new JsonResponse(['error' => 'Invalid data']);
    }

    if (!$app['steam']->isUserConnected()) {
        return new JsonResponse(['error' => 'Not logged in!']);
    }

    $gametypes = ['ift', 'ictf', 'special'];
    if (!in_array($data['gametype'], $gametypes)) {
        return new JsonResponse(['error' => 'Unknown gametype']);
    }

    if ($data['gametype'] === 'special') {
        if (count($data['maps']) !== 3 || !isset($data['maps'][0], $data['maps'][1], $data['maps'][2])) {
            return new JsonResponse(['error' =>'You have to vote on all 3 maps']);
        }
    } else {
        if (count($data['maps']) !== 2 || !isset($data['maps'][0], $data['maps'][1])) {
            return new JsonResponse(['error' =>'You have to vote on both pools']);
        }

        if (count($data['maps'][0]) !== 4 || count($data['maps'][1]) !== 3) {
            return new JsonResponse(['error' =>'You have to vote on 4 old maps and 3 new']);
        }
    }

    $voted = (int) $app['voting']->hasUserVoted(
        $app['steam']->getUserProfile()->identifier,
        $data['gametype']
    );

    if ($voted !== 0) {
        return new JsonResponse(['error' => 'You already voted on this gametype']);
    }

    try {
        $app['voting']->vote(
            $app['steam']->getUserProfile()->identifier,
            $data['gametype'],
            $data['maps']
        );

        return new JsonResponse(['message' => 'OK']);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Something went wrong. Try again later.']);
    }
});

$app->run();

