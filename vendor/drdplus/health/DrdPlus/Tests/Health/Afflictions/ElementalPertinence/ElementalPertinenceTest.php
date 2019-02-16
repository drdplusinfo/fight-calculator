<?php
namespace DrdPlus\Tests\Health\Afflictions\ElementalPertinence;

use DrdPlus\Health\Afflictions\ElementalPertinence\AirPertinence;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use PHPUnit\Framework\TestCase;

abstract class ElementalPertinenceTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_get_both_minus_and_plus_pertinence()
    {
        $pertinenceClass = $this->getPertinenceClass();
        $classConstantName = "{$pertinenceClass}::" . strtoupper($pertinenceClass::getPertinenceCode());
        self::assertTrue(defined($classConstantName), "Expected constant {$classConstantName}");
        self::assertSame(constant($classConstantName), $pertinenceClass::getPertinenceCode());

        $minusPertinence = $pertinenceClass::getMinus();
        self::assertInstanceOf($pertinenceClass, $minusPertinence);
        self::assertTrue($minusPertinence->isMinus());
        self::assertFalse($minusPertinence->isPlus());
        self::assertContains($pertinenceClass::getPertinenceCode(), $minusPertinence->getValue());

        $plusPertinence = $pertinenceClass::getPlus();
        self::assertInstanceOf($pertinenceClass, $plusPertinence);
        self::assertTrue($plusPertinence->isPlus());
        self::assertFalse($plusPertinence->isMinus());
        self::assertContains($pertinenceClass::getPertinenceCode(), $plusPertinence->getValue());
    }

    /**
     * @return string|ElementalPertinence|AirPertinence ...
     */
    private function getPertinenceClass()
    {
        return preg_replace('~[\\\]Tests([\\\].+)Test$~', '$1', static::class);
    }
}