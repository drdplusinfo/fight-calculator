<?php declare(strict_types=1);

namespace DrdPlus\FightProperties;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\ArmorCode;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\Units\DistanceUnitCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Body\PhysicalWoundTypeCode;
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Combat\Attack;
use DrdPlus\Properties\Combat\AttackNumber;
use DrdPlus\Properties\Combat\Defense;
use DrdPlus\Properties\Combat\DefenseNumber;
use DrdPlus\Properties\Combat\EncounterRange;
use DrdPlus\Properties\Combat\Fight;
use DrdPlus\Properties\Combat\FightNumber;
use DrdPlus\Properties\Combat\LoadingInRounds;
use DrdPlus\Properties\Combat\MaximalRange;
use DrdPlus\Properties\Combat\Shooting;
use DrdPlus\Skills\Skills;
use DrdPlus\Armourer\Armourer;
use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Distance\DistanceBonus;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus as BaseOfWounds;
use DrdPlus\Tables\Measurements\Wounds\WoundsBonus;
use DrdPlus\Tables\Tables;
use Granam\Boolean\Tools\ToBoolean;
use Granam\Strict\Object\StrictObject;

class FightProperties extends StrictObject
{
    /** @var BodyPropertiesForFight */
    private $bodyPropertiesForFight;
    /** @var Skills */
    private $skills;
    /** @var BodyArmorCode */
    private $wornBodyArmor;
    /** @var HelmCode */
    private $wornHelm;
    /** @var ProfessionCode */
    private $professionCode;
    /** @var Tables */
    private $tables;
    /** @var Armourer */
    private $armourer;
    /** @var ItemHoldingCode */
    private $weaponlikeHolding;
    /** @var WeaponlikeCode */
    private $weaponlike;
    /** @var bool */
    private $fightsWithTwoWeapons;
    /** @var CombatActions */
    private $combatActions;
    /** @var ShieldCode */
    private $shield;
    /** @var bool */
    private $enemyIsFasterThanYou;
    /** @var Glared */
    private $glared;
    /** @var bool */
    private $fightsOnHorseback;
    /** @var bool */
    private $fightsFreeWillAnimal;

    /** @var Defense */
    private $defense;
    /** @var Attack */
    private $attack;
    /** @var Shooting */
    private $shooting;
    /** @var Fight */
    private $fight;
    /** @var FightNumber */
    private $fightNumber;
    /** @var BaseOfWounds */
    private $baseOfWounds;
    /** @var DefenseNumber */
    private $defenseNumber;
    /** @var DefenseNumber */
    private $defenseNumberWithShield;
    /** @var Distance */
    private $movedDistance;
    /** @var MaximalRange */
    private $maximalRange;
    /** @var EncounterRange */
    private $encounterRange;
    /** @var LoadingInRounds */
    private $loadingInRounds;

    /**
     * Even shield can be used as a weapon, because it is @see WeaponlikeCode
     * Use @see ShieldCode::WITHOUT_SHIELD for no shield.
     * Note about SHIELD and range attack - there is really confusing rule on PPH page 86 right column about AUTOMATIC
     * cover by shield even if you do not know about attack. So you are not using that shield at all, it just exists.
     * So there is no malus by missing strength or skill. So you would have full cover with any shield...?
     * Don't think so... so that rule is IGNORED here.
     *
     * @param BodyPropertiesForFight $bodyPropertiesForFight
     * @param CombatActions $combatActions
     * @param Skills $skills
     * @param BodyArmorCode $wornBodyArmor
     * @param HelmCode $wornHelm
     * @param ProfessionCode $professionCode
     * @param Tables $tables
     * @param Armourer $armourer
     * @param WeaponlikeCode $weaponlike
     * @param ItemHoldingCode $weaponlikeHolding
     * @param bool $fightsWithTwoWeapons
     * @param ShieldCode $shield
     * @param bool $enemyIsFasterThanYou
     * @param Glared $glared
     * @param bool $fightsOnHorseback
     * @param bool $fightsFreeWillAnimal
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotHoldItByTwoHands
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotHoldItByOneHand
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     * @throws \DrdPlus\FightProperties\Exceptions\ImpossibleActionsWithCurrentWeaponlike
     * @throws \DrdPlus\FightProperties\Exceptions\UnknownWeaponHolding
     * @throws \DrdPlus\FightProperties\Exceptions\NoHandLeftForShield
     * @throws \Granam\Boolean\Tools\Exceptions\WrongParameterType
     */
    public function __construct(
        BodyPropertiesForFight $bodyPropertiesForFight,
        CombatActions $combatActions,
        Skills $skills,
        BodyArmorCode $wornBodyArmor, /** use @see BodyArmorCode::WITHOUT_ARMOR for no armor */
        HelmCode $wornHelm, /** use @see HelmCode::WITHOUT_HELM for no helm */
        ProfessionCode $professionCode,
        Tables $tables,
        Armourer $armourer,
        WeaponlikeCode $weaponlike, /** use @see MeleeWeaponCode::HAND for no weapon */
        ItemHoldingCode $weaponlikeHolding,
        bool $fightsWithTwoWeapons,
        ShieldCode $shield, /** use @see ShieldCode::WITHOUT_SHIELD for no shield */
        bool $enemyIsFasterThanYou,
        Glared $glared,
        bool $fightsOnHorseback,
        bool $fightsFreeWillAnimal
    )
    {
        $this->bodyPropertiesForFight = $bodyPropertiesForFight;
        $this->skills = $skills;
        $this->wornBodyArmor = $wornBodyArmor;
        $this->wornHelm = $wornHelm;
        $this->professionCode = $professionCode;
        $this->tables = $tables;
        $this->armourer = $armourer;
        $this->weaponlike = $weaponlike;
        $this->weaponlikeHolding = $weaponlikeHolding;
        $this->fightsWithTwoWeapons = ToBoolean::toBoolean($fightsWithTwoWeapons);
        $this->combatActions = $combatActions;
        $this->shield = $shield;
        $this->enemyIsFasterThanYou = ToBoolean::toBoolean($enemyIsFasterThanYou);
        $this->glared = $glared;
        $this->fightsOnHorseback = $fightsOnHorseback;
        $this->fightsFreeWillAnimal = $fightsFreeWillAnimal;
        $this->guardWornBodyArmorWearable();
        $this->guardWornHelmWearable();
        $this->guardKnownHolding();
        $this->guardHoldingCompatibleWithWeaponlike();
        $this->guardCombatActionsCompatibleWithWeaponlike();
        $this->guardShieldWearable();
        $this->guardWeaponlikeWearable();
    }

    /**
     * @throws Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    private function guardWornBodyArmorWearable(): void
    {
        $this->guardArmamentWearable(
            $this->wornBodyArmor,
            $this->bodyPropertiesForFight->getStrength(),
            $this->bodyPropertiesForFight->getSize()
        );
    }

    /**
     * @param ArmamentCode $armamentCode
     * @param Strength $strength
     * @param Size $size
     * @throws Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    private function guardArmamentWearable(ArmamentCode $armamentCode, Strength $strength, Size $size): void
    {
        if (!$this->armourer->canUseArmament($armamentCode, $strength, $size)) {
            throw new Exceptions\CanNotUseArmamentBecauseOfMissingStrength(
                "'{$armamentCode}' is too heavy"
                . ($armamentCode instanceof ArmorCode ? " for body of size {$strength}" : '')
                . " to be used by with strength {$strength}"
            );
        }
    }

    /**
     * @throws Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    private function guardWornHelmWearable(): void
    {
        $this->guardArmamentWearable(
            $this->wornHelm,
            $this->bodyPropertiesForFight->getStrength(),
            $this->bodyPropertiesForFight->getSize()
        );
    }

    /**
     * @throws Exceptions\UnknownWeaponHolding
     */
    private function guardKnownHolding(): void
    {
        if (!$this->weaponlikeHolding->holdsByMainHand()
            && !$this->weaponlikeHolding->holdsByOffhand()
            && !$this->weaponlikeHolding->holdsByTwoHands()
        ) {
            throw new Exceptions\UnknownWeaponHolding("Given holding {$this->weaponlikeHolding} is strange");
        }
    }

    /**
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotHoldItByTwoHands
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotHoldItByOneHand
     */
    private function guardHoldingCompatibleWithWeaponlike(): void
    {
        if ($this->fightsWithTwoWeapons && $this->weaponlikeHolding->holdsByTwoHands()) {
            throw new Exceptions\CanNotHoldItByTwoHands(
                "Can not hold weapon '{$this->weaponlike}' by two hands when using two weapons"
            );
        }
        if ($this->weaponlikeHolding->holdsByTwoHands()
            && !$this->armourer->canHoldItByTwoHands($this->weaponlike)
        ) {
            throw new Exceptions\CanNotHoldItByTwoHands(
                "You can not hold '{$this->weaponlike}' by '{$this->weaponlikeHolding}'"
            );
        }
        if ($this->weaponlikeHolding->holdsByOneHand()
            && !$this->armourer->canHoldItByOneHand($this->weaponlike)
        ) {
            throw new Exceptions\CanNotHoldItByOneHand(
                "You can not hold '{$this->weaponlike}' by '{$this->weaponlikeHolding}'"
            );
        }
    }

    /**
     * @throws Exceptions\ImpossibleActionsWithCurrentWeaponlike
     */
    private function guardCombatActionsCompatibleWithWeaponlike(): void
    {
        $possibleActions = $this->tables->getCombatActionsWithWeaponTypeCompatibilityTable()
            ->getActionsPossibleWhenFightingWith($this->weaponlike);
        $currentActions = $this->combatActions->getIterator()->getArrayCopy();
        $impossibleActions = array_diff($currentActions, $possibleActions);
        if (\count($impossibleActions) > 0) {
            throw new Exceptions\ImpossibleActionsWithCurrentWeaponlike(
                "With {$this->weaponlike} you can not do " . implode(', ', $impossibleActions)
            );
        }
    }

    /**
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    private function guardWeaponlikeWearable(): void
    {
        $this->guardWeaponOrShieldWearable($this->weaponlike, $this->getStrengthForWeaponlike());
    }

    /**
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     * @throws \DrdPlus\FightProperties\Exceptions\NoHandLeftForShield
     */
    private function guardShieldWearable(): void
    {
        $this->guardWeaponOrShieldWearable($this->shield, $this->getStrengthForShield());
    }

    /**
     * @param WeaponlikeCode $weaponlikeCode
     * @param Strength $currentStrengthForWeapon
     * @throws \DrdPlus\FightProperties\Exceptions\CanNotUseArmamentBecauseOfMissingStrength
     */
    private function guardWeaponOrShieldWearable(WeaponlikeCode $weaponlikeCode, Strength $currentStrengthForWeapon): void
    {
        $this->guardArmamentWearable(
            $weaponlikeCode,
            $currentStrengthForWeapon,
            $this->bodyPropertiesForFight->getSize()
        );
    }

    /**
     * @return Strength
     */
    private function getStrengthForWeaponlike(): Strength
    {
        return $this->getStrengthForWeaponOrShield($this->weaponlike, $this->weaponlikeHolding);
    }

    /**
     * If one-handed weapon or shield is kept by both hands, the required strength for weapon is lower
     * (fighter strength is considered higher respectively), see details in PPH page 93, left column.
     *
     * @param WeaponlikeCode $weaponOrShield
     * @param ItemHoldingCode $itemHoldingCode
     * @return Strength
     */
    private function getStrengthForWeaponOrShield(WeaponlikeCode $weaponOrShield, ItemHoldingCode $itemHoldingCode): Strength
    {
        return $this->armourer->getStrengthForWeaponOrShield(
            $weaponOrShield,
            $itemHoldingCode,
            $this->bodyPropertiesForFight->getStrengthOfMainHand()
        );
    }

    /**
     * @return Strength
     * @throws \DrdPlus\FightProperties\Exceptions\NoHandLeftForShield
     */
    private function getStrengthForShield(): Strength
    {
        return $this->getStrengthForWeaponOrShield($this->shield, $this->getShieldHolding());
    }

    /**
     * Gives holding opposite to given weapon holding.
     *
     * @return ItemHoldingCode
     * @throws \DrdPlus\FightProperties\Exceptions\NoHandLeftForShield
     */
    private function getShieldHolding(): ItemHoldingCode
    {
        if ($this->weaponlikeHolding->holdsByMainHand()) {
            return ItemHoldingCode::getIt(ItemHoldingCode::OFFHAND);
        }
        if ($this->weaponlikeHolding->holdsByOffhand()) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        // two hands holding
        if ($this->shield->getValue() === ShieldCode::WITHOUT_SHIELD) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        throw new Exceptions\NoHandLeftForShield(
            "Can not hold {$this->shield} when holding {$this->weaponlike} with {$this->weaponlikeHolding}"
        );
    }

    /**
     * @return Fight
     */
    public function getFight(): Fight
    {
        if ($this->fight === null) {
            $this->fight = Fight::getIt(
                $this->professionCode,
                $this->bodyPropertiesForFight,
                $this->bodyPropertiesForFight->getHeight(),
                $this->tables
            );
        }

        return $this->fight;
    }

    /**
     * Final fight number including body state (level, fatigue, wounds, curses...), used weapon and chosen action.
     *
     * @return FightNumber
     */
    public function getFightNumber(): FightNumber
    {
        if ($this->fightNumber === null) {
            $this->fightNumber = FightNumber::getIt($this->getFight(), $this->getLongerWeaponlike(), $this->tables)
                ->add($this->getFightNumberModifier());
        }

        return $this->fightNumber;
    }

    /**
     * Fight number update according to a missing strength, missing skill and by a combat action
     *
     * @return int
     */
    private function getFightNumberModifier(): int
    {
        $fightNumberModifier = 0;

        // strength effect
        $fightNumberModifier += $this->getFightNumberMalusByStrength();

        // skills effect
        $fightNumberModifier += $this->getFightNumberMalusBySkills();

        // combat actions effect
        $fightNumberModifier += $this->combatActions->getFightNumberModifier();

        return $fightNumberModifier;
    }

    /**
     * @return int
     */
    private function getFightNumberMalusByStrength(): int
    {
        $fightNumberMalus = 0;

        $fightNumberMalus += $this->armourer->getFightNumberMalusByStrengthWithWeaponOrShield(
            $this->weaponlike,
            $this->getStrengthForWeaponlike()
        );
        $fightNumberMalus += $this->armourer->getFightNumberMalusByStrengthWithWeaponOrShield(
            $this->shield,
            $this->getStrengthForShield()
        );

        return $fightNumberMalus;
    }

    /**
     * @return int
     */
    private function getFightNumberMalusBySkills(): int
    {
        $fightNumberMalus = 0;

        // weapon
        $fightNumberMalus += $this->skills->getMalusToFightNumberWithWeaponlike(
            $this->weaponlike,
            $this->tables,
            $this->fightsWithTwoWeapons
        );

        // armor and helm
        $fightNumberMalus += $this->skills->getMalusToFightNumberWithProtective(
            $this->wornBodyArmor,
            $this->armourer
        );
        $fightNumberMalus += $this->skills->getMalusToFightNumberWithProtective(
            $this->wornHelm,
            $this->armourer
        );

        // shields
        $fightNumberMalus += $this->skills->getMalusToFightNumberWithProtective(
            $this->shield,
            $this->armourer
        );
        // rare situation when you have two shields (or shield and nothing) and uses one as a weapon
        if ($this->weaponlike->isShield()) {
            /** @var ShieldCode $shieldAsWeapon */
            $shieldAsWeapon = $this->weaponlike;
            $fightNumberMalus += $this->skills->getMalusToFightNumberWithProtective(
                $shieldAsWeapon,
                $this->armourer
            );
        }

        // ride skill
        if ($this->fightsOnHorseback) {
            $fightNumberMalus += $this->skills->getMalusToAttackNumberWhenRiding();
        }

        return $fightNumberMalus;
    }

    /**
     * @return WeaponlikeCode
     */
    private function getLongerWeaponlike(): WeaponlikeCode
    {
        $weaponlikeLength = $this->armourer->getLengthOfWeaponOrShield($this->weaponlike);
        // shields have length 0, but who knows...
        $shieldLength = $this->armourer->getLengthOfWeaponOrShield($this->shield);
        if ($weaponlikeLength >= $shieldLength) {
            return $this->weaponlike;
        }

        return $this->shield;
    }

    // ATTACK

    /**
     * @return Attack
     */
    public function getAttack(): Attack
    {
        if ($this->attack === null) {
            $this->attack = Attack::getIt($this->bodyPropertiesForFight->getAgility());
        }

        return $this->attack;
    }

    /**
     * @return Shooting
     */
    public function getShooting(): Shooting
    {
        if ($this->shooting === null) {
            $this->shooting = Shooting::getIt($this->bodyPropertiesForFight->getKnack());
        }

        return $this->shooting;
    }

    /**
     * Final attack number including body state (level, fatigue, wounds, curses...), used weapon and action.
     *
     * @param Distance $targetDistance
     * @param Size $targetSize
     * @return AttackNumber
     */
    public function getAttackNumber(Distance $targetDistance, Size $targetSize): AttackNumber
    {
        return $this->createBaseAttackNumber()->add($this->getAttackNumberModifier($targetDistance, $targetSize));
    }

    /**
     * @return AttackNumber
     */
    private function createBaseAttackNumber(): AttackNumber
    {
        if ($this->weaponlike->isShootingWeapon()) {
            return AttackNumber::getItFromShooting($this->getShooting());
        }

        // covers melee and throwing weapons
        return AttackNumber::getItFromAttack($this->getAttack());
    }

    /**
     * @param Distance $targetDistance
     * @param Size $targetSize
     * @return int
     * @throws \DrdPlus\Tables\Armaments\Exceptions\DistanceIsOutOfMaximalRange
     * @throws \DrdPlus\Tables\Armaments\Exceptions\EncounterRangeCanNotBeGreaterThanMaximalRange
     * @throws \DrdPlus\Tables\Combat\Attacks\Exceptions\DistanceOutOfKnownValues
     */
    private function getAttackNumberModifier(Distance $targetDistance, Size $targetSize): int
    {
        $attackNumberModifier = 0;
        $armourer = $this->armourer;

        // strength effect
        $attackNumberModifier += $armourer->getAttackNumberMalusByStrengthWithWeaponlike(
            $this->weaponlike,
            $this->getStrengthForWeaponlike()
        );

        // skills effect
        $attackNumberModifier += $this->skills->getMalusToAttackNumberWithWeaponlike(
            $this->weaponlike,
            $this->tables,
            $this->fightsWithTwoWeapons // affects also ranged (mini-crossbows can be hold in one hand for example)
        );

        // weapon effects
        $attackNumberModifier += $armourer->getOffensivenessOfWeaponlike($this->weaponlike);

        // combat actions effect
        $attackNumberModifier += $this->combatActions->getAttackNumberModifier();

        if (!$this->combatActions->usesSimplifiedLightingRules() && $this->glared->getCurrentMalus() < 0) {
            /** see PPH page 129 top left, @link https://pph.drdplus.info/#cinnosti_pri_nedostatecne_viditelnosti */
            $attackNumberModifier += SumAndRound::half($this->glared->getCurrentMalus());
        }

        // ride skill
        if ($this->fightsOnHorseback) {
            $attackNumberModifier += $this->skills->getMalusToAttackNumberWhenRiding();
        }

        // zoology skill
        if ($this->fightsFreeWillAnimal) {
            $attackNumberModifier += $this->skills->getBonusToAttackNumberAgainstFreeWillAnimal();
        }

        // distance effect (for ranged only)
        if ($this->weaponlike->isRanged()) {
            $attackNumberModifier += $armourer->getAttackNumberModifierByDistance(
                $targetDistance,
                $this->getEncounterRange(),
                $this->getMaximalRange()
            );
            $attackNumberModifier += $armourer->getAttackNumberModifierBySize($targetSize);
        }

        return $attackNumberModifier;
    }

    /**
     * Gives @see WoundsBonus - if you need current Wounds just convert WoundsBonus to it by WoundsBonus->getWounds()
     * This number is without actions.
     *
     * @see Wounds
     * Note about both hands holding of a weapon - if you have empty off-hand (without shield) and the weapon you are
     * holding is single-hand, it will automatically add +2 for two-hand holding (if you choose such action).
     * See PPH page 92 right column.
     * @return BaseOfWounds
     */
    public function getBaseOfWounds(): BaseOfWounds
    {
        if ($this->baseOfWounds === null) {
            $baseOfWoundsValue = 0;

            // strength and weapon effects
            $baseOfWoundsValue += $this->armourer->getBaseOfWoundsUsingWeaponlike(
                $this->weaponlike,
                $this->getStrengthForWeaponlike()
            );

            // skill effect
            $baseOfWoundsValue += $this->skills->getMalusToBaseOfWoundsWithWeaponlike(
                $this->weaponlike,
                $this->tables,
                $this->fightsWithTwoWeapons
            );

            // holding effect
            $baseOfWoundsValue += $this->armourer->getBaseOfWoundsBonusForHolding(
                $this->weaponlike,
                $this->weaponlikeHolding
            );

            // action effects
            $baseOfWoundsValue += $this->combatActions->getBaseOfWoundsModifier(
                $this->tables->getWeaponlikeTableByWeaponlikeCode($this->weaponlike)
                    ->getWoundsTypeOf($this->weaponlike) === PhysicalWoundTypeCode::CRUSH
            );
            if ($this->fightsFreeWillAnimal) {
                $baseOfWoundsValue += $this->skills->getBonusToBaseOfWoundsAgainstFreeWillAnimal();
            }

            $this->baseOfWounds = new BaseOfWounds($baseOfWoundsValue, $this->tables->getWoundsTable());
        }

        return $this->baseOfWounds;
    }

    /**
     * Note: for melee weapons the loading is zero.
     *
     * @return LoadingInRounds
     */
    public function getLoadingInRounds(): LoadingInRounds
    {
        if ($this->loadingInRounds === null) {
            $loadingInRoundsValue = 0;
            if ($this->weaponlike instanceof RangedWeaponCode) {
                $loadingInRoundsValue = $this->armourer->getLoadingInRoundsByStrengthWithRangedWeapon(
                    $this->weaponlike,
                    $this->getStrengthForWeaponlike()
                );
            }

            $this->loadingInRounds = LoadingInRounds::getIt($loadingInRoundsValue);
        }

        return $this->loadingInRounds;
    }

    /**
     * Encounter range relates to weapon and strength for bows, speed for throwing weapons and nothing else for
     * crossbows. See PPH page 95 left column.
     * Melee weapons have encounter range zero.
     * Note about SPEAR: if current weapon for attack is spear for melee @see MeleeWeaponCode::SPEAR then range is zero.
     *
     * @return EncounterRange
     */
    public function getEncounterRange(): EncounterRange
    {
        if ($this->encounterRange === null) {
            $this->encounterRange = EncounterRange::getIt(
                $this->armourer->getEncounterRangeWithWeaponlike(
                    $this->weaponlike,
                    $this->getStrengthForWeaponlike(),
                    $this->bodyPropertiesForFight->getSpeed()
                )
            );
        }

        return $this->encounterRange;
    }

    /**
     * Ranged weapons can be used for indirect shooting and those have much longer maximal and still somehow
     * controllable (more or less - depends on weapon) range.
     * Others have their maximal (and still controllable) range same as encounter range.
     * See PPH page 104 left column.
     *
     * @return MaximalRange
     */
    public function getMaximalRange(): MaximalRange
    {
        if ($this->maximalRange === null) {
            if ($this->weaponlike instanceof RangedWeaponCode) {
                $this->maximalRange = MaximalRange::getItForRangedWeapon($this->getEncounterRange());
            } else {
                $this->maximalRange = MaximalRange::getItForMeleeWeapon($this->getEncounterRange()); // encounter = maximal for melee weapons
            }
        }

        return $this->maximalRange;
    }

    // DEFENSE

    /**
     * @return Defense
     */
    public function getDefense(): Defense
    {
        if ($this->defense === null) {
            $this->defense = Defense::getIt($this->bodyPropertiesForFight->getAgility());
        }

        return $this->defense;
    }

    /**
     * Your defense WITHOUT weapon and shield.
     * For standard defense @see getDefenseNumberWithShield and @see getDefenseNumberWithWeaponlike
     * Note: armor affects agility (can give restriction), but does NOT change defense number directly -
     * its protection is used after hit to lower final damage.
     *
     * @return DefenseNumber
     */
    public function getDefenseNumber(): DefenseNumber
    {
        if ($this->defenseNumber === null) {
            $baseDefenseNumber = DefenseNumber::getIt($this->getDefense());
            if ($this->enemyIsFasterThanYou) {
                // You CAN be affected by some of your actions because someone attacked you before you finished them.
                // Your defense WITHOUT weapon and shield.
                $defenseNumber = $baseDefenseNumber
                    ->add($this->combatActions->getDefenseNumberModifierAgainstFasterOpponent());
            } else {
                // You are NOT affected by any of your action just because someone attacked you before you are ready.
                $defenseNumber = $baseDefenseNumber
                    ->add($this->combatActions->getDefenseNumberModifier());
            }
            if (!$this->combatActions->usesSimplifiedLightingRules() && $this->glared->getCurrentMalus() < 0) {
                // see PPH page 129 top left
                $defenseNumber = $defenseNumber->add($this->glared->getCurrentMalus());
            }

            // ride skill
            if ($this->fightsOnHorseback) {
                $defenseNumber = $defenseNumber->add($this->skills->getMalusToAttackNumberWhenRiding());
            }

            $this->defenseNumber = $defenseNumber;
        }

        return $this->defenseNumber;
    }

    /**
     * You have to choose
     *  - if cover by shield (can twice per round even if already attacked)
     *  - or by weapon (can only once per round and only if you have attacked before defense or if you simply did not
     * used this weapon yet)
     *  - or just by a dodge (in that case use the pure @see getDefenseNumberModifier ).
     *
     * @return DefenseNumber
     */
    public function getDefenseNumberWithWeaponlike(): DefenseNumber
    {
        return $this->getDefenseNumber()->add($this->getCoverWithWeaponlike());
    }

    /**
     * @return int
     */
    private function getCoverWithWeaponlike(): int
    {
        $coverModifier = 0;

        //strength effect
        $coverModifier += $this->armourer->getDefenseNumberMalusByStrengthWithWeaponOrShield(
            $this->weaponlike,
            $this->getStrengthForWeaponlike()
        );

        // weapon or shield effect
        $coverModifier += $this->armourer->getCoverOfWeaponOrShield($this->weaponlike);

        // skill effect
        if ($this->weaponlike instanceof WeaponCode) {
            $coverModifier += $this->skills->getMalusToCoverWithWeapon(
                $this->weaponlike,
                $this->tables,
                $this->fightsWithTwoWeapons
            );
        } else { // even if you use shield as a weapon for attack, you are covering by it as a shield, of course
            $coverModifier += $this->skills->getMalusToCoverWithShield($this->tables);
        }
        if ($this->fightsFreeWillAnimal) {
            $coverModifier += $this->skills->getBonusToCoverAgainstFreeWillAnimal();
        }

        return $coverModifier;
    }

    /**
     * You have to choose
     *  - if cover by shield (can twice per round even if already attacked)
     *  - or by weapon (can only once per round and only if you have attacked before defense or if you simply did not
     * used this weapon yet)
     *  - or just by a dodge (in that case use the pure @see getDefenseNumber ).
     * Note about offhand - even shield is affected by lower strength of your offhand lower strength (-2).
     *
     * @return DefenseNumber
     */
    public function getDefenseNumberWithShield(): DefenseNumber
    {
        if ($this->defenseNumberWithShield === null) {
            $this->defenseNumberWithShield = $this->getDefenseNumber()->add($this->getCoverWithShield());
        }

        return $this->defenseNumberWithShield;
    }

    /**
     * @return int
     */
    private function getCoverWithShield(): int
    {
        $coverModifier = 0;

        //strength effect
        $coverModifier += $this->armourer->getDefenseNumberMalusByStrengthWithWeaponOrShield(
            $this->shield,
            $this->getStrengthForShield()
        );

        // weapon or shield effect
        $coverModifier += $this->armourer->getCoverOfWeaponOrShield($this->shield);

        // skill effect
        $coverModifier += $this->skills->getMalusToCoverWithShield($this->tables);
        if ($this->fightsFreeWillAnimal) {
            $coverModifier += $this->skills->getBonusToCoverAgainstFreeWillAnimal();
        }

        return $coverModifier;
    }

    // MOVEMENT

    /**
     * Note: without chosen movement action you are not moving at all, therefore moved distance is zero.
     *
     * @return Distance
     */
    public function getMovedDistance(): Distance
    {
        if ($this->movedDistance === null) {
            if ($this->combatActions->getSpeedModifier() === 0) {

                $this->movedDistance = new Distance(0, DistanceUnitCode::METER, $this->tables->getDistanceTable());
            } else {
                $speedInFight = $this->bodyPropertiesForFight->getSpeed()->add($this->combatActions->getSpeedModifier());
                $distanceBonus = new DistanceBonus($speedInFight->getValue(), $this->tables->getDistanceTable());
                $this->movedDistance = $distanceBonus->getDistance();
            }
        }

        return $this->movedDistance;
    }
}