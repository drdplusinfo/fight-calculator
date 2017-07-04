<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\Configurator\Skeleton\History;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\String\StringTools;

class Controller extends \DrdPlus\Configurator\Skeleton\Controller
{
    const MELEE_WEAPON = 'melee_weapon';
    const RANGED_WEAPON = 'ranged_weapon';
    const STRENGTH = PropertyCode::STRENGTH;
    const AGILITY = PropertyCode::AGILITY;
    const KNACK = PropertyCode::KNACK;
    const WILL = PropertyCode::WILL;
    const INTELLIGENCE = PropertyCode::INTELLIGENCE;
    const CHARISMA = PropertyCode::CHARISMA;
    const SIZE = PropertyCode::SIZE;
    const HEIGHT_IN_CM = PropertyCode::HEIGHT_IN_CM;
    const MELEE_WEAPON_HOLDING = 'melee_weapon_holding';
    const RANGED_WEAPON_HOLDING = 'ranged_weapon_holding';
    const PROFESSION = 'profession';
    const MELEE_FIGHT_SKILL = 'melee_fight_skill';
    const MELEE_FIGHT_SKILL_RANK = 'melee_fight_skill_rank';
    const RANGED_FIGHT_SKILL = 'ranged_fight_skill';
    const RANGED_FIGHT_SKILL_RANK = 'ranged_fight_skill_rank';
    const SHIELD = 'shield';
    const SHIELD_USAGE_SKILL_RANK = 'shield_usage_skill_rank';
    const FIGHT_WITH_SHIELDS_SKILL_RANK = 'fight_with_shields_skill_rank';
    const BODY_ARMOR = 'body_armor';
    const ARMOR_SKILL_VALUE = 'armor_skill_value';
    const HELM = 'helm';
    const ON_HORSEBACK = 'on_horseback';
    const RIDING_SKILL_RANK = 'riding_skill_rank';
    const FIGHT_FREE_WILL_ANIMAL = 'fight_free_will_animal';
    const ZOOLOGY_SKILL_RANK = 'zoology_skill_rank';
    const SCROLL_FROM_TOP = 'scroll_from_top';

    /** @var CurrentValues */
    private $currentValues;
    /** @var PreviousValues */
    private $previousValues;

    public function __construct()
    {
        parent::__construct('fight' /* cookies postfix */);
        $this->currentValues = new CurrentValues($_GET, $this->getHistoryWithSkillRanks());
        $this->previousValues = new PreviousValues($_GET);
    }

    protected function createHistory(string $cookiesPostfix, int $cookiesTtl = null): History
    {
        return new HistoryWithSkillRanks(
            [
                self::MELEE_FIGHT_SKILL => self::MELEE_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
            ],
            !empty($_POST[self::DELETE_HISTORY]), // clear history?
            $_GET, // values to remember
            !empty($_GET[self::REMEMBER_HISTORY]), // should remember given values
            $cookiesPostfix,
            $cookiesTtl
        );
    }

    private function getHistoryWithSkillRanks(): HistoryWithSkillRanks
    {
        return $this->getHistory();
    }

    public function shouldRemember(): bool
    {
        return $this->getHistory()->shouldRemember();
    }

    public function getSelectedScrollFromTop(): int
    {
        return (int)$this->currentValues->getValue(self::SCROLL_FROM_TOP);
    }

    public function getMeleeWeaponCodes(): array
    {
        return [
            WeaponCategoryCode::AXE => MeleeWeaponCode::getAxeCodes(),
            WeaponCategoryCode::KNIFE_AND_DAGGER => MeleeWeaponCode::getKnifeAndDaggerCodes(),
            WeaponCategoryCode::MACE_AND_CLUB => MeleeWeaponCode::getMaceAndClubCodes(),
            WeaponCategoryCode::MORNINGSTAR_AND_MORGENSTERN => MeleeWeaponCode::getMorningstarAndMorgensternCodes(),
            WeaponCategoryCode::SABER_AND_BOWIE_KNIFE => MeleeWeaponCode::getSaberAndBowieKnifeCodes(),
            WeaponCategoryCode::STAFF_AND_SPEAR => MeleeWeaponCode::getStaffAndSpearCodes(),
            WeaponCategoryCode::SWORD => MeleeWeaponCode::getSwordCodes(),
            WeaponCategoryCode::VOULGE_AND_TRIDENT => MeleeWeaponCode::getVoulgeAndTridentCodes(),
            WeaponCategoryCode::UNARMED => MeleeWeaponCode::getUnarmedCodes(),
        ];
    }

    public function getRangedWeaponCodes(): array
    {
        return [
            WeaponCategoryCode::THROWING_WEAPON => RangedWeaponCode::getThrowingWeaponValues(),
            WeaponCategoryCode::BOW => RangedWeaponCode::getBowValues(),
            WeaponCategoryCode::CROSSBOW => RangedWeaponCode::getCrossbowValues(),
        ];
    }

    /**
     * @return MeleeWeaponCode
     */
    public function getSelectedMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->currentValues->getValue(self::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return MeleeWeaponCode::getIt($meleeWeaponValue);
    }

    /**
     * @return MeleeWeaponCode
     */
    public function getPreviousMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->previousValues->getValue(self::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return $this->getSelectedMeleeWeapon();
        }

        return MeleeWeaponCode::getIt($meleeWeaponValue);
    }

    /**
     * @return RangedWeaponCode
     */
    public function getSelectedRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->currentValues->getValue(self::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::ROCK);
        }

        return RangedWeaponCode::getIt($rangedWeaponValue);
    }

    /**
     * @return RangedWeaponCode
     */
    public function getPreviousRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->previousValues->getValue(self::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return $this->getSelectedRangedWeapon();
        }

        return RangedWeaponCode::getIt($rangedWeaponValue);
    }

    public function getSelectedStrength(): Strength
    {
        return Strength::getIt((int)$this->currentValues->getValue(self::STRENGTH));
    }

    public function getPreviousStrength(): Strength
    {
        return Strength::getIt((int)$this->previousValues->getValue(self::STRENGTH));
    }

    public function getSelectedAgility(): Agility
    {
        return Agility::getIt((int)$this->currentValues->getValue(self::AGILITY));
    }

    public function getPreviousAgility(): Agility
    {
        return Agility::getIt((int)$this->previousValues->getValue(self::AGILITY));
    }

    public function getSelectedKnack(): Knack
    {
        return Knack::getIt((int)$this->currentValues->getValue(self::KNACK));
    }

    public function getPreviousKnack(): Knack
    {
        return Knack::getIt((int)$this->previousValues->getValue(self::KNACK));
    }

    public function getSelectedWill(): Will
    {
        return Will::getIt((int)$this->currentValues->getValue(self::WILL));
    }

    public function getPreviousWill(): Will
    {
        return Will::getIt((int)$this->previousValues->getValue(self::WILL));
    }

    public function getSelectedIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->currentValues->getValue(self::INTELLIGENCE));
    }

    public function getPreviousIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->previousValues->getValue(self::INTELLIGENCE));
    }

    public function getSelectedCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->currentValues->getValue(self::CHARISMA));
    }

    public function getPreviousCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->previousValues->getValue(self::CHARISMA));
    }

    public function getSelectedSize(): Size
    {
        return Size::getIt((int)$this->currentValues->getValue(self::SIZE));
    }

    public function getPreviousSize(): Size
    {
        return Size::getIt((int)$this->previousValues->getValue(self::SIZE));
    }

    public function getSelectedHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->currentValues->getValue(self::HEIGHT_IN_CM) ?? 150);
    }

    public function getPreviousHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->previousValues->getValue(self::HEIGHT_IN_CM) ?? 150);
    }

    public function getMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedMeleeWeapon(),
            $this->getSelectedMeleeWeaponHolding(),
            $this->getSelectedMeleeSkillCode(),
            $this->getSelectedMeleeSkillRank(),
            $this->getSelectedShield()
        );
    }

    private function getCurrentFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRank,
        ShieldCode $shieldCode
    ): FightProperties
    {
        return $this->currentValues->getFightProperties(
            $this->getSelectedStrength(),
            $this->getSelectedAgility(),
            $this->getSelectedKnack(),
            $this->getSelectedWill(),
            $this->getSelectedIntelligence(),
            $this->getSelectedCharisma(),
            $this->getSelectedSize(),
            $this->getSelectedHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRank,
            $shieldCode,
            $this->getSelectedShieldUsageSkillRank(),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getSelectedArmorSkillRank(),
            $this->getSelectedProfessionCode(),
            $this->getSelectedOnHorseback(),
            $this->getSelectedRidingSkillRank(),
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
        );
    }

    public function getPreviousMeleeWeaponFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousMeleeWeapon(),
            $this->getPreviousMeleeWeaponHolding(),
            $this->getPreviousMeleeSkillCode(),
            $this->getPreviousMeleeSkillRank(),
            $this->getPreviousShield()
        );
    }

    private function getPreviousFightProperties(
        WeaponlikeCode $weaponlikeCode,
        ItemHoldingCode $weaponHoldingCode,
        SkillCode $fightWithWeaponSkillCode,
        int $skillRank,
        ShieldCode $shieldCode
    ): FightProperties
    {
        return $this->previousValues->getFightProperties(
            $this->getPreviousStrength(),
            $this->getPreviousAgility(),
            $this->getPreviousKnack(),
            $this->getPreviousWill(),
            $this->getPreviousIntelligence(),
            $this->getPreviousCharisma(),
            $this->getPreviousSize(),
            $this->getPreviousHeightInCm(),
            $weaponlikeCode,
            $weaponHoldingCode,
            $fightWithWeaponSkillCode,
            $skillRank,
            $shieldCode,
            $this->getPreviousShieldUsageSkillRank(),
            $this->getPreviousBodyArmor(),
            $this->getPreviousHelm(),
            $this->getPreviousArmorSkillRank(),
            $this->getPreviousProfessionCode(),
            $this->getPreviousOnHorseback(),
            $this->getPreviousRidingSkillRank(),
            $this->getPreviousFightFreeWillAnimal(),
            $this->getPreviousZoologySkillRank()
        );
    }

    public function isTwoHandedOnly(WeaponCode $weaponCode): bool
    {
        return Tables::getIt()->getArmourer()->isTwoHandedOnly($weaponCode);
    }

    public function getSelectedMeleeWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getSelectedMeleeWeapon(),
            $this->currentValues->getValue(self::MELEE_WEAPON_HOLDING)
        );
    }

    private function getWeaponHolding(WeaponCode $weaponCode, $weaponHolding): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($weaponCode)) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        if (!$weaponHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($weaponHolding);
    }

    public function getPreviousMeleeWeaponHolding(): ItemHoldingCode
    {
        return $this->getWeaponHolding(
            $this->getPreviousMeleeWeapon(),
            $this->previousValues->getValue(self::MELEE_WEAPON_HOLDING)
        );
    }

    public function getGenericFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
        );
    }

    public function getPreviousGenericFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            MeleeWeaponCode::getIt(MeleeWeaponCode::HAND),
            ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND),
            PsychicalSkillCode::getIt(PsychicalSkillCode::ASTRONOMY), // whatever
            0, // zero skill rank
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD)
        );
    }

    public function getMeleeShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShield(),
            $this->getCurrentMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousMeleeShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield()
        );
    }

    public function getRangedShieldFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedShield(),
            $this->getCurrentRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousRangedShieldFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousShield(),
            $this->getPreviousRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getPreviousFightWithShieldsSkillRank(),
            $this->getPreviousShield()
        );
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentMeleeShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedMeleeWeaponHolding(), $this->getSelectedMeleeWeapon());
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousMeleeShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getPreviousMeleeWeaponHolding(), $this->getPreviousMeleeWeapon());
    }

    /**
     * @param ItemHoldingCode $weaponHolding
     * @param WeaponlikeCode $weaponlikeCode
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    private function getShieldHolding(ItemHoldingCode $weaponHolding, WeaponlikeCode $weaponlikeCode): ItemHoldingCode
    {
        if ($weaponHolding->holdsByTwoHands()) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            if (Tables::getIt()->getArmourer()->canHoldItByTwoHands($this->getSelectedShield())) {
                // because two-handed weapon has to be dropped to use shield and then both hands can be used for shield
                return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
            }

            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if ($weaponlikeCode->isUnarmed() && Tables::getIt()->getArmourer()->canHoldItByTwoHands($this->getSelectedShield())) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }

        return $weaponHolding->getOpposite();
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getCurrentRangedShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedRangedWeaponHolding(), $this->getSelectedRangedWeapon());
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getPreviousRangedShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getPreviousRangedWeaponHolding(), $this->getPreviousRangedWeapon());
    }

    public function getRangedFightProperties(): FightProperties
    {
        return $this->getCurrentFightProperties(
            $this->getSelectedRangedWeapon(),
            $this->getSelectedRangedWeaponHolding(),
            $this->getSelectedRangedSkillCode(),
            $this->getSelectedRangedSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getPreviousRangedFightProperties(): FightProperties
    {
        return $this->getPreviousFightProperties(
            $this->getPreviousRangedWeapon(),
            $this->getPreviousRangedWeaponHolding(),
            $this->getPreviousRangedSkillCode(),
            $this->getPreviousRangedSkillRank(),
            $this->getPreviousShield()
        );
    }

    public function getSelectedRangedWeaponHolding(): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($this->getSelectedRangedWeapon())) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        $meleeHolding = $this->currentValues->getValue(self::RANGED_WEAPON_HOLDING);
        if (!$meleeHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($meleeHolding);
    }

    public function getPreviousRangedWeaponHolding(): ItemHoldingCode
    {
        if ($this->isTwoHandedOnly($this->getPreviousRangedWeapon())) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        $meleeHolding = $this->previousValues->getValue(self::RANGED_WEAPON_HOLDING);
        if (!$meleeHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($meleeHolding);
    }

    public function getSelectedProfessionCode(): ProfessionCode
    {
        $professionName = $this->currentValues->getValue(self::PROFESSION);
        if (!$professionName) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
        }

        return ProfessionCode::getIt($professionName);
    }

    public function getPreviousProfessionCode(): ProfessionCode
    {
        $professionName = $this->previousValues->getValue(self::PROFESSION);
        if (!$professionName) {
            return $this->getSelectedProfessionCode();
        }

        return ProfessionCode::getIt($professionName);
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleSkillsForMelee(): array
    {
        return $this->getPossibleSkillsForCategories(WeaponCategoryCode::getMeleeWeaponCategoryValues());
    }

    /**
     * @param array|string $weaponCategoryValues
     * @return array|SkillCode[]
     */
    private function getPossibleSkillsForCategories(array $weaponCategoryValues): array
    {
        $fightWithCategories = [];
        $fightWithPhysical = array_map(
            function (string $skillName) {
                return PhysicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PhysicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithPhysical);
        $fightWithPsychical = array_map(
            function (string $skillName) {
                return PsychicalSkillCode::getIt($skillName);
            },
            $this->filterForCategories(PsychicalSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithPsychical);
        $fightWithCombined = array_map(
            function (string $skillName) {
                return CombinedSkillCode::getIt($skillName);
            },
            $this->filterForCategories(CombinedSkillCode::getPossibleValues(), $weaponCategoryValues)
        );
        $fightWithCategories = array_merge($fightWithCategories, $fightWithCombined);

        return $fightWithCategories;
    }

    private function filterForCategories(array $skillCodeValues, array $weaponCategoryValues): array
    {
        $fightWith = array_filter(
            $skillCodeValues,
            function (string $skillName) {
                return strpos($skillName, 'fight_') === 0;
            }
        );
        $categoryNames = array_map(
            function (string $categoryName) {
                return StringTools::toConstant(WeaponCategoryCode::getIt($categoryName)->translateTo('en', 4));
            },
            $weaponCategoryValues
        );

        return array_filter($fightWith, function (string $skillName) use ($categoryNames) {
            $categoryFromSkill = str_replace(['fight_with_', 'fight_' /*without weapon */], '', $skillName);

            return in_array($categoryFromSkill, $categoryNames, true);
        });
    }

    /**
     * @return array|SkillCode[]
     */
    public function getPossibleSkillsForRanged(): array
    {
        return $this->getPossibleSkillsForCategories(WeaponCategoryCode::getRangedWeaponCategoryValues());
    }

    public function getSelectedMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(self::MELEE_FIGHT_SKILL));
    }

    private function getSelectedSkill($skillName): SkillCode
    {
        if (!$skillName) {
            return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED);
        }

        if (in_array($skillName, PhysicalSkillCode::getPossibleValues(), true)) {
            return PhysicalSkillCode::getIt($skillName);
        }

        if (in_array($skillName, PsychicalSkillCode::getPossibleValues(), true)) {
            return PsychicalSkillCode::getIt($skillName);
        }
        if (in_array($skillName, CombinedSkillCode::getPossibleValues(), true)) {
            return CombinedSkillCode::getIt($skillName);
        }

        throw new \LogicException('Unexpected skill value ' . var_export($skillName, true));
    }

    public function getPreviousMeleeSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(self::MELEE_FIGHT_SKILL));
    }

    public function getSelectedRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->currentValues->getValue(self::RANGED_FIGHT_SKILL));
    }

    public function getPreviousRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill($this->previousValues->getValue(self::RANGED_FIGHT_SKILL));
    }

    public function getSelectedMeleeSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::MELEE_FIGHT_SKILL_RANK);
    }

    public function getPreviousMeleeSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::MELEE_FIGHT_SKILL_RANK);
    }

    public function getHistoryMeleeSkillRanksJson(): string
    {
        return $this->arrayToJson($this->getHistoryWithSkillRanks()->getPreviousSkillRanks(self::MELEE_FIGHT_SKILL_RANK));
    }

    private function arrayToJson(array $values): string
    {
        return json_encode($values, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }

    public function getHistoryRangedSkillRanksJson(): string
    {
        return $this->arrayToJson($this->getHistoryWithSkillRanks()->getPreviousSkillRanks(self::RANGED_FIGHT_SKILL_RANK));
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::RANGED_FIGHT_SKILL_RANK);
    }

    public function getPreviousRangedSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::RANGED_FIGHT_SKILL_RANK);
    }

    /**
     * @return array|ShieldCode[]
     */
    public function getPossibleShields(): array
    {
        return array_map(function (string $armorValue) {
            return ShieldCode::getIt($armorValue);
        }, ShieldCode::getPossibleValues());
    }

    public function getSelectedShield(): ShieldCode
    {
        $shield = $this->currentValues->getValue(self::SHIELD);
        if (!$shield) {
            return ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD);
        }

        return ShieldCode::getIt($shield);
    }

    public function getPreviousShield(): ShieldCode
    {
        $shield = $this->previousValues->getValue(self::SHIELD);
        if (!$shield) {
            return $this->getSelectedShield();
        }

        return ShieldCode::getIt($shield);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getShieldUsageSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::SHIELD_USAGE);
    }

    public function getSelectedShieldUsageSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::SHIELD_USAGE_SKILL_RANK);
    }

    public function getPreviousShieldUsageSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::SHIELD_USAGE_SKILL_RANK);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getFightWithShieldsSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS);
    }

    public function getSelectedFightWithShieldsSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getPreviousFightWithShieldsSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    /**
     * @return array|BodyArmorCode[]
     */
    public function getPossibleBodyArmors(): array
    {
        return array_map(function (string $armorValue) {
            return BodyArmorCode::getIt($armorValue);
        }, BodyArmorCode::getPossibleValues());
    }

    public function getSelectedBodyArmor(): BodyArmorCode
    {
        $shield = $this->currentValues->getValue(self::BODY_ARMOR);
        if (!$shield) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($shield);
    }

    public function getProtectionOfBodyArmor(BodyArmorCode $bodyArmorCode): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($bodyArmorCode);
    }

    public function getProtectionOfSelectedBodyArmor(): int
    {
        return $this->getProtectionOfBodyArmor($this->getSelectedBodyArmor());
    }

    public function getProtectionOfPreviousBodyArmor(): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($this->getPreviousBodyArmor());
    }

    public function getPreviousBodyArmor(): BodyArmorCode
    {
        $shield = $this->previousValues->getValue(self::BODY_ARMOR);
        if (!$shield) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($shield);
    }

    public function getSelectedArmorSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::ARMOR_SKILL_VALUE);
    }

    public function getPreviousArmorSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::ARMOR_SKILL_VALUE);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getPossibleSkillForArmor(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::ARMOR_WEARING);
    }

    /**
     * @return array|HelmCode[]
     */
    public function getPossibleHelms(): array
    {
        return array_map(function (string $helmValue) {
            return HelmCode::getIt($helmValue);
        }, HelmCode::getPossibleValues());
    }

    public function getSelectedHelm(): HelmCode
    {
        $shield = $this->currentValues->getValue(self::HELM);
        if (!$shield) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($shield);
    }

    public function getProtectionOfHelm(HelmCode $helmCode): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($helmCode);
    }

    public function getProtectionOfSelectedHelm(): int
    {
        return $this->getProtectionOfHelm($this->getSelectedHelm());
    }

    public function getPreviousHelm(): HelmCode
    {
        $shield = $this->previousValues->getValue(self::HELM);
        if (!$shield) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($shield);
    }

    public function getProtectionOfPreviousHelm(): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($this->getPreviousHelm());
    }

    public function getSelectedOnHorseback(): bool
    {
        return (bool)$this->currentValues->getValue(self::ON_HORSEBACK);
    }

    public function getPreviousOnHorseback(): bool
    {
        return (bool)$this->previousValues->getValue(self::ON_HORSEBACK);
    }

    public function getSelectedRidingSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::RIDING_SKILL_RANK);
    }

    public function getPreviousRidingSkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::RIDING_SKILL_RANK);
    }

    public function getSelectedFightFreeWillAnimal(): bool
    {
        return (bool)$this->currentValues->getValue(self::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getPreviousFightFreeWillAnimal(): bool
    {
        return (bool)$this->previousValues->getValue(self::FIGHT_FREE_WILL_ANIMAL);
    }

    public function getSelectedZoologySkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::ZOOLOGY_SKILL_RANK);
    }

    public function getPreviousZoologySkillRank(): int
    {
        return (int)$this->previousValues->getValue(self::ZOOLOGY_SKILL_RANK);
    }

    /**
     * @param int|IntegerInterface $previous
     * @param int|IntegerInterface $current
     * @return string
     */
    public function getClassForChangedValue($previous, $current): string
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if (ToInteger::toInteger($previous) < ToInteger::toInteger($current)) {
            return 'increased';
        }
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        if (ToInteger::toInteger($previous) > ToInteger::toInteger($current)) {
            return 'decreased';
        }

        return '';
    }
}