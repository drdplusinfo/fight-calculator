<?php
namespace DrdPlus\Fight;

use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Configurator\Skeleton\History;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;

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
    const RANGED_TARGET_DISTANCE = 'ranged_target_distance';
    const RANGED_TARGET_SIZE = 'ranged_target_size';
    // special actions
    const ACTION = 'action';
    const ADD_NEW_MELEE_WEAPON = 'add_new_melee_weapon';
    const ADD_NEW_RANGED_WEAPON = 'add_new_ranged_weapon';
    const ADD_NEW_BODY_ARMOR = 'add_new_body_armor';

    /** @var CurrentValues */
    private $currentValues;
    /** @var CurrentProperties */
    private $currentProperties;
    /** @var Fight */
    private $fight;
    /** @var array|string[] */
    private $messagesAbout = [];

    public function __construct()
    {
        parent::__construct('fight' /* cookies postfix */);
        $this->currentValues = new CurrentValues($_GET, $this->getHistoryWithSkillRanks());
        $this->currentProperties = new CurrentProperties($this->currentValues);
        $this->fight = new Fight(
            $this->currentValues,
            $this->currentProperties,
            $this->getHistoryWithSkillRanks(),
            new PreviousProperties($this->getHistoryWithSkillRanks()),
            new CustomWeaponsService()
        );
    }

    protected function createHistory(string $cookiesPostfix, int $cookiesTtl = null): History
    {
        return new PreviousValues(
            [
                self::MELEE_FIGHT_SKILL => self::MELEE_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
                self::RANGED_FIGHT_SKILL => self::RANGED_FIGHT_SKILL_RANK,
            ],
            $this->shouldDeleteHistory(),
            $_GET, // values to remember
            !empty($_GET[self::REMEMBER_HISTORY]), // should remember given values
            $cookiesPostfix,
            $cookiesTtl
        );
    }

    private function shouldDeleteHistory(): bool
    {
        return !empty($_POST[self::DELETE_HISTORY]);
    }

    /**
     * @return CurrentValues
     */
    public function getCurrentValues(): CurrentValues
    {
        return $this->currentValues;
    }

    /**
     * @return Fight
     */
    public function getFight(): Fight
    {
        return $this->fight;
    }

    /**
     * @return PreviousValues|History
     */
    private function getHistoryWithSkillRanks(): PreviousValues
    {
        return $this->getHistory();
    }

    /**
     * @return CurrentProperties
     */
    public function getCurrentProperties(): CurrentProperties
    {
        return $this->currentProperties;
    }

    public function shouldRemember(): bool
    {
        return $this->getHistory()->shouldRemember();
    }

    public function getScrollFromTop(): int
    {
        return (int)$this->currentValues->getValue(self::SCROLL_FROM_TOP);
    }

    /**
     * @return array|MeleeWeaponCode[][][]
     */
    public function getMeleeWeapons(): array
    {
        $weaponCodes = $this->fight->getPossibleMeleeWeapons();
        $countOfUnusable = 0;
        foreach ($weaponCodes as $weaponCodesOfSameCategory) {
            $countOfUnusable += $this->countUnusable($weaponCodesOfSameCategory);
        }
        if ($countOfUnusable > 0) {
            $weaponWord = 'zbraň';
            if ($countOfUnusable >= 5) {
                $weaponWord = 'zbraní';
            } elseif ($countOfUnusable >= 2) {
                $weaponWord = 'zbraně';
            }
            $this->messagesAbout['melee']['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $weaponWord na blízko.";
        }

        return $weaponCodes;
    }

    /**
     * @param array|bool[][] $items
     * @return int
     */
    private function countUnusable(array $items): int
    {
        $count = 0;
        foreach ($items as $item) {
            if (!$item['canUseIt']) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array|RangedWeaponCode[][][]
     */
    public function getRangedWeapons(): array
    {
        $weaponCodes = $this->fight->getPossibleRangedWeapons();
        $countOfUnusable = 0;
        foreach ($weaponCodes as $weaponCodesOfSameCategory) {
            $countOfUnusable += $this->countUnusable($weaponCodesOfSameCategory);
        }
        if ($countOfUnusable > 0) {
            $weaponWord = 'zbraň';
            if ($countOfUnusable >= 5) {
                $weaponWord = 'zbraní';
            } elseif ($countOfUnusable >= 2) {
                $weaponWord = 'zbraně';
            }
            $this->messagesAbout['ranged']['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $weaponWord na dálku.";
        }

        return $weaponCodes;
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

    /**
     * @return array|ShieldCode[][][]
     */
    public function getShields(): array
    {
        $shieldCodes = $this->fight->getPossibleShields();
        $countOfUnusable = $this->countUnusable($shieldCodes);
        if ($countOfUnusable > 0) {
            $shieldWord = 'štít';
            if ($countOfUnusable >= 5) {
                $shieldWord = 'štítů';
            } elseif ($countOfUnusable >= 2) {
                $shieldWord = 'štíty';
            }
            $this->messagesAbout['shields']['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $shieldWord.";
        }

        return $shieldCodes;
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getShieldUsageSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::SHIELD_USAGE);
    }

    /**
     * @return PhysicalSkillCode
     */
    public function getFightWithShieldsSkillCode(): PhysicalSkillCode
    {
        return PhysicalSkillCode::getIt(PhysicalSkillCode::FIGHT_WITH_SHIELDS);
    }

    /**
     * @return array
     */
    public function getBodyArmors(): array
    {
        $bodyArmors = $this->fight->getPossibleBodyArmors();
        $countOfUnusable = $this->countUnusable($bodyArmors);
        if ($countOfUnusable > 0) {
            $armorWord = 'zbroj';
            if ($countOfUnusable >= 5) {
                $armorWord = 'zbrojí';
            } elseif ($countOfUnusable >= 2) {
                $armorWord = 'zbroje';
            }
            $this->messagesAbout['armors']['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $armorWord.";
        }

        return $bodyArmors;
    }

    /**
     * @return array|HelmCode[][]
     */
    public function getHelms(): array
    {
        $helmCodes = $this->fight->getPossibleHelms();
        $countOfUnusable = $this->countUnusable($helmCodes);
        $this->addUnusableMessage($countOfUnusable, 'helms', 'helmu', 'helmy', 'helem');

        return $helmCodes;
    }

    private function addUnusableMessage(int $countOfUnusable, string $key, string $single, string $few, string $many)
    {
        if ($countOfUnusable > 0) {
            $word = $single;
            if ($countOfUnusable >= 5) {
                $word = $many;
            } elseif ($countOfUnusable >= 2) {
                $word = $few;
            }

            $this->messagesAbout[$key]['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $word.";
        }
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

    public function getMessagesAboutMelee(): array
    {
        return $this->messagesAbout['melee'] ?? [];
    }

    public function getMessagesAboutRanged(): array
    {
        return $this->messagesAbout['ranged'] ?? [];
    }

    public function getMessagesAboutShields(): array
    {
        return $this->messagesAbout['shields'] ?? [];
    }

    public function getMessagesAboutHelms(): array
    {
        return $this->messagesAbout['helms'] ?? [];
    }

    public function getMessagesAboutArmors(): array
    {
        return $this->messagesAbout['armors'] ?? [];
    }

    public function getCurrentUrlWithQuery(array $additionalParameters = []): string
    {
        /** @var array $parameters */
        $parameters = $_GET;
        if ($additionalParameters) {
            foreach ($additionalParameters as $name => $value) {
                $parameters[$name] = $value;
            }
        }
        $queryParts = [];
        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                /** @var array $value */
                foreach ($value as $index => $item) {
                    $queryParts[] = urlencode("{$name}[{$index}]") . '=' . urlencode($item);
                }
            } else {
                $queryParts[] = urlencode($name) . '=' . urlencode($value);
            }
        }
        $query = '';
        if ($queryParts) {
            $query = '?' . implode('&', $queryParts);
        }

        return $query;
    }

    public function addingNewMeleeWeapon(): bool
    {
        return $this->currentValues->getCurrentValue(self::ACTION) === self::ADD_NEW_MELEE_WEAPON;
    }

    public function addingNewRangedWeapon(): bool
    {
        return $this->currentValues->getCurrentValue(self::ACTION) === self::ADD_NEW_RANGED_WEAPON;
    }

    public function addingNewBodyArmor(): bool
    {
        return $this->currentValues->getCurrentValue(self::ACTION) === self::ADD_NEW_BODY_ARMOR;
    }
}