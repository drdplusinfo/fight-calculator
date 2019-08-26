<?php declare(strict_types=1);

namespace DrdPlus\Lighting;

use DrdPlus\RollsOn\Traps\RollOnSenses;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 128 left column, @link https://pph.drdplus.jaroslavtyc.com/#oslneni
 */
class Glare extends StrictObject
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
     * @param Contrast $contrast
     * @param RollOnSenses $rollOnSenses
     * @param bool $wasPrepared Note: to be prepared for contrast from light-to-dark, you need ten more time for
     *     preparation
     */
    public function __construct(Contrast $contrast, RollOnSenses $rollOnSenses, $wasPrepared)
    {
        if ($contrast->getValue() <= $rollOnSenses->getValue()) {
            $possibleMalus = -($contrast->getValue() - 1);
        } else {
            $possibleMalus = -($contrast->getValue() - 7);
        }
        // if you are expecting the shine, you have twice a chance to avoid it
        if ($wasPrepared) {
            $possibleMalus += 6;
        }
        $this->malus = 0;
        if ($possibleMalus < 0) {
            $this->malus = $possibleMalus;
        }
        $this->shined = $contrast->isFromDarkToLight(); // otherwise blinded
    }

    /**
     * Gives malus to activities requiring sight.
     *
     * @return int
     */
    public function getMalus(): int
    {
        return $this->malus;
    }

    public function isShined(): bool
    {
        return $this->shined && $this->getMalus() !== 0;
    }

    public function isBlinded(): bool
    {
        return !$this->isShined() && $this->getMalus() !== 0;
    }
}