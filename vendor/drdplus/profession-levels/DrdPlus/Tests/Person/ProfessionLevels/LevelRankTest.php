<?php declare(strict_types=1);

namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Person\ProfessionLevels\LevelRank;
use Granam\Tests\Tools\TestWithMockery;

class LevelRankTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_create_it(): void
    {
        $levelRank = LevelRank::getIt($value = 12345);
        self::assertSame(12345, $levelRank->getValue());
        self::assertSame('12345', (string)$levelRank);
    }

    /**
     * @test
     */
    public function I_can_get_its_value(): void
    {
        $levelRank = LevelRank::getIt($value = 12345);
        self::assertSame($value, $levelRank->getValue());
        self::assertSame((string)$value, (string)$levelRank);
    }

    /**
     * @test
     */
    public function I_can_create_it_from_to_string_object(): void
    {
        /** @noinspection PhpParamsInspection */
        $levelRank = LevelRank::getIt($someToStringObject = new SomeToStringObject($value = 12));
        self::assertSame($value, $levelRank->getValue());
        self::assertSame((string)$someToStringObject, (string)$levelRank);
    }

    /**
     * @test
     */
    public function I_can_easily_find_out_if_is_first_or_next_level(): void
    {
        $zeroLevelRank = LevelRank::getIt(0);
        self::assertTrue($zeroLevelRank->isZeroLevel());
        self::assertFalse($zeroLevelRank->isFirstLevel());
        self::assertFalse($zeroLevelRank->isNextLevel());

        $firstLevelRank = LevelRank::getIt(1);
        self::assertFalse($firstLevelRank->isZeroLevel());
        self::assertTrue($firstLevelRank->isFirstLevel());
        self::assertFalse($firstLevelRank->isNextLevel());

        $nextLevelRank = LevelRank::getIt(123);
        self::assertFalse($firstLevelRank->isZeroLevel());
        self::assertFalse($nextLevelRank->isFirstLevel());
        self::assertTrue($nextLevelRank->isNextLevel());
    }

    /**
     * @param int $prohibitedValue
     * @test
     * @dataProvider provideProhibitedLevelValue
     */
    public function I_can_not_create_negative_level(int $prohibitedValue): void
    {
        $this->expectException(\DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank::class);
        LevelRank::getIt($prohibitedValue);
    }

    public function provideProhibitedLevelValue(): array
    {
        return [[-1], [-12345]];
    }
}

/** @inner */
class SomeToStringObject
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}