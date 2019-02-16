<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\SkillRank;
use Granam\Integer\PositiveInteger;
use Mockery\MockInterface;
use Granam\Tests\Tools\TestWithMockery;

abstract class SkillRankTest extends TestWithMockery
{

    /**
     * @test
     * @dataProvider provideAllowedSkillRankValues
     * @param int $skillRankValue
     */
    public function I_can_create_skill_rank($skillRankValue)
    {
        $sutClass = self::getSutClass();
        /** @var SkillRank $skillRank */
        $skillRank = new $sutClass(
            $this->createOwningSkill(),
            $skillPoint = $this->createSkillPoint($skillRankValue > 0 ? 1 : 0),
            $this->createRequiredRankValue($skillRankValue)
        );

        self::assertSame($skillRankValue, $skillRank->getValue());
        self::assertSame((string)$skillRankValue, (string)$skillRank);
        self::assertSame($skillPoint->getProfessionLevel(), $skillRank->getProfessionLevel());
        self::assertSame($skillPoint, $skillRank->getSkillPoint());
    }

    public function provideAllowedSkillRankValues()
    {
        return [[0], [1], [2], [3]];
    }

    abstract protected function createOwningSkill(): Skill;

    protected function addProfessionLevelGetter(MockInterface $mock)
    {
        $mock->shouldReceive('getProfessionLevel')
            ->andReturn($this->mockery(ProfessionLevel::class));
    }

    /**
     * @return MockInterface|ProfessionFirstLevel
     */
    protected function createProfessionFirstLevel()
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function I_can_not_create_negative_skill_rank()
    {
        /** @var SkillRank $sutClass */
        $sutClass = self::getSutClass();
        new $sutClass(
            $this->createOwningSkill(),
            $this->createSkillPoint(0),
            $this->createRequiredRankValue(-1)
        );
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function I_can_not_create_skill_rank_with_value_of_four()
    {
        $sutClass = self::getSutClass();
        new $sutClass(
            $this->createOwningSkill(),
            $this->createSkillPoint(1),
            $this->createRequiredRankValue(4)
        );
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|SkillPoint
     */
    abstract protected function createSkillPoint($value = null);

    /**
     * @param int $value
     * @return \Mockery\MockInterface|PositiveInteger
     */
    private function createRequiredRankValue($value)
    {
        $requiredRankValue = $this->mockery(PositiveInteger::class);
        $requiredRankValue->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($value);

        return $requiredRankValue;
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\CanNotVerifyOwningSkill
     */
    public function Skill_has_to_be_set_in_descendant_constructor_first()
    {
        /** @var PositiveInteger $requiredRankValue */
        $requiredRankValue = $this->mockery(PositiveInteger::class);

        new BrokenBecauseOfSkillNotSetInConstructor(
            $this->createOwningSkill(),
            $this->createSkillPoint(),
            $requiredRankValue
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\CanNotVerifyPaidSkillPoint
     */
    public function Skill_point_has_to_be_set_in_descendant_constructor_first()
    {
        /** @var PositiveInteger $requiredRankValue */
        $requiredRankValue = $this->mockery(PositiveInteger::class);

        new BrokenBecauseOfSkillPointNotSetInConstructor(
            $this->createOwningSkill(),
            $this->createSkillPoint(),
            $requiredRankValue
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\WastedSkillPoint
     */
    public function I_can_not_pay_for_zero_skill_rank_by_non_zero_skill_point()
    {
        /** @var SkillRank|string $sutClass */
        $sutClass = self::getSutClass();
        new $sutClass(
            $this->createOwningSkill(),
            $this->createSkillPoint(1),
            $this->createRequiredRankValue(0)
        );
    }
}

class BrokenBecauseOfSkillNotSetInConstructor extends SkillRank
{
    public function __construct(
        Skill $owningSkill,
        SkillPoint $skillPoint,
        PositiveInteger $requiredRankValue
    )
    {
        parent::__construct($owningSkill, $skillPoint, $requiredRankValue);
    }

    public function getSkillPoint(): SkillPoint
    {
        return \Mockery::mock(SkillPoint::class);
    }

    public function getSkill(): Skill
    {
        return \Mockery::mock(Skill::class);
    }

}

class BrokenBecauseOfSkillPointNotSetInConstructor extends SkillRank
{
    private $skill;

    public function __construct(
        Skill $owningSkill,
        SkillPoint $skillPoint,
        PositiveInteger $requiredRankValue
    )
    {
        $this->skill = $owningSkill;
        parent::__construct($owningSkill, $skillPoint, $requiredRankValue);
    }

    public function getSkillPoint(): SkillPoint
    {
        return \Mockery::mock(SkillPoint::class);
    }

    public function getSkill(): Skill
    {
        return $this->skill;
    }

}