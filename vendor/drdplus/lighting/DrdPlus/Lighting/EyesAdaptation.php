<?php
declare(strict_types=1);

namespace DrdPlus\Lighting;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Lighting\Partials\LightingQualityInterface;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 130 right column, @link https://pph.drdplus.jaroslavtyc.com/#rozsirujici_pravidla_pro_videni
 */
class EyesAdaptation extends StrictObject implements LightingQualityInterface
{
    /**
     * @var int
     */
    private $value;

    public function __construct(
        LightingQuality $previousLightingQuality,
        LightingQuality $currentLightingQuality,
        RaceCode $raceCode,
        Tables $tables,
        PositiveInteger $roundsOfAdaptation
    )
    {
        $this->value = $this->calculateValue(
            $previousLightingQuality,
            $currentLightingQuality,
            $raceCode,
            $tables,
            $roundsOfAdaptation
        );
    }

    /**
     * @param LightingQuality $previousLightingQuality
     * @param LightingQuality $currentLightingQuality
     * @param RaceCode $raceCode
     * @param Tables $tables
     * @param PositiveInteger $roundsOfAdaptation how much time did you have to get used to current lighting
     * @return int
     */
    private function calculateValue(
        LightingQuality $previousLightingQuality,
        LightingQuality $currentLightingQuality,
        RaceCode $raceCode,
        Tables $tables,
        PositiveInteger $roundsOfAdaptation
    ): int
    {
        $sightRangesTable = $tables->getSightRangesTable();
        $maximalLighting = $sightRangesTable->getMaximalLighting($raceCode);
        $minimalLighting = $sightRangesTable->getMinimalLighting($raceCode);
        $previousLighting = $previousLightingQuality->getValue();
        if ($previousLighting < $minimalLighting) {
            $previousLighting = $minimalLighting;
        } elseif ($previousLighting > $maximalLighting) {
            $previousLighting = $maximalLighting;
        }
        $currentLighting = $currentLightingQuality->getValue();
        if ($currentLighting < $minimalLighting) {
            $currentLighting = $minimalLighting;
        } elseif ($currentLighting > $maximalLighting) {
            $currentLighting = $maximalLighting;
        }
        if ($previousLighting === $currentLighting) {
            return $currentLighting; // nothing to adapt at all
        }

        $difference = $previousLighting - $currentLighting;
        $needsAdaptForRounds = abs($difference); // needs one round if came to a darker place
        if ($difference < 0) { // from dark to light
            $needsAdaptForRounds *= 10; // needs ten rounds if came to lighter place
        }
        $effectiveRoundsOfAdaptation = $needsAdaptForRounds;
        if ($roundsOfAdaptation->getValue() < $needsAdaptForRounds) {
            $effectiveRoundsOfAdaptation = $roundsOfAdaptation->getValue();
        }

        if ($difference > 0) { // from light to dark
            return -$effectiveRoundsOfAdaptation; // needs one round for one point of difference if came to a darker place
        }

        return SumAndRound::floor($effectiveRoundsOfAdaptation / 10); // needs ten rounds for one point of difference if came to lighter place
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
        return (string)$this->getValue();
    }

}