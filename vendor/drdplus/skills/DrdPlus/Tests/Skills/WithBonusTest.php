<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\WithBonus;
use DrdPlus\Skills\WithBonusToCharisma;
use DrdPlus\Skills\WithBonusToIntelligence;
use DrdPlus\Skills\WithBonusToKnack;
use DrdPlus\Skills\WithBonusToSenses;
use DrdPlus\Skills\WithBonusToMovementSpeed;
use Granam\Tests\Tools\TestWithMockery;

abstract class WithBonusTest extends TestWithMockery
{
    /**
     * @test
     */
    public function It_has_expected_interface()
    {
        self::assertTrue(
            \is_a(self::getSutClass(), $this->getExpectedInterface(), true),
            self::getSutClass() . ' should implement ' . $this->getExpectedInterface()
        );
    }

    protected function getExpectedInterface(): string
    {
        return WithBonus::class;
    }

    /**
     * @test
     */
    public function I_can_get_its_bonus()
    {
        /** @var CombinedSkill|PhysicalSkill|PsychicalSkill $sutClass */
        $sutClass = self::getSutClass();
        /** @var CombinedSkill|PhysicalSkill|PsychicalSkill|WithBonus $sut */
        $sut = new $sutClass($this->createProfessionLevel());

        $getBonus = $this->getBonusGetterName();
        self::assertSame(0, $sut->getCurrentSkillRank()->getValue());
        self::assertSame(0, $sut->$getBonus());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusFromSkill(1), $sut->$getBonus());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusFromSkill(2), $sut->$getBonus());

        $sut->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $sut->getCurrentSkillRank()->getValue());
        self::assertSame($this->getExpectedBonusFromSkill(3), $sut->$getBonus());
    }

    protected function getBonusGetterName(): string
    {
        if (\is_a($this->getExpectedInterface(), WithBonusToMovementSpeed::class, true)) {
            return 'getBonusToMovementSpeed';
        }
        if (\is_a($this->getExpectedInterface(), WithBonusToCharisma::class, true)) {
            return 'getBonusToCharisma';
        }
        if (\is_a($this->getExpectedInterface(), WithBonusToIntelligence::class, true)) {
            return 'getBonusToIntelligence';
        }
        if (\is_a($this->getExpectedInterface(), WithBonusToKnack::class, true)) {
            return 'getBonusToKnack';
        }
        if (\is_a($this->getExpectedInterface(), WithBonusToSenses::class, true)) {
            return 'getBonusToSenses';
        }

        return 'getBonus';
    }

    abstract protected function getExpectedBonusFromSkill(int $skillRankValue): int;

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    protected function createProfessionLevel(): ProfessionFirstLevel
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    /**
     * @return \Mockery\MockInterface|PhysicalSkillPoint|PsychicalSkillPoint|CombinedSkillPoint|SkillPoint
     */
    abstract protected function createSkillPoint(): SkillPoint;
}