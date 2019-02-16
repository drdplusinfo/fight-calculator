<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\Psychical\ReadingAndWriting;
use Granam\Tests\Tools\TestWithMockery;

class ReadingAndWritingTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_bonus_to_reading_speed()
    {
        $readingAndWriting = new ReadingAndWriting($this->createProfessionLevel());

        self::assertSame(0, $readingAndWriting->getCurrentSkillRank()->getValue());
        self::assertSame(-164, $readingAndWriting->getBonusToReadingSpeed());

        $readingAndWriting->increaseSkillRank($this->createPsychicalSkillPoint());
        self::assertSame(1, $readingAndWriting->getCurrentSkillRank()->getValue());
        self::assertSame(0, $readingAndWriting->getBonusToReadingSpeed());

        $readingAndWriting->increaseSkillRank($this->createPsychicalSkillPoint());
        self::assertSame(2, $readingAndWriting->getCurrentSkillRank()->getValue());
        self::assertSame(3, $readingAndWriting->getBonusToReadingSpeed());

        $readingAndWriting->increaseSkillRank($this->createPsychicalSkillPoint());
        self::assertSame(3, $readingAndWriting->getCurrentSkillRank()->getValue());
        self::assertSame(6, $readingAndWriting->getBonusToReadingSpeed());
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    protected function createProfessionLevel()
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    /**
     * @return \Mockery\MockInterface|PsychicalSkillPoint
     */
    protected function createPsychicalSkillPoint(): PsychicalSkillPoint
    {
        $psychicalSkillPoint = $this->mockery(PsychicalSkillPoint::class);
        $psychicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $psychicalSkillPoint;
    }
}