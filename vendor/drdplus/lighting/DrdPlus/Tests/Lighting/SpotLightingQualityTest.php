<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Lighting;

use DrdPlus\Lighting\LightingQuality;
use DrdPlus\Lighting\Partials\LightingQualityInterface;
use DrdPlus\Lighting\SpotLightingQuality;
use PHPUnit\Framework\TestCase;

class SpotLightingQualityTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_create_very_dark_item_lighting_quality()
    {
        $forVeryDarkItem = SpotLightingQuality::createForVeryDarkItem(new LightingQuality(15));
        self::assertSame(-5, $forVeryDarkItem->getValue());
        self::assertSame('-5', (string)$forVeryDarkItem);
        self::assertInstanceOf(LightingQualityInterface::class, $forVeryDarkItem);
    }

    /**
     * @test
     */
    public function I_can_create_bright_white_item_lighting_quality()
    {
        $forVeryDarkItem = SpotLightingQuality::createForBrightWhiteItem(new LightingQuality(15));
        self::assertSame(35, $forVeryDarkItem->getValue());
        self::assertSame('35', (string)$forVeryDarkItem);
        self::assertInstanceOf(LightingQualityInterface::class, $forVeryDarkItem);
    }

    /**
     * @test
     */
    public function I_can_create_light_source_spot_lighting_quality()
    {
        $forVeryDarkItem = SpotLightingQuality::createForLightSource(new LightingQuality(15));
        self::assertSame(45, $forVeryDarkItem->getValue());
        self::assertSame('45', (string)$forVeryDarkItem);
        self::assertInstanceOf(LightingQualityInterface::class, $forVeryDarkItem);
    }
}