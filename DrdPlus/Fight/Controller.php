<?php
namespace DrdPlus\Fight;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
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
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\FightProperties\BodyPropertiesForFight;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Health\Health;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Properties\Body\Height;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Properties\Derived\Speed;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Skills\Skills;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Strict\Object\StrictObject;
use Granam\String\StringTools;

class Controller extends StrictObject
{
    const DELETE_FIGHT_HISTORY = 'delete_fight_history';
    const FIGHT_HISTORY_TOKEN = 'fight_history_token';
    const FIGHT_HISTORY = 'fight_history';
    const RANKS_HISTORY = 'ranks_history';
    const REMEMBER = 'remember';
    const FORGOT_FIGHT = 'forgot_fight';
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
    const FIGHT_ANIMAL = 'fight_animal';
    const ZOOLOGY_SKILL_RANK = 'zoology_skill_rank';

    /**
     * @var array
     */
    private $history = [];

    public function __construct()
    {
        if (!empty($_POST[self::DELETE_FIGHT_HISTORY])) {
            $this->deleteHistory();
            header('Location: /', true, 301);
            exit;
        }
        $afterYear = (new \DateTime('+ 1 year'))->getTimestamp();
        if (!empty($_GET)) {
            if (!empty($_GET[self::REMEMBER])) {
                $this->setCookie(self::FORGOT_FIGHT, null, $afterYear);
                $this->setCookie(self::FIGHT_HISTORY, serialize($_GET), $afterYear);
                $this->setCookie(self::FIGHT_HISTORY_TOKEN, md5_file(__FILE__), $afterYear);
                $this->addSelectedSkillsToHistory($_GET);
            } else {
                $this->deleteHistory();
                $this->setCookie(self::FORGOT_FIGHT, 1, $afterYear);
            }
        } elseif (!$this->cookieHistoryIsValid()) {
            $this->deleteHistory();
        }
        if (!empty($_COOKIE[self::FIGHT_HISTORY])) {
            $this->history = unserialize($_COOKIE[self::FIGHT_HISTORY], ['allowed_classes' => []]);
            if (!is_array($this->history)) {
                $this->history = [];
            }
        }
    }

    private function deleteHistory()
    {
        $this->setCookie(self::FIGHT_HISTORY_TOKEN, null);
        $this->setCookie(self::FIGHT_HISTORY, null);
        $this->setCookie(self::RANKS_HISTORY, null);
    }

    private function setCookie(string $name, $value, int $expire = 0)
    {
        setcookie(
            $name,
            $value,
            $expire,
            '/',
            '',
            !empty($_SERVER['HTTPS']), // secure only ?
            true // http only
        );
        $_COOKIE[$name] = $value;
    }

    private function cookieHistoryIsValid(): bool
    {
        return !empty($_COOKIE[self::FIGHT_HISTORY_TOKEN]) && $_COOKIE[self::FIGHT_HISTORY_TOKEN] === md5_file(__FILE__);
    }

    private function addSelectedSkillsToHistory(array $request)
    {
        $skillsToSave = [];
        foreach ([self::MELEE_FIGHT_SKILL => self::MELEE_FIGHT_SKILL_RANK,
                     self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                     self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                 ] as $skillName => $rankName
        ) {
            if (array_key_exists($rankName, $request) && !empty($request[$skillName])) {
                // like melee_fight_skill => fight_unarmed => 0
                $skillsToSave[$rankName][$request[$skillName]] = $request[$rankName];
            }
        }
        if (count($skillsToSave) === 0) {
            return;
        }
        $ranksHistory = $this->getRanksHistory();
        foreach ($skillsToSave as $rankName => $rankValues) {
            // changed values are replaced because of string keys
            $ranksHistory[$rankName] = array_merge($ranksHistory[$rankName] ?? [], $rankValues);
        }
        $serialized = serialize($ranksHistory);
        $this->setCookie(self::RANKS_HISTORY, $serialized);
    }

    private function getRanksHistory(): array
    {
        return unserialize($_COOKIE[self::RANKS_HISTORY] ?? '', ['allowed_classes' => false]) ?: [];
    }

    public function shouldRemember(): bool
    {
        return empty($_COOKIE[self::FORGOT_FIGHT]);
    }

    public function getSelectedScrollFromTop(): int
    {
        return (int)$this->getValueFromRequest('scroll-from-top');
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
        $meleeWeaponValue = $this->getValueFromRequest(self::MELEE_WEAPON);
        if (!$meleeWeaponValue) {
            return MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        }

        return MeleeWeaponCode::getIt($meleeWeaponValue);
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getValueFromRequest(string $name)
    {
        if (array_key_exists($name, $_GET)) {
            return $_GET[$name];
        }
        if (array_key_exists($name, $this->history) && $this->cookieHistoryIsValid()) {
            return $this->history[$name];
        }

        return null;
    }

    /**
     * @return RangedWeaponCode
     */
    public function getSelectedRangedWeapon(): RangedWeaponCode
    {
        $rangedWeaponValue = $this->getValueFromRequest(self::RANGED_WEAPON);
        if (!$rangedWeaponValue) {
            return RangedWeaponCode::getIt(RangedWeaponCode::ROCK);
        }

        return RangedWeaponCode::getIt($rangedWeaponValue);
    }

    public function getSelectedStrength(): Strength
    {
        return Strength::getIt((int)$this->getValueFromRequest(self::STRENGTH));
    }

    public function getSelectedAgility(): Agility
    {
        return Agility::getIt((int)$this->getValueFromRequest(self::AGILITY));
    }

    public function getSelectedKnack(): Knack
    {
        return Knack::getIt((int)$this->getValueFromRequest(self::KNACK));
    }

    public function getSelectedWill(): Will
    {
        return Will::getIt((int)$this->getValueFromRequest(self::WILL));
    }

    public function getSelectedIntelligence(): Intelligence
    {
        return Intelligence::getIt((int)$this->getValueFromRequest(self::INTELLIGENCE));
    }

    public function getSelectedCharisma(): Charisma
    {
        return Charisma::getIt((int)$this->getValueFromRequest(self::CHARISMA));
    }

    public function getSelectedSize(): Size
    {
        return Size::getIt((int)$this->getValueFromRequest(self::SIZE));
    }

    public function getSelectedHeightInCm(): HeightInCm
    {
        return HeightInCm::getIt($this->getValueFromRequest(self::HEIGHT_IN_CM) ?? 150);
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
        ItemHoldingCode $weaponHolding,
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        ShieldCode $usedShield
    ): FightProperties
    {
        return new FightProperties(
            new BodyPropertiesForFight(
                $strength = $this->getSelectedStrength(),
                $agility = $this->getSelectedAgility(),
                $this->getSelectedKnack(),
                $this->getSelectedWill(),
                $this->getSelectedIntelligence(),
                $this->getSelectedCharisma(),
                $this->getSelectedSize(),
                $height = Height::getIt($this->getSelectedHeightInCm(), Tables::getIt()),
                Speed::getIt($strength, $agility, $height)
            ),
            new CombatActions([], Tables::getIt()),
            $this->createSkills($skillWithWeapon, $skillRankWithWeapon),
            $this->getSelectedBodyArmor(),
            $this->getSelectedHelm(),
            $this->getSelectedProfessionCode(),
            Tables::getIt(),
            $weaponlikeCode,
            $weaponHolding,
            false, // does not fight with two weapons
            $usedShield,
            false, // enemy is not faster
            Glared::createWithoutGlare(new Health())
        );
    }

    private function createSkills(SkillCode $skillWithWeapon, int $skillRankWithWeapon): Skills
    {
        $professionFirstLevel = ProfessionFirstLevel::createFirstLevel(
            Profession::getItByCode($this->getSelectedProfessionCode())
        );
        $skills = Skills::createSkills(
            new ProfessionLevels(
                ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
                $professionFirstLevel
            ),
            $skillPointsFromBackground = SkillPointsFromBackground::getIt(
                new PositiveIntegerObject(8), // just a maximum
                Ancestry::getIt(new PositiveIntegerObject(8), Tables::getIt()),
                Tables::getIt()
            ),
            new PhysicalSkills($professionFirstLevel),
            new PsychicalSkills($professionFirstLevel),
            new CombinedSkills($professionFirstLevel),
            Tables::getIt()
        );
        $this->addSkillWithWeapon(
            $skillWithWeapon,
            $skillRankWithWeapon,
            $skills,
            $professionFirstLevel,
            $skillPointsFromBackground
        );

        $skillRankWithArmor = $this->getSelectedArmorSkillRank();
        if ($skillRankWithArmor > 0) {
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $armorWearing = $skills->getPhysicalSkills()->getArmorWearing();
            while ($skillRankWithArmor-- > 0) {
                $armorWearing->increaseSkillRank($physicalSkillPoint);
            }
        }
        $selectedShieldSkillRank = $this->getSelectedShieldUsageSkillRank();
        if ($selectedShieldSkillRank > 0) {
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            $shieldUsage = $skills->getPhysicalSkills()->getShieldUsage();
            while ($selectedShieldSkillRank-- > 0) {
                $shieldUsage->increaseSkillRank($physicalSkillPoint);
            }
        }
        if ($this->fightAnimal()) {
            $zoologySkillRank = $this->getSelectedZoologySkillRank();
            if ($zoologySkillRank > 0) {
                $psychicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                    $professionFirstLevel,
                    $skillPointsFromBackground,
                    Tables::getIt()
                );
                $zoology = $skills->getPsychicalSkills()->getZoology();
                while ($skillRankWithArmor-- > 0) {
                    $zoology->increaseSkillRank($psychicalSkillPoint);
                }
            }
        }

        return $skills;
    }

    private function addSkillWithWeapon(
        SkillCode $skillWithWeapon,
        int $skillRankWithWeapon,
        Skills $skills,
        ProfessionFirstLevel $professionFirstLevel,
        SkillPointsFromBackground $skillPointsFromBackground
    )
    {
        if ($skillRankWithWeapon === 0) {
            return;
        }
        if (in_array($skillWithWeapon->getValue(), PhysicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PhysicalSkill $skill */
            $skill = $skills->getPhysicalSkills()->$getSkill();
            $physicalSkillPoint = PhysicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (in_array($skillWithWeapon->getValue(), PsychicalSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var PsychicalSkill $skill */
            $skill = $skills->getPsychicalSkills()->$getSkill();
            $physicalSkillPoint = PsychicalSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($physicalSkillPoint);
            }

            return;
        }
        if (in_array($skillWithWeapon->getValue(), CombinedSkillCode::getPossibleValues(), true)) {
            $getSkill = StringTools::assembleGetterForName($skillWithWeapon->getValue());
            /** @var CombinedSkill $skill */
            $skill = $skills->getCombinedSkills()->$getSkill();
            $combinedSkillPoint = CombinedSkillPoint::createFromFirstLevelSkillPointsFromBackground(
                $professionFirstLevel,
                $skillPointsFromBackground,
                Tables::getIt()
            );
            while ($skillRankWithWeapon-- > 0) {
                $skill->increaseSkillRank($combinedSkillPoint);
            }
        }
        throw new \LogicException("Given skill with a weapon '{$skillWithWeapon}' does not belong to any skill group");
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
        $meleeHolding = $this->getValueFromRequest(self::MELEE_WEAPON_HOLDING);
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
        return (int)$this->getValueFromRequest(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
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
        $meleeHolding = $this->getValueFromRequest(self::RANGED_WEAPON_HOLDING);
        if (!$meleeHolding) {
            return ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        }

        return ItemHoldingCode::getIt($meleeHolding);
    }

    public function getSelectedProfessionCode(): ProfessionCode
    {
        $professionName = $this->getValueFromRequest(self::PROFESSION);
        if (!$professionName) {
            return ProfessionCode::getIt(ProfessionCode::COMMONER);
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
        $skillValue = $this->getValueFromRequest($skillInputName);
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
        return (int)$this->getValueFromRequest(self::MELEE_FIGHT_SKILL_RANK);
    }

    public function getPreviousMeleeSkillRanksJson(): string
    {
        return $this->getPreviousSkillRanksJson(self::MELEE_FIGHT_SKILL_RANK);
    }

    private function getPreviousSkillRanksJson(string $skillRankInputName): string
    {
        return json_encode(
            $this->getRanksHistory()[$skillRankInputName] ?? [],
            JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE
        );
    }

    public function getPreviousRangedSkillRanksJson(): string
    {
        return $this->getPreviousSkillRanksJson(self::RANGED_FIGHT_SKILL_RANK);
    }

    public function getSelectedRangedSkillRank(): int
    {
        return (int)$this->getValueFromRequest(self::RANGED_FIGHT_SKILL_RANK);
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
        $shield = $this->getValueFromRequest(self::SHIELD);
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
        return (int)$this->getValueFromRequest(self::SHIELD_USAGE_SKILL_RANK);
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
        return (int)$this->getValueFromRequest(self::FIGHT_WITH_SHIELDS_SKILL_RANK);
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
        $shield = $this->getValueFromRequest(self::BODY_ARMOR);
        if (!$shield) {
            return BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR);
        }

        return BodyArmorCode::getIt($shield);
    }

    public function getSelectedArmorSkillRank(): int
    {
        return (int)$this->getValueFromRequest(self::ARMOR_SKILL_VALUE);
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
        $shield = $this->getValueFromRequest(self::HELM);
        if (!$shield) {
            return HelmCode::getIt(HelmCode::WITHOUT_HELM);
        }

        return HelmCode::getIt($shield);
    }

    public function fightAnimal(): bool
    {
        return (bool)$this->getValueFromRequest(self::FIGHT_ANIMAL);
    }

    public function getSelectedZoologySkillRank(): int
    {
        return (int)$this->getValueFromRequest(self::ZOOLOGY_SKILL_RANK);
    }
}