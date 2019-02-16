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
use DrdPlus\Health\Afflictions\Effects\PainEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use DrdPlus\Health\SeriousWound;

/**
 * see PPH page 79 left column
 *
  * @method PainEffect getAfflictionEffect(): int
 */
class Pain extends AfflictionByWound
{
    public const PAIN = 'pain';

    /**
     * @param SeriousWound $seriousWound
     * @param AfflictionVirulence $virulence
     * @param AfflictionSize $painSize
     * @param ElementalPertinence $elementalPertinence
     * @return Pain
     * @throws \DrdPlus\Health\Afflictions\Exceptions\WoundHasToBeFreshForAffliction
     * @throws \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     * @throws \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     */
    public static function createIt(
        SeriousWound $seriousWound,
        AfflictionVirulence $virulence,
        AfflictionSize $painSize,
        ElementalPertinence $elementalPertinence
    ): Pain
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static(
            $seriousWound,
            AfflictionProperty::getIt(PropertyCode::WILL),
            AfflictionDangerousness::getIt(10 + $painSize->getValue()),
            AfflictionDomain::getPhysicalDomain(),
            $virulence,
            AfflictionSource::getExternalSource(),
            $painSize,
            $elementalPertinence,
            PainEffect::getIt(),
            new \DateInterval('PT0S'), // immediately
            AfflictionName::getIt(self::PAIN)
        );
    }

    public function getHealMalus(): int
    {
        return 0;
    }

    public function getMalusToActivities(): int
    {
        // this malus affects activities, not passive actions like be healed
        return $this->getAfflictionEffect()->getMalusFromPain($this);
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