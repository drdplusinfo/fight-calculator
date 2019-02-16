<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat\Partials;

use DrdPlus\Codes\CombatCharacteristicCode;
use DrdPlus\Properties\Combat\Partials\CharacteristicForGame;
use DrdPlus\Tests\BaseProperties\Partials\PropertyTest;
use Granam\Integer\IntegerInterface;

abstract class CombatCharacteristicTest extends PropertyTest
{
    protected function getExpectedCodeClass(): string
    {
        return CombatCharacteristicCode::class;
    }

    /**
     * @test
     */
    public function I_can_use_it_as_integer_object(): void
    {
        $sut = $this->createSut();
        self::assertInstanceOf(IntegerInterface::class, $sut);
        self::assertSame((string)$sut->getValue(), (string)$sut);
        self::assertIsInt($sut->getValue());
    }

    /**
     * @return CharacteristicForGame
     */
    abstract protected function createSut();

}