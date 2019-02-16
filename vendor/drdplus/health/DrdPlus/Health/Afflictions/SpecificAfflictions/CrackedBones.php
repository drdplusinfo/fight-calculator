<?php
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
use DrdPlus\Health\Afflictions\Effects\CrackedBonesEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\EarthPertinence;
use DrdPlus\Health\SeriousWound;
use DrdPlus\Properties\Derived\WoundBoundary;

/**
  * @method CrackedBonesEffect getAfflictionEffect(): int
 */
class CrackedBones extends AfflictionByWound
{
    public const CRACKED_BONES = 'cracked_bones';

    /**
     * @param SeriousWound $seriousWound
     * @param WoundBoundary $woundBoundary
     * @return CrackedBones
     * @throws \DrdPlus\Health\Afflictions\Exceptions\WoundHasToBeFreshForAffliction
     */
    public static function createIt(SeriousWound $seriousWound, WoundBoundary $woundBoundary): CrackedBones
    {
        // see PPH page 78 right column, Cracked bones
        $sizeValue = 2 * $seriousWound
                ->getHealth()
                ->getGridOfWounds()
                ->calculateFilledHalfRowsFor($seriousWound->getWoundSize(), $woundBoundary);

        return new static(
            $seriousWound,
            AfflictionProperty::getIt(PropertyCode::TOUGHNESS),
            AfflictionDangerousness::getIt(15),
            AfflictionDomain::getPhysicalDomain(),
            AfflictionVirulence::getDayVirulence(),
            AfflictionSource::getPassiveSource(),
            AfflictionSize::getIt($sizeValue),
            EarthPertinence::getMinus(),
            CrackedBonesEffect::getIt(),
            new \DateInterval('PT0S'), // immediately
            AfflictionName::getIt(self::CRACKED_BONES)
        );
    }

    public function getHealMalus(): int
    {
        return $this->getAfflictionEffect()->getHealingMalus($this);
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