<?php declare(strict_types=1);

namespace DrdPlus\CombatActions;

use DrdPlus\Codes\CombatActions\CombatActionCode;
use DrdPlus\Codes\CombatActions\MeleeCombatActionCode;
use DrdPlus\Codes\CombatActions\RangedCombatActionCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

class CombatActions extends StrictObject implements \IteratorAggregate, \Countable
{
    /** @var int */
    private $finishedRoundsOfAiming;
    /** @var array|CombatActionCode[] */
    private $combatActionCodes;

    /**
     * If you want numbers for more combinations than is possible in a single round (for complete list of modifications
     * for example) simply create more instances with different actions.
     *
     * @param array|string[]|CombatActionCode[] $combatActionCodes
     * @param Tables $tables
     * @param int $finishedRoundsOfAiming zero is for shooting without aim and disrupted aim
     * @throws \DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions
     * @throws \DrdPlus\CombatActions\Exceptions\InvalidFormatOfRoundsOfAiming
     * @throws \DrdPlus\CombatActions\Exceptions\UnknownCombatActionCode
     */
    public function __construct(
        array $combatActionCodes,
        Tables $tables,
        $finishedRoundsOfAiming = 0 // zero means you just start aim
    )
    {
        $sanitizedCombatActionCodes = $this->sanitizeCombatActionCodes($combatActionCodes);
        $this->validateActionCodesCoWork($sanitizedCombatActionCodes, $tables);
        $this->finishedRoundsOfAiming = $this->sanitizeRoundsOfAiming($finishedRoundsOfAiming);
        $this->combatActionCodes = [];
        foreach ($sanitizedCombatActionCodes as $combatActionCode) {
            $this->combatActionCodes[$combatActionCode->getValue()] = $combatActionCode;
        }
    }

    /**
     * @param array|string[]|CombatActionCode[] $combatActionCodes
     * @return array|CombatActionCode[]
     * @throws \DrdPlus\CombatActions\Exceptions\UnknownCombatActionCode
     */
    private function sanitizeCombatActionCodes(array $combatActionCodes): array
    {
        $sanitizedCombatActionCodes = [];
        foreach ($combatActionCodes as $combatActionCode) {
            if (\in_array((string)$combatActionCode, CombatActionCode::getPossibleValues(), true)) {
                $sanitizedCombatActionCodes[] = CombatActionCode::getIt($combatActionCode);
            } elseif (\in_array((string)$combatActionCode, MeleeCombatActionCode::getPossibleValues(), true)) {
                $sanitizedCombatActionCodes[] = MeleeCombatActionCode::getIt($combatActionCode);
            } elseif (\in_array((string)$combatActionCode, RangedCombatActionCode::getPossibleValues(), true)) {
                $sanitizedCombatActionCodes[] = RangedCombatActionCode::getIt($combatActionCode);
            } else {
                throw new Exceptions\UnknownCombatActionCode(
                    'Given action code is not known: ' . ValueDescriber::describe($combatActionCode)
                );
            }
        }

        return $sanitizedCombatActionCodes;
    }

    /**
     * @param array|CombatActionCode[] $combatActionCodes
     * @param Tables $tables
     * @throws \DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions
     */
    private function validateActionCodesCoWork(array $combatActionCodes, Tables $tables)
    {
        $this->guardUsableForSameAttackTypes($combatActionCodes);
        $this->checkIncompatibleActions($combatActionCodes, $tables);
    }

    /**
     * @param array|CombatActionCode[] $combatActionCodes
     * @throws \DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions
     */
    private function guardUsableForSameAttackTypes(array $combatActionCodes)
    {
        $forMeleeOnly = [];
        $forRangedOnly = [];
        foreach ($combatActionCodes as $combatActionCode) {
            if ($combatActionCode->isForMelee() && !$combatActionCode->isForRanged()) {
                $forMeleeOnly[] = $combatActionCode;
            }
            if ($combatActionCode->isForRanged() && !$combatActionCode->isForMelee()) {
                $forRangedOnly[] = $combatActionCode;
            }
        }
        if (count($forMeleeOnly) > 0 && count($forRangedOnly) > 0) {
            throw new Exceptions\IncompatibleCombatActions(
                'There are combat actions usable only for melee and another only for ranged, which prohibits their joining;'
                . ' melee: ' . \implode(', ', $forMeleeOnly) . '; ranged: ' . \implode(', ', $forRangedOnly)
            );
        }
    }

    /**
     * @param array|CombatActionCode[] $combatActionCodes
     * @param Tables $tables
     * @throws \DrdPlus\CombatActions\Exceptions\IncompatibleCombatActions
     */
    private function checkIncompatibleActions(array $combatActionCodes, Tables $tables)
    {
        $incompatiblePairs = [];
        $anotherCombatActionCodes = $combatActionCodes;
        foreach ($combatActionCodes as $combatActionCode) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            \array_shift($anotherCombatActionCodes); // remove an item from beginning
            foreach ($anotherCombatActionCodes as $anotherCombatActionCode) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                if ($combatActionCode !== $anotherCombatActionCode
                    && !$tables->getCombatActionsCompatibilityTable()
                        ->canCombineTwoActions($combatActionCode, $anotherCombatActionCode)
                ) {
                    $incompatiblePairs[] = [$combatActionCode, $anotherCombatActionCode];
                }
            }
        }
        if ($incompatiblePairs) {
            throw new Exceptions\IncompatibleCombatActions(
                'There are incompatible combat actions: '
                . \implode(
                    ', ',
                    \array_map(
                        function (array $incompatiblePair) {
                            return "'{$incompatiblePair[0]}' with '{$incompatiblePair[1]}'";
                        },
                        $incompatiblePairs
                    )
                )
            );
        }
    }

    /**
     * Aiming gives bonus up to three rounds of aim, any addition is thrown away.
     *
     * @param int $roundsOfAiming
     * @return int
     * @throws \DrdPlus\CombatActions\Exceptions\InvalidFormatOfRoundsOfAiming
     */
    private function sanitizeRoundsOfAiming($roundsOfAiming): int
    {
        try {
            $roundsOfAiming = ToInteger::toPositiveInteger($roundsOfAiming);
            if ($roundsOfAiming > 3) {
                return 3;
            }

            return $roundsOfAiming;
        } catch (\Granam\Integer\Tools\Exceptions\Exception $integerException) {
            throw new Exceptions\InvalidFormatOfRoundsOfAiming($integerException->getMessage());
        }
    }

    /**
     * @return array|CombatActionCode[]
     */
    public function getCombatActionCodes(): array
    {
        return $this->combatActionCodes;
    }

    /**
     * @return \ArrayIterator|CombatActionCode[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->combatActionCodes);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->combatActionCodes);
    }

    /**
     * Gives list of all combat actions separated,by,comma
     *
     * @return string
     */
    public function __toString()
    {
        return \implode(
            ',',
            \array_map(
                function (CombatActionCode $combatActionCode) {
                    return $combatActionCode->getValue();
                },
                $this->getIterator()->getArrayCopy()
            )
        );
    }

    /**
     * @return int
     */
    public function getFightNumberModifier(): int
    {
        $fightNumber = 0;
        foreach ($this->combatActionCodes as $combatActionCode) {
            if ($combatActionCode->getValue() === CombatActionCode::CONCENTRATION_ON_DEFENSE) {
                $fightNumber += 2;
            }
            if ($combatActionCode->getValue() === RangedCombatActionCode::AIMED_SHOT) {
                $fightNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::SWAP_WEAPONS) {
                $fightNumber -= 2;
            }
            if ($combatActionCode->getValue() === MeleeCombatActionCode::HANDOVER_ITEM) {
                $fightNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::LAYING) {
                $fightNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::SITTING_OR_ON_KNEELS) {
                $fightNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUTTING_ON_ARMOR) {
                $fightNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUTTING_ON_ARMOR_WITH_HELP) {
                $fightNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::HELPING_TO_PUT_ON_ARMOR) {
                $fightNumber -= 2;
            }
        }

        return $fightNumber;
    }

    /**
     * Note about AIMED SHOT, you have to provide rounds of aim to get expected attack number.
     * Maximum counted is +3, more if truncated.
     *
     * @return int
     */
    public function getAttackNumberModifier(): int
    {
        $attackNumber = 0;
        foreach ($this->combatActionCodes as $combatActionCode) {
            if ($combatActionCode->getValue() === MeleeCombatActionCode::HEADLESS_ATTACK) {
                $attackNumber += 2;
            }
            if ($combatActionCode->getValue() === MeleeCombatActionCode::PRESSURE) {
                $attackNumber += 2;
            }
            if ($combatActionCode->getValue() === RangedCombatActionCode::AIMED_SHOT) {
                $attackNumber += $this->finishedRoundsOfAiming;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUT_OUT_EASILY_ACCESSIBLE_ITEM) {
                $attackNumber += 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUT_OUT_HARDLY_ACCESSIBLE_ITEM) {
                $attackNumber += 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::LAYING) {
                $attackNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::SITTING_OR_ON_KNEELS) {
                $attackNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::BLINDFOLD_FIGHT) {
                $attackNumber -= 6;
            }
            if ($combatActionCode->getValue() === CombatActionCode::FIGHT_IN_REDUCED_VISIBILITY) {
                /** @noinspection PrefixedIncDecrementEquivalentInspection */
                $attackNumber -= 1;
            }
        }

        return $attackNumber;
    }

    /**
     * @param bool $usedWeaponDoesCrushWounds
     * @return int
     */
    public function getBaseOfWoundsModifier($usedWeaponDoesCrushWounds): int
    {
        $baseOfWounds = 0;
        if ($this->hasAction(MeleeCombatActionCode::HEADLESS_ATTACK)) {
            $baseOfWounds += 2;
        }
        if (!$usedWeaponDoesCrushWounds && $this->hasAction(MeleeCombatActionCode::FLAT_ATTACK)
        ) {
            $baseOfWounds -= 6;
        }

        return $baseOfWounds;
    }

    /**
     * @param string|CombatActionCode $combatActionCode
     * @return bool
     */
    private function hasAction($combatActionCode): bool
    {
        return \array_key_exists((string)$combatActionCode, $this->combatActionCodes);
    }

    /**
     * Note about RUN: if someone attacks you before you RUN, than you have to choose between canceling of RUN and
     * running with malus -4 to defense number and without weapon. Note about PUTTING OUT HARDLY ACCESSIBLE ITEM:
     * whenever someone attacks you before you put out desired item, than you have to choose between canceling of PUT
     * OUT... and continuing with malus -4 to defense number and without weapon, Note about ATTACKED FROM BEHIND: if
     * you are also surprised, then you can not defense yourself and your defense roll is automatically zero.
     *
     * @see getDefenseNumberModifierAgainstFasterOpponent
     * @return int
     */
    public function getDefenseNumberModifier(): int
    {
        $defenseNumber = 0;
        foreach ($this->combatActionCodes as $combatActionCode) {
            if ($combatActionCode->getValue() === MeleeCombatActionCode::HEADLESS_ATTACK) {
                $defenseNumber -= 5;
            }
            if ($combatActionCode->getValue() === CombatActionCode::CONCENTRATION_ON_DEFENSE) {
                $defenseNumber += 2;
            }
            if ($combatActionCode->getValue() === MeleeCombatActionCode::PRESSURE) {
                /** @noinspection PrefixedIncDecrementEquivalentInspection */
                $defenseNumber -= 1;
            }
            if ($combatActionCode->getValue() === MeleeCombatActionCode::RETREAT) {
                /** @noinspection PrefixedIncDecrementEquivalentInspection */
                $defenseNumber += 1;
            }
            if ($combatActionCode->getValue() === CombatActionCode::LAYING) {
                $defenseNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::SITTING_OR_ON_KNEELS) {
                $defenseNumber -= 2;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUTTING_ON_ARMOR) {
                $defenseNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUTTING_ON_ARMOR_WITH_HELP) {
                $defenseNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::ATTACKED_FROM_BEHIND) {
                $defenseNumber -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::BLINDFOLD_FIGHT) {
                $defenseNumber -= 10;
            }
            if ($combatActionCode->getValue() === CombatActionCode::FIGHT_IN_REDUCED_VISIBILITY) {
                $defenseNumber -= 2;
            }
        }

        return $defenseNumber;
    }

    /**
     * Against those opponents acting faster then you, you can have significantly lower defense because they catch you
     * unprepared.
     *
     * @return int
     */
    public function getDefenseNumberModifierAgainstFasterOpponent(): int
    {
        $defenseNumberModifier = $this->getDefenseNumberModifier();
        foreach ($this->combatActionCodes as $combatActionCode) {
            if ($combatActionCode->getValue() === CombatActionCode::RUN) {
                $defenseNumberModifier -= 4;
            }
            if ($combatActionCode->getValue() === CombatActionCode::PUT_OUT_HARDLY_ACCESSIBLE_ITEM) {
                $defenseNumberModifier -= 4;
            }
        }

        return $defenseNumberModifier;
    }

    /**
     * In case of MOVE or RUN there is significant speed increment.
     *
     * @return int
     */
    public function getSpeedModifier(): int
    {
        $speedBonus = 0;
        foreach ($this->combatActionCodes as $combatActionCode) {
            /** can not be combined with RUN, but that should be solved in @see validateActionCodesCoWork */
            if ($combatActionCode->getValue() === CombatActionCode::MOVE) {
                /** see PPH page 107 left column */
                $speedBonus += 8;
            }
            /** can not be combined with MOVE, but that should be solved in @see validateActionCodesCoWork */
            if ($combatActionCode->getValue() === CombatActionCode::RUN) {
                /** see PPH page 107 left column */
                $speedBonus += 22;
            }
        }

        return $speedBonus;
    }

    /**
     * @return bool
     */
    public function usesSimplifiedLightingRules(): bool
    {
        // see PPH page 129 top left
        return $this->hasAction(CombatActionCode::FIGHT_IN_REDUCED_VISIBILITY)
            || $this->hasAction(CombatActionCode::BLINDFOLD_FIGHT);
    }
}