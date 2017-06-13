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
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class Controller extends StrictObject
{
    const REMEMBER = 'remember';
    const DELETE_FIGHT_HISTORY = 'delete_fight_history';

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
    const FIGHT_FREE_WILL_ANIMAL = 'fight_free_will_animal';
    const ZOOLOGY_SKILL_RANK = 'zoology_skill_rank';
    const SCROLL_FROM_TOP = 'scroll_from_top';

    /** @var History */
    private $history;
    /** @var CurrentValues */
    private $currentValues;
    /** @var PreviousValues */
    private $previousValues;

    public function __construct()
    {
        $this->history = new History(
            [
                self::MELEE_FIGHT_SKILL => self::MELEE_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
            ],
            !empty($_POST[self::DELETE_FIGHT_HISTORY]), // clear history?
            $_GET, // values to remember
            !empty($_GET[self::REMEMBER]) // should remember given values
        );
        $this->currentValues = new CurrentValues($_GET, $this->history);
        $this->previousValues = new PreviousValues($_GET);
    }

    public function shouldRemember(): bool
    {
        return $this->history->shouldRemember();
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
        return $this->getFightProperties(
            $this->getSelectedMeleeWeapon(),
            $this->getSelectedMeleeWeaponHolding(),
            $this->getSelectedMeleeSkillCode(),
            $this->getSelectedMeleeSkillRank(),
            $this->getSelectedShield()
        );
    }

    private function getFightProperties(
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
            $this->getSelectedFightFreeWillAnimal(),
            $this->getSelectedZoologySkillRank()
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
        return $this->currentValues->getFightProperties(
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
        if ($this->isTwoHandedOnly($this->getSelectedMeleeWeapon())) {
            return ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        }
        $meleeHolding = $this->currentValues->getValue(self::MELEE_WEAPON_HOLDING);
        if (!$meleeHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($meleeHolding);
    }

    public function getGenericFightProperties(): FightProperties
    {
        return $this->getFightProperties(
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
        return $this->getFightProperties(
            $this->getSelectedShield(),
            $this->getMeleeShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldSkillRank(),
            $this->getSelectedShield()
        );
    }

    public function getRangedShieldFightProperties(): FightProperties
    {
        return $this->getFightProperties(
            $this->getSelectedShield(),
            $this->getRangedShieldHolding(),
            PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS),
            $this->getSelectedFightWithShieldsSkillRank(),
            $this->getSelectedShield()
        );
    }

    /**
     * @return ItemHoldingCode
     * @throws \DrdPlus\Codes\Exceptions\ThereIsNoOppositeForTwoHandsHolding
     */
    public function getMeleeShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedMeleeWeaponHolding(), $this->getSelectedMeleeWeapon());
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
    public function getRangedShieldHolding(): ItemHoldingCode
    {
        return $this->getShieldHolding($this->getSelectedRangedWeaponHolding(), $this->getSelectedRangedWeapon());
    }

    private function getSelectedFightWithShieldSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
    }

    public function getRangedFightProperties(): FightProperties
    {
        return $this->getFightProperties(
            $this->getSelectedRangedWeapon(),
            $this->getSelectedRangedWeaponHolding(),
            $this->getSelectedRangedSkillCode(),
            $this->getSelectedRangedSkillRank(),
            $this->getSelectedShield()
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
        return $this->getSelectedSkill(self::MELEE_FIGHT_SKILL);
    }

    private function getSelectedSkill(string $skillInputName): SkillCode
    {
        $skillValue = $this->currentValues->getValue($skillInputName);
        if (!$skillValue) {
            return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_UNARMED);
        }

        if (in_array($skillValue, PhysicalSkillCode::getPossibleValues(), true)) {
            return PhysicalSkillCode::getIt($skillValue);
        }

        if (in_array($skillValue, PsychicalSkillCode::getPossibleValues(), true)) {
            return PsychicalSkillCode::getIt($skillValue);
        }
        if (in_array($skillValue, CombinedSkillCode::getPossibleValues(), true)) {
            return CombinedSkillCode::getIt($skillValue);
        }

        throw new \LogicException('Unexpected skill value ' . var_export($skillValue, true));
    }

    public function getSelectedRangedSkillCode(): SkillCode
    {
        return $this->getSelectedSkill(self::RANGED_FIGHT_SKILL);
    }

    public function getSelectedMeleeSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::MELEE_FIGHT_SKILL_RANK);
    }

    public function getPreviousMeleeSkillRanksJson(): string
    {
        return $this->arrayToJson($this->history->getPreviousSkillRanks(self::MELEE_FIGHT_SKILL_RANK));
    }

    private function arrayToJson(array $values): string
    {
        return json_encode($values, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }

    public function getPreviousRangedSkillRanksJson(): string
    {
        return $this->arrayToJson($this->history->getPreviousSkillRanks(self::RANGED_FIGHT_SKILL_RANK));
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->currentValues->getValue(self::RANGED_FIGHT_SKILL_RANK);
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

    public function getProtectionOfSelectedBodyArmor(): int
    {
        return Tables::getIt()->getBodyArmorsTable()->getProtectionOf($this->getSelectedBodyArmor());
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

    public function getProtectionOfSelectedHelm(): int
    {
        return Tables::getIt()->getHelmsTable()->getProtectionOf($this->getSelectedHelm());
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
        return (int)$this->currentValues->getValue(self::ZOOLOGY_SKILL_RANK);
    }
}