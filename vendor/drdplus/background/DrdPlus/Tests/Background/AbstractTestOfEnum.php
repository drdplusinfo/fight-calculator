<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Background;

use Granam\ScalarEnum\ScalarEnum;
use Granam\Tests\Tools\TestWithMockery;

abstract class AbstractTestOfEnum extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_use_it_as_an_enum(): void
    {
        $sutClass = self::getSutClass();
        self::assertTrue(\class_exists($sutClass), "Class $sutClass not found");
        self::assertTrue(
            \is_a($sutClass, ScalarEnum::class, true),
            "Class $sutClass should be child of " . ScalarEnum::class
        );
    }
}