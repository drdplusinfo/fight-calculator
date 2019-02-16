<?php
declare(strict_types=1);

namespace DrdPlus\Lighting;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Lighting\Partials\WithInsufficientLightingBonus;
use DrdPlus\Tables\Tables;
use Granam\Integer\NegativeInteger;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 128, @link https://pph.drdplus.jaroslavtyc.com/#postihy_za_nedostatecne_svetlo
 */
class UnsuitableLightingQualityMalus extends StrictObject implements NegativeInteger
{
    /**
     * @var int
     */
    private $malus;

    public static function createWithEyesAdaptation(
        EyesAdaptation $eyesAdaptation,
        LightingQuality $currentLightingQuality,
        Opacity $barrierOpacity,
        WithInsufficientLightingBonus $duskSightBonus,
        RaceCode $raceCode,
        SubRaceCode $subRaceCode,
        Tables $tables,
        bool $situationAllowsUseOfInfravision
    ): UnsuitableLightingQualityMalus
    {
        if ($barrierOpacity->getValue() > 0) {
            $currentLightingQuality = new LightingQuality($currentLightingQuality->getValue() - $barrierOpacity->getValue());
        }
        $contrast = Contrast::createByExtendedRules($eyesAdaptation, $currentLightingQuality, $raceCode, $tables);
        $possibleBaseMalus = -$contrast->getValue();

        return new static(
            $possibleBaseMalus,
            $currentLightingQuality,
            $duskSightBonus,
            $situationAllowsUseOfInfravision,
            $raceCode,
            $subRaceCode,
            $tables
        );
    }

    public static function createWithSimplifiedRules(
        LightingQuality $currentLightingQuality,
        Opacity $barrierOpacity,
        WithInsufficientLightingBonus $duskSightBonus,
        RaceCode $raceCode,
        SubRaceCode $subRaceCode,
        Tables $tables,
        bool $situationAllowsUseOfInfravision
    ): UnsuitableLightingQualityMalus
    {
        $possibleMalus = 0;
        if ($barrierOpacity->getValue() > 0) {
            $currentLightingQuality = new LightingQuality($currentLightingQuality->getValue() - $barrierOpacity->getValue());
        }
        /** see PPH page 128 right column bottom, @link https://pph.drdplus.jaroslavtyc.com/#postihy_za_nedostatecne_svetlo */
        if ($currentLightingQuality->getValue() < -10) {
            $contrast = Contrast::createBySimplifiedRules(new LightingQuality(0), $currentLightingQuality);
            $possibleMalus = -$contrast->getValue();
            if (in_array($raceCode->getValue(), [RaceCode::DWARF, RaceCode::ORC], true)) {
                $possibleMalus += 4; // lowering malus
            } elseif ($raceCode->getValue() === RaceCode::KROLL) {
                $possibleMalus += 2; // lowering malus
            }
        } elseif ($currentLightingQuality->getValue() >= 60 /* strong daylight */ && $raceCode->getValue() === RaceCode::ORC) {
            /** see PPH page 128 right column bottom, @link https://pph.drdplus.jaroslavtyc.com/#postih_skretu_za_ostreho_denniho_svetla */
            $possibleMalus = -2;
        }

        return new static(
            $possibleMalus,
            $currentLightingQuality,
            $duskSightBonus,
            $situationAllowsUseOfInfravision,
            $raceCode,
            $subRaceCode,
            $tables
        );
    }

    private function __construct(
        int $possibleBaseMalus,
        LightingQuality $currentLightingQuality,
        WithInsufficientLightingBonus $duskSightBonus,
        bool $situationAllowsUseOfInfravision,
        RaceCode $raceCode,
        SubRaceCode $subRaceCode,
        Tables $tables
    )
    {
        $possibleMalus = $possibleBaseMalus;
        if ($currentLightingQuality->getValue() < 0) {
            $possibleMalus += $duskSightBonus->getInsufficientLightingBonus(); // lowering malus
        }
        if ($situationAllowsUseOfInfravision && $currentLightingQuality->getValue() <= -90 // like star night
            && $tables->getRacesTable()->hasInfravision($raceCode, $subRaceCode)
        ) {
            /** lowering malus by infravision, see PPH page 129 right column, @link https://pph.drdplus.jaroslavtyc.com/#infravideni */
            $possibleMalus += 3;
        }
        $this->malus = 0;
        if ($possibleMalus >= -20) {
            if ($possibleMalus < 0) {
                $this->malus = $possibleMalus;
            } // otherwise zero (malus can not be positive)
        } else {
            /** maximal possible malus on absolute dark, see PPH page 128 right column bottom, @link https://pph.drdplus.jaroslavtyc.com/#postih_za_uplnou_tmu */
            $this->malus = -20;
        }
    }

    public function getValue(): int
    {
        return $this->malus;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

}