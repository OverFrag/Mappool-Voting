<?php
if (php_sapi_name() !== 'cli') {
    echo 'Run this script with CLI';
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../app/config/container.php';

$maps = [
    'ift' => [
        'psidm7',
        'thedreadfulplace',
        'almostlost',
        'deepinside',
        'overkill',
        'chemicalreaction',
        'coldwar',
        'jaxdm8',
        'tscabdm2',
        'intervention',
        'theedge',
        'cpm14 / last fortress / q3fraggel2a',
        'forgotten (q3dm5)',
        'bal3dm3',
        'hearth (q3dm3)',
        'place of many deaths (q3dm4)',
        'spillway (ex house of pain / q3dm2)'
    ],
    'ictf' => [
        'q3wcp10 / crudecrossings',
        'mkbase',
        'siberia',
        'push',
        'infinity',
        'pro_rings',
        'ctf_voy4',
        'courtyard',
        'basesiege',
        'troubledwaters',
        'falloutbunker',
        'futurecrossings',
        'pillbox',
        'rebound',
        'reflux',
        'stronghold',
        'vampirecrossings',
        'q3wcp12 (Mostly Harmless by Hal9000)',
        'q3wc8 (Ultimatium by DD)',
    ]
];


foreach ($maps as $gt => $list) {
    foreach ($list as $i => $map) {
        $app['db']->insert('maps', [
            'map' => $map,
            'gametype' => $gt,
            'score' => 0,
            'pool' => 0
        ]);
    }
}
