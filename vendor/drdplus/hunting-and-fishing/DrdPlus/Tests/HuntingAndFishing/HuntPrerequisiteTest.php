<?php
declare(strict_types=1);

namespace DrdPlus\Tests\HuntingAndFishing;

use DrdPlus\HuntingAndFishing\BonusFromDmForRolePlaying;
use DrdPlus\HuntingAndFishing\HuntPrerequisite;
use DrdPlus\HuntingAndFishing\WithBonusFromHuntingAndFishingSkill;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\Properties\Derived\Senses;
use Granam\Integer\IntegerInterface;
use Granam\Tests\Tools\TestWithMockery;

class HuntPrerequisiteTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_hunt_prerequisite(): void
    {
        $huntingAndFishing = new HuntPrerequisite(
            $this->createKnack(13),
            $this->createSenses(6),
            $this->createHuntingAndFishingSkillBonus(156),
            $this->createBonusFromDmForRolePlaying(237)
        );
        self::assertInstanceOf(IntegerInterface::class, $huntingAndFishing);
        self::assertSame(10 /* (13 + 6) / 2 */ + 156 + 237, $huntingAndFishing->getValue());
        self::assertSame((string)(10 /* (13 + 6) / 2 */ + 156 + 237), (string)$huntingAndFishing);
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Knack
     */
    private function createKnack(int $value): Knack
    {
        $knack = $this->mockery(Knack::class);
        $knack->shouldReceive('getValue')
            ->andReturn($value);

        return $knack;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|Senses
     */
    private function createSenses(int $value): Senses
    {
        $senses = $this->mockery(Senses::class);
        $senses->shouldReceive('getValue')
            ->andReturn($value);

        return $senses;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|WithBonusFromHuntingAndFishingSkill
     */
    private function createHuntingAndFishingSkillBonus(int $value): WithBonusFromHuntingAndFishingSkill
    {
        $huntingAndFishingSkillBonus = $this->mockery(WithBonusFromHuntingAndFishingSkill::class);
        $huntingAndFishingSkillBonus->shouldReceive('getBonusFromSkill')
            ->andReturn($value);

        return $huntingAndFishingSkillBonus;
    }

    /**
     * @param int $value
     * @return \Mockery\MockInterface|BonusFromDmForRolePlaying
     */
    private function createBonusFromDmForRolePlaying(int $value): BonusFromDmForRolePlaying
    {
        $bonusFromDmForRolePlaying = $this->mockery(BonusFromDmForRolePlaying::class);
        $bonusFromDmForRolePlaying->shouldReceive('getValue')
            ->andReturn($value);

        return $bonusFromDmForRolePlaying;
    }
}