<?php

namespace Scoring;

use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;

/**
 * Class ScoringResult
 *
 * @method string getName()
 * @method void setName(string $name)
 * @method string getScoreValue()
 * @method void setScoreValue(string $scoreValue)
 * @method string getSource()
 * @method void setSource(string $source)
 * @method string getIssuer()
 * @method void setIssuer(string $issuer)
 */
class ScoringResult implements \JsonSerializable {

    public static function of ($name, $value, $source = NULL, $issuer = NULL) {
        $res             = new ScoringResult();
        $res->name       = $name;
        $res->scoreValue = $value;
        $res->issuer     = $issuer;
        $res->source     = $source;
        return $res;
    }

    /**
     * @var string
     * @Getter()
     * @Setter()
     */
    private $name;

    /**
     * @var string
     * @Getter()
     * @Setter()
     */
    private $scoreValue;

    /**
     * @var string
     * @Getter()
     * @Setter()
     */
    private $source;

    /**
     * @var string
     * @Getter()
     * @Setter()
     */
    private $issuer;

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize () {
        return array(
            'name'   => $this->getName(),
            'score'  => $this->getScoreValue(),
            'source' => $this->getSource(),
            'issuer' => $this->getIssuer()
        );
    }
}