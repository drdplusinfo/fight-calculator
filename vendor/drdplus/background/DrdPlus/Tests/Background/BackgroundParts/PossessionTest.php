<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Background\BackgroundParts;

use DrdPlus\Background\BackgroundParts\Possession;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Background\BackgroundParts\Partials\AbstractAncestryDependentTest;
use Granam\Integer\PositiveIntegerObject;

class PossessionTest extends AbstractAncestryDependentTest
{
    /**
     * @test
     * @dataProvider provideBackgroundPointsAndAncestryAndPrice
     * @param int $spentBackgroundPoints
     * @param int $ancestryValue
     * @param int $expectedPrice
     */
    public function I_can_get_possession_price(int $spentBackgroundPoints, int $ancestryValue, int $expectedPrice): void
    {
        $possession = Possession::getIt(
            new PositiveIntegerObject($spentBackgroundPoints),
            $ancestry = $this->createAncestry($ancestryValue),
            Tables::getIt()
        );
        $price = $possession->getPrice(Tables::getIt());
        self::assertSame((float)$expectedPrice, $price->getGoldCoins());
        self::assertSame((float)$expectedPrice, $price->getValue()); // the base value is in gold already
    }

    public function provideBackgroundPointsAndAncestryAndPrice(): array
    {
        return [
            [0, 0, 1],
            [1, 0, 3],
            [2, 0, 10],
            [3, 0, 30],
            [4, 4, 100],
            [5, 3, 300],
            [6, 8, 1000],
            [7, 7, 3000],
            [8, 6, 10000],
        ];
    }
}