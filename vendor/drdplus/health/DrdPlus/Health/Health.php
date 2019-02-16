<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Health;

use DrdPlus\BaseProperties\Will;
use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Health\Afflictions\Affliction;
use DrdPlus\Health\Afflictions\AfflictionByWound;
use DrdPlus\Health\Afflictions\SpecificAfflictions\Pain;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\Lighting\Glare;
use DrdPlus\Properties\Derived\Toughness;
use DrdPlus\Properties\Derived\WoundBoundary;
use DrdPlus\RollsOn\Traps\RollOnWillAgainstMalus;
use DrdPlus\RollsOn\Traps\RollOnWill;
use DrdPlus\Tables\Tables;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use Granam\Strict\Object\StrictObject;

class Health extends StrictObject
{
    /**
     * @var array|Wound[]
     */
    private $wounds = [];
    /**
     * @var array|Affliction[]
     */
    private $afflictions = [];
    /**
     * Separates new and old (or serious) injuries.
     *
     * @var TreatmentBoundary
     */
    private $treatmentBoundary;
    /**
     * @var MalusFromWounds
     */
    private $malusFromWounds;
    /**
     * @var ReasonToRollAgainstMalusFromWounds|null
     */
    private $reasonToRollAgainstMalusFromWounds;
    /**
     * @var GridOfWounds|null is just a helper, does not need to be persisted
     */
    private $gridOfWounds;
    /**
     * @var bool helper to avoid side-adding of new wounds (Those created on their own and linked by Doctrine relation
     *     instead of directly here).
     */
    private $openForNewWound = false;
    /**
     * @var Glared
     */
    private $glared;

    public function __construct()
    {
        $this->treatmentBoundary = TreatmentBoundary::getIt(0);
        $this->malusFromWounds = MalusFromWounds::getIt(0);
        $this->glared = Glared::createWithoutGlare($this);
    }

    /**
     * @param WoundSize $woundSize
     * @param SeriousWoundOriginCode $seriousWoundOriginCode Beware that if the wound size is considered as NOT serious then
     *     OrdinaryWoundOrigin will be used instead (as the only possible for @see OrdinaryWound)
     * @param WoundBoundary $woundBoundary
     * @return OrdinaryWound|SeriousWound|Wound
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function addWound(
        WoundSize $woundSize,
        SeriousWoundOriginCode $seriousWoundOriginCode,
        WoundBoundary $woundBoundary
    ): Wound
    {
        $this->checkIfNeedsToRollAgainstMalusFirst();
        $this->openForNewWound = true;
        $wound = $this->isSeriousInjury($woundSize, $woundBoundary)
            ? new SeriousWound($this, $woundSize, $seriousWoundOriginCode)
            : new OrdinaryWound($this, $woundSize);
        $this->openForNewWound = false;
        $this->wounds[] = $wound;
        if ($wound->isSerious()) {
            $this->treatmentBoundary = TreatmentBoundary::getIt($this->getTreatmentBoundary()->getValue() + $wound->getValue());
        }
        $this->resolveMalusAfterWound($wound->getValue(), $woundBoundary);
        return $wound;
    }

    /**
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    private function checkIfNeedsToRollAgainstMalusFirst(): void
    {
        if ($this->needsToRollAgainstMalusFromWounds()) {
            throw new Exceptions\NeedsToRollAgainstMalusFromWoundsFirst(
                'Need to roll on will against malus caused by wounds because of previous '
                . $this->reasonToRollAgainstMalusFromWounds
            );
        }
    }

    /**
     * A lock of current health instance to ensure that new wound is created by that health,
     * @see \DrdPlus\Health\Wound::checkIfCreatedByGivenHealth
     * @return bool
     */
    public function isOpenForNewWound(): bool
    {
        return $this->openForNewWound;
    }

    private function isSeriousInjury(WoundSize $woundSize, WoundBoundary $woundBoundary): bool
    {
        return $this->getGridOfWounds()->calculateFilledHalfRowsFor($woundSize, $woundBoundary) > 0;
    }

    private function maySufferFromPain(WoundBoundary $woundBoundary): bool
    {
        // if the being became unconscious than the roll against pain malus is not re-rolled
        return
            $this->getGridOfWounds()->getNumberOfFilledRows($woundBoundary) >= GridOfWounds::PAIN_NUMBER_OF_ROWS
            && $this->isConscious($woundBoundary);
    }

    public function isConscious(WoundBoundary $woundBoundary): bool
    {
        return $this->getGridOfWounds()->getNumberOfFilledRows($woundBoundary) < GridOfWounds::UNCONSCIOUS_NUMBER_OF_ROWS;
    }

    /**
     * @param int $woundAmount
     * @param WoundBoundary $woundBoundary
     */
    private function resolveMalusAfterWound(int $woundAmount, WoundBoundary $woundBoundary)
    {
        if ($woundAmount === 0) {
            return;
        }
        if ($this->maySufferFromPain($woundBoundary)) {
            $this->reasonToRollAgainstMalusFromWounds = ReasonToRollAgainstMalusFromWounds::getWoundReason();
        } elseif ($this->isConscious($woundBoundary)) {
            $this->malusFromWounds = MalusFromWounds::getIt(0);
        } // otherwise left the previous malus - creature will suffer by it when comes conscious again
    }

    /**
     * Every serious injury SHOULD has at least one accompanying affliction (but it is PJ privilege to say it has not).
     *
     * @param Affliction $affliction
     * @throws \DrdPlus\Health\Exceptions\UnknownAfflictionOriginatingWound
     * @throws \DrdPlus\Health\Exceptions\AfflictionIsAlreadyRegistered
     */
    public function addAffliction(Affliction $affliction): void
    {
        if ($affliction instanceof AfflictionByWound && !$this->doesHaveThatWound($affliction->getSeriousWound())) {
            throw new Exceptions\UnknownAfflictionOriginatingWound(
                "Given affliction '{$affliction->getName()}' to add comes from unknown wound"
                . " of value {$affliction->getSeriousWound()} and origin '{$affliction->getSeriousWound()->getWoundOriginCode()}'."
                . ' Have you created that wound by current health?'
            );
        }
        if ($this->doesHaveThatAffliction($affliction)) {
            throw new Exceptions\AfflictionIsAlreadyRegistered(
                "Given instance of affliction '{$affliction->getName()}' is already added."
            );
        }
        $this->afflictions[] = $affliction;
    }

    /**
     * @param Wound $givenWound
     * @return bool
     */
    private function doesHaveThatWound(Wound $givenWound): bool
    {
        if ($givenWound->getHealth() !== $this) {
            return false; // easiest test - the wound belongs to different health
        }
        foreach ($this->wounds as $registeredWound) {
            if ($givenWound === $registeredWound) {
                return true; // this health recognizes that wound
            }
        }
        return false; // the wound know this health, but this health does not know that wound
    }

    /**
     * @param Affliction $givenAffliction
     * @return bool
     */
    private function doesHaveThatAffliction(Affliction $givenAffliction): bool
    {
        foreach ($this->afflictions as $registeredAffliction) {
            if ($givenAffliction === $registeredAffliction) {
                return true;
            }
        }
        return false;
    }

    /**
     * Also sets treatment boundary to unhealed wounds after. Even if the heal itself heals nothing!
     *
     * @param HealingPower $healingPower
     * @param WoundBoundary $woundBoundary
     * @return int amount of actually healed points of wounds
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function healFreshOrdinaryWounds(HealingPower $healingPower, WoundBoundary $woundBoundary): int
    {
        $this->checkIfNeedsToRollAgainstMalusFirst();
        // can heal new and ordinary wounds only, up to limit by current treatment boundary
        $healedAmount = 0;
        $remainingHealUpToWounds = $healingPower->getHealUpToWounds();
        foreach ($this->getUnhealedFreshOrdinaryWounds() as $newOrdinaryWound) {
            // wound is set as old internally, ALL OF THEM, even if no healing left
            $currentlyRegenerated = $newOrdinaryWound->heal($remainingHealUpToWounds);
            $remainingHealUpToWounds -= $currentlyRegenerated;
            $healedAmount += $currentlyRegenerated;
        }
        $this->treatmentBoundary = TreatmentBoundary::getIt($this->getUnhealedWoundsSum());
        $this->resolveMalusAfterHeal($healedAmount, $woundBoundary);

        return $healedAmount;
    }

    /**
     * @return OrdinaryWound[]|array
     */
    private function getUnhealedFreshOrdinaryWounds(): array
    {
        return \array_filter(
            $this->wounds,
            function (Wound $wound) {
                return !$wound->isHealed() && !$wound->isSerious() && !$wound->isOld();
            }
        );
    }

    /**
     * @return OrdinaryWound[]|array
     */
    private function getUnhealedFreshSeriousWounds(): array
    {
        return \array_filter(
            $this->wounds,
            function (Wound $wound) {
                return !$wound->isHealed() && $wound->isSerious() && !$wound->isOld();
            }
        );
    }

    /**
     * @param int $healedAmount
     * @param WoundBoundary $woundBoundary
     */
    private function resolveMalusAfterHeal(int $healedAmount, WoundBoundary $woundBoundary): void
    {
        if ($healedAmount === 0) { // both wounds remain the same and pain remains the same
            return;
        }
        if ($this->maySufferFromPain($woundBoundary)) {
            $this->reasonToRollAgainstMalusFromWounds = ReasonToRollAgainstMalusFromWounds::getHealReason();
        } elseif ($this->isConscious($woundBoundary)) {
            $this->malusFromWounds = MalusFromWounds::getIt(0); // pain is gone and creature feel it - lets remove the malus
        } // otherwise left the previous malus - creature will suffer by it when comes conscious again
    }

    /**
     * @param SeriousWound $seriousWound
     * @param HealingPower $healingPower
     * @param Toughness $toughness
     * @param Tables $tables
     * @return int amount of healed points of wounds
     * @throws \DrdPlus\Health\Exceptions\UnknownSeriousWoundToHeal
     * @throws \DrdPlus\Health\Exceptions\ExpectedFreshWoundToHeal
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function healFreshSeriousWound(
        SeriousWound $seriousWound,
        HealingPower $healingPower,
        Toughness $toughness,
        Tables $tables
    ): int
    {
        $this->checkIfNeedsToRollAgainstMalusFirst();
        if (!$this->doesHaveThatWound($seriousWound)) {
            throw new Exceptions\UnknownSeriousWoundToHeal(
                "Given serious wound of value {$seriousWound->getValue()} and origin"
                . " {$seriousWound->getWoundOriginCode()} to heal does not belongs to this health"
            );
        }
        if ($seriousWound->isOld()) {
            throw new Exceptions\ExpectedFreshWoundToHeal(
                "Given serious wound of value {$seriousWound->getValue()} and origin"
                . " {$seriousWound->getWoundOriginCode()} should not be old to be healed."
            );
        }
        $healedAmount = $seriousWound->heal($healingPower->getHealUpToWounds());
        // treatment boundary is taken with wounds down together
        $this->treatmentBoundary = TreatmentBoundary::getIt($this->treatmentBoundary->getValue() - $healedAmount);
        $this->resolveMalusAfterHeal($healedAmount, WoundBoundary::getIt($toughness, $tables));
        return $healedAmount;
    }

    /**
     * Regenerate any wound, both ordinary and serious, both new and old, by natural or unnatural way.
     *
     * @param HealingPower $healingPower
     * @param WoundBoundary $woundBoundary
     * @return int actually regenerated amount
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function regenerate(HealingPower $healingPower, WoundBoundary $woundBoundary): int
    {
        $this->checkIfNeedsToRollAgainstMalusFirst();
        // every wound becomes old after this
        $regeneratedAmount = 0;
        $remainingHealUpToWounds = $healingPower->getHealUpToWounds();
        foreach ($this->getUnhealedWounds() as $unhealedWound) {
            // wound is set as old internally, ALL OF THEM, even if no healing left
            $currentlyRegenerated = $unhealedWound->heal($remainingHealUpToWounds);
            $remainingHealUpToWounds -= $currentlyRegenerated;
            $regeneratedAmount += $currentlyRegenerated;
        }
        $this->treatmentBoundary = TreatmentBoundary::getIt($this->getUnhealedWoundsSum());
        $this->resolveMalusAfterHeal($regeneratedAmount, $woundBoundary);
        return $regeneratedAmount;
    }

    /**
     * Usable for info about amount of wounds which can be healed by basic healing
     *
     * @return int
     */
    public function getUnhealedFreshOrdinaryWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (OrdinaryWound $ordinaryWound) {
                    return $ordinaryWound->getValue();
                },
                $this->getUnhealedFreshOrdinaryWounds()
            )
        );
    }

    /**
     * Usable for info about amount of wounds which can be healed by treatment
     *
     * @return int
     */
    public function getUnhealedFreshSeriousWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (SeriousWound $seriousWound) {
                    return $seriousWound->getValue();
                },
                $this->getUnhealedFreshSeriousWounds()
            )
        );
    }

    public function getUnhealedOrdinaryWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (OrdinaryWound $seriousWound) {
                    return $seriousWound->getValue();
                },
                $this->getUnhealedOrdinaryWounds()
            )
        );
    }

    public function getUnhealedSeriousWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (SeriousWound $seriousWound) {
                    return $seriousWound->getValue();
                },
                $this->getUnhealedSeriousWounds()
            )
        );
    }

    /**
     * @return SeriousWound[]|array
     */
    private function getUnhealedOrdinaryWounds(): array
    {
        return \array_filter(
            $this->getUnhealedWounds(),
            function (Wound $wound) {
                return !$wound->isSerious() && !$wound->isHealed();
            }
        );
    }

    /**
     * @return SeriousWound[]|array
     */
    private function getUnhealedSeriousWounds(): array
    {
        return \array_filter(
            $this->getUnhealedWounds(),
            function (Wound $wound) {
                return $wound->isSerious() && !$wound->isHealed();
            }
        );
    }

    public function getUnhealedWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (Wound $unhealedWound) {
                    return $unhealedWound->getValue();
                },
                $this->getUnhealedWounds()
            )
        );
    }

    public function getUnhealedFreshWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (Wound $unhealedWound) {
                    return $unhealedWound->getValue();
                },
                $this->getUnhealedFreshWounds()
            )
        );
    }

    public function getUnhealedOldWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (Wound $unhealedWound) {
                    return $unhealedWound->getValue();
                },
                $this->getUnhealedOldWounds()
            )
        );
    }

    public function getUnhealedOldSeriousWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (Wound $unhealedWound) {
                    return $unhealedWound->getValue();
                },
                $this->getUnhealedOldSeriousWounds()
            )
        );
    }

    private function getUnhealedOldSeriousWounds(): array
    {
        return \array_filter(
            $this->getUnhealedOldWounds(),
            function (Wound $wound) {
                return $wound->isSerious() && $wound->isOld() && !$wound->isHealed();
            }
        );
    }

    public function getUnhealedOldOrdinaryWoundsSum(): int
    {
        return \array_sum(
            \array_map(
                function (Wound $unhealedWound) {
                    return $unhealedWound->getValue();
                },
                $this->getUnhealedOldOrdinaryWounds()
            )
        );
    }

    /**
     * @return array|Wound[]
     */
    private function getUnhealedOldOrdinaryWounds(): array
    {
        return \array_filter(
            $this->getUnhealedOldWounds(),
            function (Wound $wound) {
                return !$wound->isSerious() && $wound->isOld() && !$wound->isHealed();
            }
        );
    }

    /**
     * Can be healed only by regeneration.
     * @return array|Wound[]
     */
    public function getUnhealedOldWounds(): array
    {
        return \array_filter(
            $this->getUnhealedWounds(),
            function (Wound $wound) {
                return $wound->isOld();
            }
        );
    }

    /**
     * Can be healed by treatment.
     * @return array|Wound[]
     */
    public function getUnhealedFreshWounds(): array
    {
        return \array_filter(
            $this->getUnhealedWounds(),
            function (Wound $wound) {
                return !$wound->isOld();
            }
        );
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return int
     */
    public function getHealthMaximum(WoundBoundary $woundBoundary): int
    {
        return $woundBoundary->getValue() * GridOfWounds::TOTAL_NUMBER_OF_ROWS;
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return int
     */
    public function getRemainingHealthAmount(WoundBoundary $woundBoundary): int
    {
        return \max(0, $this->getHealthMaximum($woundBoundary) - $this->getUnhealedWoundsSum());
    }

    /**
     * Gives both fresh and old wounds
     *
     * @return array|Wound[]
     */
    public function getUnhealedWounds(): array
    {
        // results into different instance of array which avoids external change of the original
        return array_filter(
            $this->wounds,
            function (Wound $wound) {
                return !$wound->isHealed();
            }
        );
    }

    public function getGridOfWounds(): GridOfWounds
    {
        if ($this->gridOfWounds === null) {
            $this->gridOfWounds = new GridOfWounds($this);
        }
        return $this->gridOfWounds;
    }

    /**
     * Looking for a setter? Sorry but affliction can be caused only by a new wound.
     *
     * @return array|AfflictionByWound[]
     */
    public function getAfflictions(): array
    {
        return $this->afflictions;
    }

    public function getStrengthMalusFromAfflictions(): int
    {
        $strengthMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $strengthMalus += $afflictionByWound->getStrengthMalus();
        }
        return $strengthMalus;
    }

    public function getAgilityMalusFromAfflictions(): int
    {
        $agilityMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $agilityMalus += $afflictionByWound->getAgilityMalus();
        }
        return $agilityMalus;
    }

    public function getKnackMalusFromAfflictions(): int
    {
        $knackMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $knackMalus += $afflictionByWound->getKnackMalus();
        }
        return $knackMalus;
    }

    public function getWillMalusFromAfflictions(): int
    {
        $willMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $willMalus += $afflictionByWound->getWillMalus();
        }
        return $willMalus;
    }

    public function getIntelligenceMalusFromAfflictions(): int
    {
        $intelligenceMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $intelligenceMalus += $afflictionByWound->getIntelligenceMalus();
        }
        return $intelligenceMalus;
    }

    public function getCharismaMalusFromAfflictions(): int
    {
        $charismaMalus = 0;
        foreach ($this->getAfflictions() as $afflictionByWound) {
            $charismaMalus += $afflictionByWound->getCharismaMalus();
        }
        return $charismaMalus;
    }

    /**
     * Treatment boundary is set automatically on any heal (lowering wounds) or new serious injury
     *
     * @return TreatmentBoundary
     */
    public function getTreatmentBoundary(): TreatmentBoundary
    {
        return $this->treatmentBoundary;
    }

    public function getNumberOfSeriousInjuries(): int
    {
        return \count($this->getUnhealedSeriousWounds());
    }

    private const DEADLY_NUMBER_OF_SERIOUS_INJURIES = 6;

    /**
     * @param WoundBoundary $woundBoundary
     * @return bool
     */
    public function isAlive(WoundBoundary $woundBoundary): bool
    {
        return $this->getRemainingHealthAmount($woundBoundary) > 0
            && $this->getNumberOfSeriousInjuries() < self::DEADLY_NUMBER_OF_SERIOUS_INJURIES;
    }

    /**
     * Dominant, applied malus from wounds (pains respectively)
     * @param WoundBoundary $woundBoundary
     * @return int
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    public function getSignificantMalusFromPains(WoundBoundary $woundBoundary): int
    {
        $maluses = [$this->getMalusFromWoundsValue($woundBoundary)];
        foreach ($this->getPains() as $pain) {
            // for Pain see PPH page 79, left column
            $maluses[] = $pain->getMalusToActivities();
        }

        return \min($maluses); // the most significant malus (always lesser than zero), therefore the lowest value
    }

    /**
     * @param WoundBoundary $woundBoundary
     * @return int
     * @throws \DrdPlus\Health\Exceptions\NeedsToRollAgainstMalusFromWoundsFirst
     */
    private function getMalusFromWoundsValue(WoundBoundary $woundBoundary): int
    {
        $this->checkIfNeedsToRollAgainstMalusFirst();
        if (!$this->mayHaveMalusFromWounds($woundBoundary)) {
            return 0;
        }

        /**
         * note: Can grow only on new wound when reach second row in grid of wounds.
         * Can decrease only on heal of any wound when on second row in grid of wounds.
         * Is removed when first row of grid of wounds is not filled.
         * Even unconscious can has a malus (but would be wrong if applied).
         * See PPH page 75 right column
         */
        return $this->malusFromWounds->getValue();
    }

    /**
     * @return bool
     */
    public function needsToRollAgainstMalusFromWounds(): bool
    {
        return $this->reasonToRollAgainstMalusFromWounds !== null;
    }

    /**
     * @return ReasonToRollAgainstMalusFromWounds|null
     */
    public function getReasonToRollAgainstMalusFromWounds(): ?ReasonToRollAgainstMalusFromWounds
    {
        return $this->reasonToRollAgainstMalusFromWounds;
    }

    /**
     * @param Will $will
     * @param Roll2d6DrdPlus $roll2D6DrdPlus
     * @param WoundBoundary $woundBoundary
     * @return int resulted malus
     * @throws \DrdPlus\Health\Exceptions\UselessRollAgainstMalus
     */
    public function rollAgainstMalusFromWounds(
        Will $will,
        Roll2d6DrdPlus $roll2D6DrdPlus,
        WoundBoundary $woundBoundary
    ): int
    {
        if (!$this->needsToRollAgainstMalusFromWounds()) {
            throw new Exceptions\UselessRollAgainstMalus(
                'There is no need to roll against malus from wounds'
                . ($this->isConscious($woundBoundary) ? '' : ' (being is unconscious)')
            );
        }

        $malusValue = $this->reasonToRollAgainstMalusFromWounds->becauseOfHeal()
            ? $this->rollAgainstMalusOnHeal($will, $roll2D6DrdPlus)
            : $this->rollAgainstMalusOnWound($will, $roll2D6DrdPlus);

        $this->reasonToRollAgainstMalusFromWounds = null;

        return $malusValue;
    }

    private function rollAgainstMalusOnHeal(Will $will, Roll2d6DrdPlus $roll2D6DrdPlus): int
    {
        if ($this->malusFromWounds->getValue() === 0) {
            return $this->malusFromWounds->getValue(); // on heal can be the malus only lowered - there is nothing to lower
        }
        $newRoll = $this->createRollOnWillAgainstMalus($will, $roll2D6DrdPlus);
        // lesser (or same of course) malus remains; can not be increased on healing
        if ($this->malusFromWounds->getValue() >= $newRoll->getMalusValue()) { // greater in mathematical meaning (malus is negative)
            return $this->malusFromWounds->getValue(); // lesser malus remains
        }
        $malusFromWounds = $this->setMalusFromWounds($newRoll);

        return $malusFromWounds->getValue();
    }

    private function createRollOnWillAgainstMalus(Will $will, Roll2d6DrdPlus $roll2D6DrdPlus): RollOnWillAgainstMalus
    {
        return new RollOnWillAgainstMalus(new RollOnWill($will, $roll2D6DrdPlus));
    }

    private function setMalusFromWounds(RollOnWillAgainstMalus $rollOnWillAgainstMalus): MalusFromWounds
    {
        return $this->malusFromWounds = MalusFromWounds::getIt($rollOnWillAgainstMalus->getMalusValue());
    }

    private function rollAgainstMalusOnWound(Will $will, Roll2d6DrdPlus $roll2D6DrdPlus): int
    {
        if ($this->malusFromWounds->getValue() === MalusFromWounds::MOST) {
            return $this->malusFromWounds->getValue();
        }
        $newRoll = $this->createRollOnWillAgainstMalus($will, $roll2D6DrdPlus);
        // bigger (or same of course) malus remains; can not be decreased on new wounds
        if ($this->malusFromWounds->getValue() <= $newRoll->getMalusValue() // lesser in mathematical meaning (malus is negative)
        ) {
            return $this->malusFromWounds->getValue(); // greater malus remains
        }
        return $this->setMalusFromWounds($newRoll)->getValue();
    }

    /**
     * @return array|Pain[]
     */
    public function getPains(): array
    {
        return \array_filter(
            $this->getAfflictions(),
            function (Affliction $affliction) {
                return $affliction instanceof Pain;
            }
        );
    }

    public function inflictByGlare(Glare $glare)
    {
        $this->glared = Glared::createFromGlare($glare, $this);
    }

    public function getGlared(): Glared
    {
        return $this->glared;
    }

    public function hasFreshWounds(): bool
    {
        foreach ($this->getUnhealedWounds() as $unhealedWound) {
            if (!$unhealedWound->isOld()) {
                return true;
            }
        }
        return false;
    }

    public function maySufferFromWounds(WoundBoundary $woundBoundary): bool
    {
        // if you became unconscious than the roll against pain malus is not re-rolled
        return $this->mayHaveMalusFromWounds($woundBoundary) && $this->isConscious($woundBoundary);
    }

    public function mayHaveMalusFromWounds(WoundBoundary $woundBoundary): bool
    {
        return $this->getGridOfWounds()->getNumberOfFilledRows($woundBoundary) >= GridOfWounds::PAIN_NUMBER_OF_ROWS;
    }
}