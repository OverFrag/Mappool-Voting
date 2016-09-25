<?php
if (php_sapi_name() !== 'cli') {
    echo 'Run this script with CLI';
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../app/config/container.php';

$app['db']->exec(<<<SQL
DROP TABLE IF EXISTS votes;
CREATE TABLE votes (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "user" INTEGER,
    "map" INTEGER
)
SQL
);

$app['db']->exec(<<<SQL
DROP TABLE IF EXISTS vote_users
CREATE TABLE vote_users (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "steamid" TEXT,
    "gametype" TEXT,
    "created_at" DATETIME
)
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
)
SQL
);

