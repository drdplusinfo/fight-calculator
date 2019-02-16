<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Body;

use DrdPlus\Properties\Body\BodyProperty;

/**
 * @method static string getSutClass
 * @method static assertInstanceOf($expectedClass, $class)
 */
trait BodyPropertyTest
{
    /**
     * @test
     */
    public function It_is_body_property()
    {
        self::assertTrue(is_a(self::getSutClass(), BodyProperty::class, true));
    }
}