<?php
declare(strict_types=1);

namespace DrdPlus\Tests\HuntingAndFishing;

use DrdPlus\HuntingAndFishing\BonusFromDmForRolePlaying;
use Granam\Integer\PositiveInteger;
use Granam\Tests\Tools\TestWithMockery;

class BonusFromDmForRolePlayingTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider providePossibleValuesForBonus
     * @param int $value
     */
    public function I_can_use_it(int $value): void
    {
        $bonusFromDmForRolePlaying = new BonusFromDmForRolePlaying($value);
        self::assertInstanceOf(PositiveInteger::class, $bonusFromDmForRolePlaying);
        self::assertSame($value, $bonusFromDmForRolePlaying->getValue());
        self::assertSame((string)$value, (string)$bonusFromDmForRolePlaying->getValue());
    }

    public function providePossibleValuesForBonus(): array
    {
        return [
            [0],
            [1],
            [2],
            [3],
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\HuntingAndFishing\Exceptions\BonusFromDmIsTooHigh
     * @expectedExceptionMessageRegExp ~4~
     */
    public function I_can_not_create_too_high_bonus(): void
    {
        new BonusFromDmForRolePlaying(4);
    }
}