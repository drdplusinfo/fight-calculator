<?php declare(strict_types=1);

namespace DrdPlus\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Health\Afflictions\AfflictionByWound;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\BleedingEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\WaterPertinence;
use DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative;
use DrdPlus\Health\SeriousWound;
use DrdPlus\Properties\Derived\WoundBoundary;

/**
 * See PPH page 78, right column
 *
 * @method BleedingEffect getAfflictionEffect(): int
 */
class Bleeding extends AfflictionByWound
{
    public const BLEEDING = 'bleeding';

    /**
     * @param SeriousWound $seriousWound
     * @param WoundBoundary $woundBoundary
     * @return Bleeding
     * @throws \DrdPlus\Health\Afflictions\SpecificAfflictions\Exceptions\BleedingCanNotExistsDueToTooLowWound
     * @throws \DrdPlus\Health\Afflictions\Exceptions\WoundHasToBeFreshForAffliction
     * @throws \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     * @throws \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     */
    public static function createIt(SeriousWound $seriousWound, WoundBoundary $woundBoundary): Bleeding
    {
        // see PPH page 78 right column, Bleeding
        $bleedingSizeValue = $seriousWound->getHealth()->getGridOfWounds()
                ->calculateFilledHalfRowsFor($seriousWound->getWoundSize(), $woundBoundary) - 1;
        try {
            $size = AfflictionSize::getIt($bleedingSizeValue);
        } catch (AfflictionSizeCanNotBeNegative $afflictionSizeCanNotBeNegative) {
            throw new Exceptions\BleedingCanNotExistsDueToTooLowWound(
                "Size of bleeding resulted into {$bleedingSizeValue}"
            );
        }

        return new static(
            $seriousWound,
            AfflictionProperty::getIt(PropertyCode::TOUGHNESS),
            AfflictionDangerousness::getIt(15),
            AfflictionDomain::getPhysicalDomain(),
            AfflictionVirulence::getRoundVirulence(),
            AfflictionSource::getActiveSource(),
            $size,
            WaterPertinence::getMinus(),
            BleedingEffect::getIt(),
            new \DateInterval('PT0S'), // immediately
            AfflictionName::getIt(self::BLEEDING)
        );
    }

    public function getHealMalus(): int
    {
        return 0;
    }

    public function getMalusToActivities(): int
    {
        return 0;
    }

    public function getStrengthMalus(): int
    {
        return 0;
    }

    public function getAgilityMalus(): int
    {
        return 0;
    }

    public function getKnackMalus(): int
    {
        return 0;
    }
}