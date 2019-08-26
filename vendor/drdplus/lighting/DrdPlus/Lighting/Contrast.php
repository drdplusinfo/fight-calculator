<?php declare(strict_types=1);

namespace DrdPlus\Lighting;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;

/**
 * see PPH page 128 left column, @link https://pph.drdplus.jaroslavtyc.com/#oslneni
 */
class Contrast extends StrictObject implements PositiveInteger
{
    /**
     * @var int
     */
    private $value;
    /**
     * @var bool
     */
    private $fromLightToDark;

    public static function createBySimplifiedRules(
        LightingQuality $previousLightingQuality,
        LightingQuality $currentLightingQuality
    ): Contrast
    {
        $difference = $previousLightingQuality->getValue() - $currentLightingQuality->getValue();

        return new static($difference, 10);
    }

    public static function createByExtendedRules(
        EyesAdaptation $eyesAdaptation,
        LightingQuality $currentLightingQuality,
        RaceCode $raceCode,
        Tables $tables
    ): Contrast
    {
        $difference = $eyesAdaptation->getValue() - $currentLightingQuality->getValue();

        return new static($difference, $tables->getSightRangesTable()->getAdaptability($raceCode));
    }

    private function __construct(int $lightsDifference, int $eyeAdaptability)
    {
        $this->fromLightToDark = $lightsDifference > 0;
        $base = \abs($lightsDifference) / $eyeAdaptability;
        /** note: it differs for simplified rules rounding by floor
         * (PPH page 128 left column, @link https://pph.drdplus.jaroslavtyc.com/#oslneni)
         * but standard rounding fits to extended rules and is more generic in DrD+ so it has been unified here
         */
        $this->value = SumAndRound::round($base);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $asString = (string)$this->getValue();
        if ($this->isFromLightToDark()) {
            $asString .= ' (to dark)';
        } elseif ($this->isFromDarkToLight()) {
            $asString .= ' (to light)';
        } // else nothing if contrast is zero

        return $asString;
    }

    public function isFromLightToDark(): bool
    {
        return $this->fromLightToDark;
    }

    public function isFromDarkToLight(): bool
    {
        return !$this->isFromLightToDark() && $this->getValue() !== 0;
    }
}