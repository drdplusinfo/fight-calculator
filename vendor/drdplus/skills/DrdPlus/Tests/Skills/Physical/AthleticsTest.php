<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Skills\Physical\Athletics;
use DrdPlus\Tables\Properties\AthleticsInterface;
use DrdPlus\Tests\Skills\WithBonusTest;

class AthleticsTest extends WithBonusTest
{
    use CreatePhysicalSkillPointTrait;

    /**
     * @test
     */
    public function Can_be_used_as_properties_athletic_requirement(): void
    {
        $athletics = new Athletics($this->createProfessionLevel());
        self::assertInstanceOf(
            AthleticsInterface::class,
            $athletics,
            Athletics::class . ' should implement ' . AthleticsInterface::class . ' interface'
        );
        self::assertSame($athletics->getCurrentSkillRank(), $athletics->getAthleticsBonus());
    }

    protected function getExpectedBonusFromSkill(int $skillRankValue): int
    {
        return $skillRankValue;
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_fall(): void
    {
        $athletics = new Athletics($this->createProfessionLevel());
        self::assertSame($athletics->getBonus(), $athletics->getBonusToAgilityOnFall());
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_run_sprint_and_jump(): void
    {
        $athletics = new Athletics($this->createProfessionLevel());
        self::assertSame($athletics->getBonus(), $athletics->getBonusToSpeedOnRunSprintAndJump());
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_maximal_load(): void
    {
        $athletics = new Athletics($this->createProfessionLevel());
        self::assertSame($athletics->getBonus(), $athletics->getBonusToMaximalLoad());
    }
}