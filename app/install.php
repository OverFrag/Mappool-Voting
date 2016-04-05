<?php
if (php_sapi_name() !== 'cli') {
    echo 'Run this script with CLI';
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../app/config/container.php';

$app['db']->exec(<<<SQL
DROP TABLE IF EXISTS vote_users;
CREATE TABLE vote_users (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "steamid" TEXT,
    "gametype" TEXT,
    "created_at" DATETIME
);
SQL
);

$app['db']->exec(<<<SQL
DROP TABLE IF EXISTS maps;
CREATE TABLE maps (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "map" TEXT,
    "gametype" TEXT,
    "pool" INTEGER,
    "score" INTEGER DEFAULT(0)
);
SQL
);

$app['db']->exec(<<<SQL
DROP TABLE IF EXISTS votes;
CREATE TABLE votes (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "user" INTEGER,
    "map" INTEGER
);
SQL
);

$mappools = [
    'ictf' => [
        0 => [
            'Courtyard',
            'Futurecrossings',
            'Infinity',
            'Iron Works',
            'Japanese Castles',
            'Shining Forces',
            'Siberia',
            'Spider Crossings',
            'Troubled Waters',
        ],
        1 => [
            'basesiege',
            'Bloodlust',
            'ctf_mapel4',
            'ctf_tkc',
            'ctf_voy4',
            'falloutbunker',
            'mapel4b',
            'mkbase',
            'Push',
            'q3wcp10',
            'q3wcp22',
            'rebound',
        ]
    ],
    'ift' => [
        0 => [
            'Almost Lost',
            'Campgrounds',
            'Deep Inside',
            'Hidden Fortress',
            'Intervention',
            'Lost World',
            'Overkill',
            'Retribution',
            'The Edge',
        ],
        1 => [
            'Black Cathedral',
            'Chemical Reaction',
            'Cold War',
            'cpm2',
            'cpm14',
            'Forgotten',
            'fr3dm1',
            'Grim Dungeons',
            'jaxdm8',
            'psidm7',
            'tscabdm2',
        ]
    ],
    'special' => [
        0 => [
            'Retribution',
            '7+'
        ],
        1 => [
            'Campgrounds',
            'Campgrounds Blue'
        ],
        2 => [
            'Lost World',
            'pro-q3dm13'
        ]
    ]
];

$query = $app['db']->prepare('INSERT INTO maps (map, gametype, pool) VALUES (:map, :type, :pool)');
foreach ($mappools as $type => $pools) {
    $query->bindParam(':type', $type);

    foreach ($pools as $pool => $maps) {
        $query->bindParam(':pool', $pool);

        foreach ($maps as $map) {
            $query->bindParam(':map', $map);
            $query->execute();
        }
    }
}
