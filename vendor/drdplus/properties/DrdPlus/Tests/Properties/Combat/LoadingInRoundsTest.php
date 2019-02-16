<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat;

use DrdPlus\Properties\Combat\LoadingInRounds;
use DrdPlus\Tests\Properties\Combat\Partials\PositiveIntegerCharacteristicForGameTest;

class LoadingInRoundsTest extends PositiveIntegerCharacteristicForGameTest
{
    /**
     * @test
     */
    public function I_can_get_property_easily()
    {
        $loadingInRounds = LoadingInRounds::getIt(123);
        self::assertInstanceOf(LoadingInRounds::class, $loadingInRounds);
        self::assertSame(123, $loadingInRounds->getValue());
    }

    /**
     * @return LoadingInRounds
     */
    protected function createSut(): LoadingInRounds
    {
        return LoadingInRounds::getIt(123);
    }
}