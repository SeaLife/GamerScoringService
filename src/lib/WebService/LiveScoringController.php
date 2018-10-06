<?php

namespace WebService;

use App\Entity\ScoreEntry;
use Globals\Annotations\WebResponder;
use Globals\Scoring;
use Scoring\SteamVacScoringProvider;
use Symfony\Component\Finder\Finder;

class LiveScoringController {

    /**
     * @WebResponder(path="/api/scoring/live/{steamId}", method="GET", produces="application/json")
     * @param $vars
     */
    public function demoSteam ($vars) {
        $steamId   = $vars["steamId"];
        $providers = Scoring::getInstance()->getProviders();
        $maxScore  = 10000;
        $score     = $maxScore;

        $entries = array();

        foreach ($providers as $provider) {
            $data = $provider->fetchScoringData($steamId);

            if ($data != NULL) {
                continue;
            }

            $entry = new ScoreEntry();
            $entry->setScoreData(json_encode($data));

            $result = $provider->processScoringData($entry);

            foreach ($result as $item) {
                $score -= $item->getScoreValue();
            }

            $entries = array_merge($entries, $result);
        }

        echo json_encode(array("playerId" => $steamId, "score" => $score, "maxScore" => $maxScore, "trusty" => round($score / $maxScore * 100, 2), "entries" => $entries), JSON_PRETTY_PRINT);
    }
}