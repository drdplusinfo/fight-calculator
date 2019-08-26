<?php declare(strict_types=1);

namespace DrdPlus\Tests\Health;

use DrdPlus\Health\ReasonToRollAgainstMalusFromWounds;
use PHPUnit\Framework\TestCase;

class ReasonToRollAgainstMalusTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_use_wound_reason(): void
    {
        $woundReason = ReasonToRollAgainstMalusFromWounds::getWoundReason();
        self::assertTrue($woundReason->becauseOfWound());
        self::assertFalse($woundReason->becauseOfHeal());
        self::assertSame('wound', $woundReason->getValue());
        self::assertSame('wound', ReasonToRollAgainstMalusFromWounds::WOUND);
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getIt('wound'), $woundReason);
    }

    public function I_can_use_heal_reason(): void
    {
        $healReason = ReasonToRollAgainstMalusFromWounds::getHealReason();
        self::assertTrue($healReason->becauseOfHeal());
        self::assertFalse($healReason->becauseOfWound());
        self::assertSame('heal', $healReason->getValue());
        self::assertSame('heal', ReasonToRollAgainstMalusFromWounds::HEAL);
        self::assertSame(ReasonToRollAgainstMalusFromWounds::getIt('heal'), $healReason);
    }

    /**
     * @test
     */
    public function I_can_not_create_unknown_reason(): void
    {
        $this->expectException(\DrdPlus\Health\Exceptions\UnknownReasonToRollAgainstMalus::class);
        $this->expectExceptionMessageRegExp('~hypochondriac~');
        ReasonToRollAgainstMalusFromWounds::getEnum('hypochondriac');
    }
}