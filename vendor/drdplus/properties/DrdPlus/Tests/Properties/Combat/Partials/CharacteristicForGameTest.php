<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Combat\Partials;

use DrdPlus\Codes\Properties\CharacteristicForGameCode;

abstract class CharacteristicForGameTest extends CombatCharacteristicTest
{
    protected function getExpectedCodeClass(): string
    {
        return CharacteristicForGameCode::class;
    }

    /**
     * @test
     */
    public function I_can_add_value()
    {
        $combatCharacteristic = $this->createSut();

        $increased = $combatCharacteristic->add(456);
        self::assertNotEquals($combatCharacteristic, $increased);
        self::assertSame($combatCharacteristic->getValue() + 456, $increased->getValue());

        $double = $increased->add($increased);
        self::assertSame($increased->getValue() * 2, $double->getValue());
    }

    /**
     * @test
     */
    public function I_can_subtract_value()
    {
        $combatCharacteristic = $this->createSut();

        $decreased = $combatCharacteristic->sub(1);
        self::assertNotEquals($combatCharacteristic, $decreased);
        self::assertSame($combatCharacteristic->getValue() - 1, $decreased->getValue());

        $zeroed = $decreased->sub($decreased);
        self::assertSame(0, $zeroed->getValue());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function Its_modifying_methods_have_return_value_annotated()
    {
        $reflectionClass = new \ReflectionClass(self::getSutClass());
        $classBasename = preg_replace('~^.+[\\\](\w+)$~', '$1', self::getSutClass());
        self::assertStringContainsString(<<<ANNOTATION
 * @method {$classBasename} add(int | \\Granam\\Integer\\IntegerInterface \$value)
 * @method {$classBasename} sub(int | \\Granam\\Integer\\IntegerInterface \$value)
ANNOTATION
            , (string)$reflectionClass->getDocComment());
    }
}