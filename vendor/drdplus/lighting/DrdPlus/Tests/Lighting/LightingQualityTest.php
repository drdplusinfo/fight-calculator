<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Lighting\LightingQuality;
use DrdPlus\Lighting\Partials\LightingQualityInterface;
use PHPUnit\Framework\TestCase;

class LightingQualityTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $lightingQuality = new LightingQuality(-123);
        self::assertSame(-123, $lightingQuality->getValue());
        self::assertSame('-123', (string)$lightingQuality);
        self::assertInstanceOf(LightingQualityInterface::class, $lightingQuality);
    }
}