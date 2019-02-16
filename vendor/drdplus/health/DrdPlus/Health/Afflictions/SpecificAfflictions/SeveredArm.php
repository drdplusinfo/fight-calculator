<?php
namespace DrdPlus\Health\Afflictions\SpecificAfflictions;

use DrdPlus\Health\Afflictions\AfflictionByWound;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\SeveredArmEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\EarthPertinence;
use DrdPlus\Health\SeriousWound;

/**
 * See PPH page 78, right column
 *
  * @method SeveredArmEffect getAfflictionEffect(): int
 */
class SeveredArm extends AfflictionByWound
{
    public const SEVERED_ARM = 'severed_arm';
    public const COMPLETELY_SEVERED_ARM = 'completely_severed_arm';
    public const COMPLETELY_SEVERED_ARM_SIZE = 6;

    /**
     * @param SeriousWound $seriousWound
     * @param int $sizeValue
     * @return SeveredArm
     * @throws \DrdPlus\Health\Afflictions\Exceptions\AfflictionSizeCanNotBeNegative
     * @throws \DrdPlus\Health\Afflictions\SpecificAfflictions\Exceptions\SeveredArmAfflictionSizeExceeded
     * @throws \DrdPlus\Health\Afflictions\Exceptions\WoundHasToBeFreshForAffliction
     * @throws \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     * @throws \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     */
    public static function createIt(SeriousWound $seriousWound, $sizeValue = self::COMPLETELY_SEVERED_ARM_SIZE): SeveredArm
    {
        $size = AfflictionSize::getIt($sizeValue); // completely severed arm has +6, partially related lower
        if ($size->getValue() > self::COMPLETELY_SEVERED_ARM_SIZE) {
            throw new Exceptions\SeveredArmAfflictionSizeExceeded(
                'Size of an affliction caused by severed arm can not be greater than ' . self::COMPLETELY_SEVERED_ARM_SIZE
            );
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(
            $seriousWound,
            AfflictionProperty::getIt(AfflictionProperty::TOUGHNESS), // irrelevant, full deformation can not be avoided
            AfflictionDangerousness::getIt(9999), // irrelevant, full deformation can not be avoided
            AfflictionDomain::getPhysicalDomain(),
            AfflictionVirulence::getDayVirulence(),
            AfflictionSource::getFullDeformationSource(),
            $size,
            EarthPertinence::getMinus(),
            SeveredArmEffect::getIt(),
            new \DateInterval('PT0S'), // immediately
            AfflictionName::getIt(
                $size->getValue() === self::COMPLETELY_SEVERED_ARM_SIZE
                    ? self::COMPLETELY_SEVERED_ARM
                    : self::SEVERED_ARM
            )
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
        return $this->getAfflictionEffect()->getStrengthMalus($this);
    }

    public function getAgilityMalus(): int
    {
        return 0;
    }

    public function getKnackMalus(): int
    {
        return $this->getAfflictionEffect()->getKnackMalus($this);
    }
}