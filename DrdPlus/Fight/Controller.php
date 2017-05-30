<?php
namespace DrdPlus\Fight;

use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundParts\SkillsFromBackground;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\CombatActions\CombatActions;
use DrdPlus\FightProperties\BodyPropertiesForFight;
use DrdPlus\FightProperties\FightProperties;
use DrdPlus\Health\Health;
use DrdPlus\Health\Inflictions\Glared;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Skills\Skills;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Strict\Object\StrictObject;

class Controller extends StrictObject
{
    const HISTORY_TOKEN = 'historyToken';

    public function __construct()
    {
        $afterYear = (new \DateTime('+ 1 year'))->getTimestamp();
        foreach (['meleeWeapon', 'rangedWeapon'] as $name) {
            $this->setCookie($name, $_GET[$name] ?? null, $afterYear);
        }
        setcookie(self::HISTORY_TOKEN, md5(__FILE__), $afterYear);
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
    }

    /**
     * @return MeleeWeaponCode
     */
    public function getSelectedMeleeWeapon(): MeleeWeaponCode
    {
        $meleeWeaponValue = $this->getValueFromRequest('meleeWeapon');
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
        if (array_key_exists($name, $_COOKIE) && $this->cookieHistoryIsValid()) {
            return $_COOKIE[$name];
        }

        return null;
    }

    private function cookieHistoryIsValid(): bool
    {
        return !empty($_COOKIE['historyToken']) && $_COOKIE['historyToken'] === md5_file(__FILE__);
    }

    /**
     * @return RangedWeaponCode|null
     */
    public function getSelectedRangedWeapon()
    {
        $rangedWeaponValue = $this->getValueFromRequest('rangedWeapon');
        if (!$rangedWeaponValue) {
            return null;
        }

        return RangedWeaponCode::getIt($rangedWeaponValue);
    }

    public function getFightProperties(): FightProperties
    {
        return new FightProperties(
            new BodyPropertiesForFight(),
            new CombatActions([], Tables::getIt()),
            Skills::createSkills(
                new ProfessionLevels(
                    ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
                    $firstLevel = ProfessionFirstLevel::createFirstLevel(Commoner::getIt())
                ),
                SkillsFromBackground::getIt(
                    new PositiveIntegerObject(0),
                    Ancestry::getIt(new PositiveIntegerObject(0), Tables::getIt()),
                    Tables::getIt()
                ),
                new PhysicalSkills($firstLevel),
                new PsychicalSkills($firstLevel),
                new CombinedSkills($firstLevel),
                Tables::getIt()
            ),
            BodyArmorCode::getIt(BodyArmorCode::WITHOUT_ARMOR),
            HelmCode::getIt(HelmCode::WITHOUT_HELM),
            ProfessionCode::getIt(ProfessionCode::COMMONER),
            Tables::getIt(),
            $this->getSelectedMeleeWeapon(),
            ItemHoldingCode::getIt(
                Tables::getIt()->getArmourer()->isTwoHandedOnly($this->getSelectedMeleeWeapon())
                    ? ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS)
                    : ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND)
            ),
            false, // does not fight with two weapons
            ShieldCode::getIt(ShieldCode::WITHOUT_SHIELD),
            false, // enemy is not faster
            Glared::createWithoutGlare(new Health())
        );
    }
}