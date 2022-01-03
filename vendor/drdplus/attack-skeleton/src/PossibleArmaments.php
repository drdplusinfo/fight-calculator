<?php declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\Armaments\HelmCode;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCategoryCode;
use DrdPlus\Codes\ItemHoldingCode;
use Granam\Strict\Object\StrictObject;

class PossibleArmaments extends StrictObject
{
    use UsingArmaments;

    private \DrdPlus\Armourer\Armourer $armourer;
    private \DrdPlus\AttackSkeleton\CurrentProperties $currentProperties;
    private \DrdPlus\Codes\ItemHoldingCode $currentMeleeWeaponHolding;
    private \DrdPlus\Codes\ItemHoldingCode $currentRangedWeaponHolding;

    public function __construct(
        Armourer $armourer,
        CurrentProperties $currentProperties,
        ItemHoldingCode $currentMeleeWeaponHolding,
        ItemHoldingCode $currentRangedWeaponHolding
    )
    {
        $this->armourer = $armourer;
        $this->currentProperties = $currentProperties;
        $this->currentMeleeWeaponHolding = $currentMeleeWeaponHolding;
        $this->currentRangedWeaponHolding = $currentRangedWeaponHolding;
    }

    public function getPossibleMeleeWeapons(): array
    {
        $weaponCodes = [
            WeaponCategoryCode::AXES => MeleeWeaponCode::getAxesValues(),
            WeaponCategoryCode::KNIVES_AND_DAGGERS => MeleeWeaponCode::getKnivesAndDaggersValues(),
            WeaponCategoryCode::MACES_AND_CLUBS => MeleeWeaponCode::getMacesAndClubsValues(),
            WeaponCategoryCode::MORNINGSTARS_AND_MORGENSTERNS => MeleeWeaponCode::getMorningstarsAndMorgensternsValues(),
            WeaponCategoryCode::SABERS_AND_BOWIE_KNIVES => MeleeWeaponCode::getSabersAndBowieKnivesValues(),
            WeaponCategoryCode::STAFFS_AND_SPEARS => MeleeWeaponCode::getStaffsAndSpearsValues(),
            WeaponCategoryCode::SWORDS => MeleeWeaponCode::getSwordsValues(),
            WeaponCategoryCode::VOULGES_AND_TRIDENTS => MeleeWeaponCode::getVoulgesAndTridentsValues(),
            WeaponCategoryCode::UNARMED => MeleeWeaponCode::getUnarmedValues(),
        ];
        foreach ($weaponCodes as &$weaponCodesOfSameCategory) {
            $weaponCodesOfSameCategory = $this->addUsabilityToMeleeWeapons($weaponCodesOfSameCategory);
        }

        return $weaponCodes;
    }

    protected function addUsabilityToMeleeWeapons(array $meleeWeaponCodeValues): array
    {
        $meleeWeaponCodes = [];
        foreach ($meleeWeaponCodeValues as $meleeWeaponCodeValue) {
            $meleeWeaponCodes[] = MeleeWeaponCode::getIt($meleeWeaponCodeValue);
        }

        return $this->addWeaponlikeUsability($meleeWeaponCodes, $this->currentMeleeWeaponHolding);
    }

    protected function addWeaponlikeUsability(array $weaponLikeCode, ItemHoldingCode $itemHoldingCode): array
    {
        $withUsagePossibility = [];
        foreach ($weaponLikeCode as $code) {
            $withUsagePossibility[] = [
                'code' => $code,
                'canUseIt' => $this->canUseWeaponlike($code, $itemHoldingCode, $this->armourer, $this->currentProperties),
            ];
        }

        return $withUsagePossibility;
    }

    /**
     * @return array|RangedWeaponCode[][][]
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    public function getPossibleRangedWeapons(): array
    {
        $weaponCodes = [
            WeaponCategoryCode::THROWING_WEAPONS => RangedWeaponCode::getThrowingWeaponsValues(),
            WeaponCategoryCode::BOWS => RangedWeaponCode::getBowsValues(),
            WeaponCategoryCode::CROSSBOWS => RangedWeaponCode::getCrossbowsValues(),
        ];
        foreach ($weaponCodes as &$weaponCodesOfSameCategory) {
            $weaponCodesOfSameCategory = $this->addUsabilityToRangedWeapons($weaponCodesOfSameCategory);
        }

        return $weaponCodes;
    }

    /**
     * @param array|string[] $rangedWeaponCodeValues
     * @return array
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     */
    protected function addUsabilityToRangedWeapons(array $rangedWeaponCodeValues): array
    {
        $meleeWeaponCodes = [];
        foreach ($rangedWeaponCodeValues as $rangedWeaponCodeValue) {
            $meleeWeaponCodes[] = RangedWeaponCode::getIt($rangedWeaponCodeValue);
        }

        return $this->addWeaponlikeUsability($meleeWeaponCodes, $this->currentRangedWeaponHolding);
    }

    /**
     * @return array|BodyArmorCode[][][]
     */
    public function getPossibleBodyArmors(): array
    {
        $bodyArmors = \array_map(static fn(string $armorValue) => BodyArmorCode::getIt($armorValue), BodyArmorCode::getPossibleValues());

        return $this->addUsabilityToNonWeaponArmament($bodyArmors);
    }

    /**
     * @return array|HelmCode[][][]
     */
    public function getPossibleHelms(): array
    {
        $helmCodes = \array_map(static fn(string $helmValue) => HelmCode::getIt($helmValue), HelmCode::getPossibleValues());

        return $this->addUsabilityToNonWeaponArmament($helmCodes);
    }

    /**
     * @return array|ShieldCode[][][]
     */
    public function getPossibleShields(): array
    {
        $shieldCodes = \array_map(static fn(string $shieldValue) => ShieldCode::getIt($shieldValue), ShieldCode::getPossibleValues());

        return $this->addUsabilityToNonWeaponArmament($shieldCodes);
    }

    /**
     * @param array|ArmamentCode[] $armamentCodes
     * @return array
     */
    protected function addUsabilityToNonWeaponArmament(array $armamentCodes): array
    {
        $withUsagePossibility = [];
        foreach ($armamentCodes as $armamentCode) {
            $withUsagePossibility[] = [
                'code' => $armamentCode,
                'canUseIt' => $this->canUseArmament(
                    $armamentCode,
                    $this->currentProperties->getCurrentStrength(),
                    $this->armourer,
                    $this->currentProperties->getCurrentSize()
                ),
            ];
        }

        return $withUsagePossibility;
    }
}
