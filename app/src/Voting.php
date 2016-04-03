<?php
namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Voting
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getResults()
    {
        return [
            'votes' => $this->getMapResults(),
            'sum' => $this->getSumVotes()
        ];
    }

    public function hasUserVoted($steamid, $gametype)
    {
        $query = $this->db->prepare('SELECT count(id) FROM vote_users WHERE steamid = :steamid AND gametype = :gametype');
        $query->bindParam(':steamid', $steamid, \PDO::PARAM_STR);
        $query->bindParam(':gametype', $gametype, \PDO::PARAM_STR);

        $query->execute();

        return $query->fetchColumn();
    }

    public function vote($steamid, $gametype, $maps)
    {
        $query = $this->db->prepare(<<<SQL
INSERT INTO vote_users (
    steamid,
    gametype,
    created_at
) VALUES (
    :steamid,
    :gametype,
    CURRENT_TIMESTAMP 
)
SQL
);
        $query->bindParam(':steamid', $steamid, \PDO::PARAM_STR);
        $query->bindParam(':gametype', $gametype, \PDO::PARAM_STR);
        //$query->bindParam(':datetime', new \DateTime(), Type::DATETIME);
        $query->execute();

        $userId = $this->db->lastInsertId();

        $voteQuery = $this->db->prepare('INSERT INTO votes (user, map) VALUES (:user, :map)');
        $voteQuery->bindParam(':user', $userId);

        $updateScoreQuery = $this->db->prepare('UPDATE maps SET score = score+1 WHERE id = :id');

        foreach ($maps as $pool) {
            foreach ($pool as $map) {
                $voteQuery->bindParam(':map', $map, \PDO::PARAM_INT);
                $voteQuery->execute();

                $updateScoreQuery->bindParam(':id', $map, \PDO::PARAM_INT);
                $updateScoreQuery->execute();
            }
        }
    }

    private function getSumVotes()
    {
        $sumQuery = $this->db->query('SELECT gametype,pool,sum(score) as score FROM maps GROUP BY gametype, pool');
        $sum = [];
        foreach ($sumQuery->fetchAll() as $sumValue) {
            if (!array_key_exists($sumValue['gametype'], $sum)) {
                $sum[$sumValue['gametype']] = [];
            }

            $sum[$sumValue['gametype']][$sumValue['pool']] = ($sumValue['score'] ?: 1);
        }

        return $sum;
    }

    private function getMapResults()
    {
        $resultsQuery = $this->db->query(<<<SQL
SELECT
    id,
    map,
    gametype,
    pool,
    score
FROM maps
ORDER BY
    gametype ASC,
    pool ASC,
    score DESC,
    map ASC
SQL
        );

        $results = [];
        foreach ($resultsQuery->fetchAll() as $map) {
            if (!array_key_exists($map['gametype'], $results)) {
                $results[$map['gametype']] = [];
            }

            if (!array_key_exists($map['pool'], $results[$map['gametype']])) {
                $results[$map['gametype']][$map['pool']] = [];
            }

            $results[$map['gametype']][$map['pool']][] = $map;
        }

        return $results;
    }
}
