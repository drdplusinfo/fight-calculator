<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Profession;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\BaseProperty;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;

class ProfessionZeroLevel extends ProfessionLevel
{
    /**
     * @param Commoner $commoner
     * @param \DateTimeImmutable|null $levelUpAt
     * @return ProfessionZeroLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidZeroLevelRank
     */
    public static function createZeroLevel(Commoner $commoner, \DateTimeImmutable $levelUpAt = null): ProfessionZeroLevel
    {
        return new static(
            $commoner,
            new LevelRank(0),
            Strength::getIt(0),
            Agility::getIt(0),
            Knack::getIt(0),
            Will::getIt(0),
            Intelligence::getIt(0),
            Charisma::getIt(0),
            $levelUpAt
        );
    }

    /**
     * @param LevelRank $levelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidZeroLevelRank
     */
    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() !== 0) {
            throw new Exceptions\InvalidZeroLevelRank(
                "Zero level has to have level rank 0, got {$levelRank->getValue()}"
            );
        }
    }

    /**
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidZeroLevelPropertyValue
     */
    protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession)
    {
        if ($baseProperty->getValue() !== 0) {
            throw new Exceptions\InvalidZeroLevelPropertyValue(
                'Expected 0 as base property "increment" for zero level, got ' . $baseProperty->getValue()
            );
        }
    }

}