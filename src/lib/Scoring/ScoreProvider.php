<?php

namespace Scoring;

use App\Entity\ScoreEntry;

interface ScoreProvider {

    public function isCompatible ($playerUID);

    public function fetchScoringData ($playerUID);

    /**
     * @param ScoreEntry $entry
     * @return ScoringResult[]
     */
    public function processScoringData (ScoreEntry $entry);
}