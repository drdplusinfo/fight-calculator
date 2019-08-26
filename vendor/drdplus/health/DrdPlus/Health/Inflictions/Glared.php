<?php declare(strict_types=1);

namespace DrdPlus\Health\Inflictions;

use DrdPlus\Health\Health;
use DrdPlus\Lighting\Glare;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Calculations\SumAndRound;
use Granam\Strict\Object\StrictObject;

/**
 */
class Glared extends StrictObject
{
    /**
     * @var int
     */
    private $malus;
    /**
     * @var bool
     */
    private $shined;
    /**
     * @var int
     */
    private $gettingUsedToForRounds;
    /**
     * @var Health
     */
    private $health;

    public static function createWithoutGlare(Health $health): Glared
    {
        return new static(0, false, $health);
    }

    public static function createFromGlare(Glare $glare, Health $health): Glared
    {
        return new static($glare->getMalus(), $glare->isShined(), $health);
    }

    private function __construct(int $malus, bool $isShined, Health $health)
    {
        $this->malus = $malus;
        $this->shined = $isShined;
        $this->gettingUsedToForRounds = 0;
        $this->health = $health;
    }

    /**
     * Gives malus to activities requiring sight, already lowered by a time getting used to the glare, if any.
     *
     * @return int negative integer or zero
     */
    public function getCurrentMalus(): int
    {
        if ($this->getGettingUsedToForRounds() === 0) {
            return $this->malus;
        }
        if ($this->isShined()) {
            // each rounds of getting used to lowers malus by one point
            return $this->malus + $this->getGettingUsedToForRounds();
        }
        // ten rounds of getting used to are needed to lower glare malus by a single point
        return $this->malus + SumAndRound::floor($this->getGettingUsedToForRounds() / 10);
    }

    public function isShined(): bool
    {
        return $this->shined;
    }

    public function isBlinded(): bool
    {
        return !$this->isShined();
    }

    public function getHealth(): Health
    {
        return $this->health;
    }

    /**
     * Total rounds of getting used to current contrast, which lowers glare and malus.
     *
     * @param Time $gettingUsedToFor
     */
    public function setGettingUsedToForTime(Time $gettingUsedToFor)
    {
        $inRounds = $gettingUsedToFor->findRounds();
        if ($inRounds === null) {
            // it can not be expressed by rounds, so definitely get used to it - malus zeroed
            if ($this->isShined()) {
                $this->gettingUsedToForRounds = -$this->malus;
            } else { // if blinded than needs ten more time to get used to it
                $this->gettingUsedToForRounds = -$this->malus * 10;
            }

            return;
        }
        if ($this->isShined()) {
            if ($this->malus + $inRounds->getValue() > 0) { // more time than needed, malus is gone
                $this->gettingUsedToForRounds = -$this->malus; // zeroed malus in fact
            } else {
                $this->gettingUsedToForRounds = $inRounds->getValue(); // not enough to remove whole glare and malus

            }
        } else { // if blinded than needs ten more time to get used to it
            if ($this->malus + $inRounds->getValue() / 10 > 0) { // more time than needed, malus is gone
                $this->gettingUsedToForRounds = -$this->malus * 10; // zeroed malus in fact
            } else {
                $this->gettingUsedToForRounds = $inRounds->getValue(); // not enough to remove whole glare and malus
            }
        }
    }

    /**
     * Gives number of rounds when getting used to current contrast.
     *
     * @return int
     */
    public function getGettingUsedToForRounds(): int
    {
        return $this->gettingUsedToForRounds;
    }
}