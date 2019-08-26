<?php declare(strict_types=1);

namespace DrdPlus\Races;

use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Dwarfs\WoodDwarf;
use DrdPlus\Races\Humans\Highlander;
use DrdPlus\Races\Orcs\CommonOrc;
use DrdPlus\Races\Orcs\Orc;
use Granam\Strict\Object\StrictObject;

class RacesFactory extends StrictObject
{
    /**
     * @param RaceCode $raceCode
     * @param SubRaceCode $subRaceCode
     * @return Race
     * @throws \DrdPlus\Races\Exceptions\UnknownRaceCode
     */
    public static function getSubRaceByCodes(RaceCode $raceCode, SubRaceCode $subRaceCode): Race
    {
        $subRaceClass = static::getSubRaceClassByCodes($raceCode, $subRaceCode);

        return $subRaceClass::getIt();
    }

    /**
     * @param RaceCode $raceCode
     * @param SubRaceCode $subRaceCode
     * @return string|Race|WoodDwarf|CommonOrc ...
     * @throws \DrdPlus\Races\Exceptions\UnknownRaceCode
     */
    private static function getSubRaceClassByCodes(RaceCode $raceCode, SubRaceCode $subRaceCode): string
    {
        $raceCodeValue = $raceCode->getValue();
        $subRaceCodeValue = $subRaceCode->getValue();
        if ($raceCodeValue === RaceCode::ELF) {
            $baseNamespace = 'elves';
        } else {
            $baseNamespace = $raceCodeValue . 's';
        }
        $subraceNamespace = __NAMESPACE__ . '\\' . ucfirst($baseNamespace) . '\\';
        if ($raceCodeValue !== Orc::ORC || $subRaceCodeValue === CommonOrc::COMMON) {
            if ($subRaceCodeValue !== Highlander::HIGHLANDER) {
                $subRaceClass = $subraceNamespace . ucfirst($subRaceCodeValue) . ucfirst($raceCodeValue);
            } else {
                $subRaceClass = $subraceNamespace . ucfirst($subRaceCodeValue);
            }
        } else {
            $subRaceClass = $subraceNamespace . ucfirst($subRaceCodeValue);
        }
        if (!\class_exists($subRaceClass)) {
            throw new Exceptions\UnknownRaceCode(
                "Was searching for class {$subRaceClass}" . " created from race code {$raceCodeValue} and sub-race code {$subRaceCodeValue}"
            );
        }

        return $subRaceClass;
    }

}