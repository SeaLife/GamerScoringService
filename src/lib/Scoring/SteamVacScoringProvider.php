<?php

namespace Scoring;

use App\Entity\ScoreEntry;
use GuzzleHttp\Exception\ClientException;
use Zyberspace\SteamWebApi\Client;
use Zyberspace\SteamWebApi\Interfaces\ISteamUser;

class SteamVacScoringProvider implements ScoreProvider {

    public function isCompatible ($playerUID) {
        return TRUE;
    }

    public function fetchScoringData ($playerUID) {
        $steamClient = new Client(envvar("STEAM_KEY", ''));
        $steamUser   = new ISteamUser($steamClient);

        try {
            $response = $steamUser->GetPlayerBansV1($playerUID);

            $arr = json_decode(json_encode($response), TRUE);

            return $arr["players"][0];
        } catch (ClientException $e) {
            return null;
        }
    }

    public function processScoringData (ScoreEntry $entry) {
        $result = array();
        $data   = json_decode($entry->getScoreData(), TRUE);

        if ($data['CommunityBanned']) {
            array_push($result, ScoringResult::of('Steam::CommunityBan', 100, 'Steam', 'Steam'));
        }
        if ($data['VACBanned']) {
            array_push($result, ScoringResult::of('Steam::VAC', 1000, 'Steam', 'Steam'));
        }
        if ($data['NumberOfVACBans'] > 0) {
            array_push($result, ScoringResult::of('Steam::NumberVACBans::' . $data['NumberOfVACBans'], 100 * $data['NumberOfVACBans'], 'Steam', 'Steam'));
        }

        return $result;
    }
}