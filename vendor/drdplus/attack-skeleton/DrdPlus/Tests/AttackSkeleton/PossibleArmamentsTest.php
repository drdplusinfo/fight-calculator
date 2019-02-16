<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\CurrentProperties;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\Armaments\BodyArmorCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\Properties\Body\Size;

class PossibleArmamentsTest extends AbstractAttackTest
{
    /**
     * @test
     */
    public function I_get_all_armor_codes_with_their_usability(): void
    {
        $armourer = Armourer::getIt();
        $possibleArmaments = new PossibleArmaments(
            $armourer,
            new CurrentProperties(new CurrentValues([], $this->createEmptyMemory())),
            ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS),
            ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS)
        );
        $possibleBodyArmors = $possibleArmaments->getPossibleBodyArmors();
        self::assertCount(\count(BodyArmorCode::getPossibleValues()), $possibleBodyArmors);
        $bodyArmorValues = BodyArmorCode::getPossibleValues();
        $strength = Strength::getIt(0);
        $size = Size::getIt(0);
        foreach ($possibleBodyArmors as $index => $bodyArmorWithUsability) {
            /** @var BodyArmorCode $bodyArmor */
            $bodyArmor = $bodyArmorWithUsability['code'];
            self::assertContains($bodyArmor->getValue(), $bodyArmorValues);
            unset($possibleBodyArmors[$index], $bodyArmorValues[\array_search($bodyArmor->getValue(), $bodyArmorValues, true)]);
            self::assertSame(
                $armourer->canUseArmament($bodyArmor, $strength, $size),
                $bodyArmorWithUsability['canUseIt'],
                "Armor {$bodyArmor} has opposite usability with zero strength and body size"
            );
        }
        self::assertCount(0, $bodyArmorValues, 'There are some body armors missed by the ' . self::getSutClass());
        self::assertCount(0, $possibleBodyArmors, 'There are some non-existing body armors given by the ' . self::getSutClass());
    }

}