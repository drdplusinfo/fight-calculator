<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Skills\Psychical\Zoology;

class ZoologyTest extends WithBonusToIntelligenceFromPsychicalTest
{
    /**
     * @test
     */
    public function I_can_get_bonuses_when_fighting_natural_animal()
    {
        $zoology = new Zoology($this->createProfessionLevel());
        self::assertSame(0, $zoology->getBonusToAttackNumberAgainstFreeWillAnimal());
        self::assertSame(0, $zoology->getBonusToBaseOfWoundsAgainstFreeWillAnimal());
        self::assertSame(0, $zoology->getBonusToCoverAgainstFreeWillAnimal());

        for ($rank = 1; $rank <= 3; $rank++) {
            $zoology->increaseSkillRank($this->createSkillPoint());
            self::assertSame($rank, $zoology->getCurrentSkillRank()->getValue());
            self::assertSame($rank, $zoology->getBonusToAttackNumberAgainstFreeWillAnimal());
            self::assertSame($rank, $zoology->getBonusToBaseOfWoundsAgainstFreeWillAnimal());
            self::assertSame($rank, $zoology->getBonusToCoverAgainstFreeWillAnimal());
        }
    }
}