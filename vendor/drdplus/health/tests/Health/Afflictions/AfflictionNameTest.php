<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Health\Afflictions\AfflictionName;
use PHPUnit\Framework\TestCase;

class AfflictionNameTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_any_name()
    {
        $afflictionName = AfflictionName::getIt('foo');
        self::assertInstanceOf(AfflictionName::class, $afflictionName);
        self::assertSame('foo', $afflictionName->getValue());
    }

    /**
     * @test
     */
    public function I_can_not_create_empty_name()
    {
        $this->expectException(\DrdPlus\Health\Afflictions\Exceptions\AfflictionNameCanNotBeEmpty::class);
        AfflictionName::getIt('');
    }
}