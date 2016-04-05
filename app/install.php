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
            'Japanese Castles',
            'Shining Forces',
            'Iron Works',
            'Spider Crossings',
            'Siberia',
            'Troubled Waters',
            'Courtyard',
            'Infinity',
            'Futurecrossings'
        ],
        1 => [
            'q3wcp10',
            'Push',
            'Bloodlust',
            'mapel4b',
            'Q3wcp22',
            'ctf_voy4',
            'ctf_tkc',
            'basesiege',
            'falloutbunker',
            'rebound',
            'ctf_mapel4',
            'mkbase'
        ]
    ],
    'ift' => [
        0 => [
            'Campgrounds',
            'Retribution',
            'The Edge',
            'Deep Inside',
            'Almost Lost',
            'Lost World',
            'Overkill',
            'Hidden Fortress',
            'Intervention'
        ],
        1 => [
            'tscabdm2',
            'psidm7',
            'fr3dm1',
            'Forgotten',
            'cpm2',
            'cpm14',
            'jaxdm8',
            'Black Cathedral',
            'Chemical Reaction',
            'Grim Dungeons',
            'Cold War'
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
