<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;

/**
 * @ORM\Entity ()
 * @ORM\Table (name="player_scoring")
 * @method int getId()
 * @method void setPlayer(\App\Entity\PlayerInformation $player)
 * @method \App\Entity\PlayerInformation getPlayer()
 * @method void setScoreData(string $scoreData)
 * @method string getScoreData()
 */
class ScoreEntry {

    /**
     * @var int
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Getter()
     */
    protected $id;

    /**
     * @Setter()
     * @Getter()
     * @var PlayerInformation
     * @ORM\ManyToOne(targetEntity="PlayerInformation")
     */
    protected $player;

    /**
     * @Setter()
     * @Getter()
     * @var string
     * @ORM\Column(type="json")
     */
    protected $scoreData;
}