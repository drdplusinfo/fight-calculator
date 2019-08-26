<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\Combined\HandlingWithAnimals;

class HandlingWithAnimalsTest extends WithBonusFromCombinedTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_can_soothe_runaway_animal()
    {
        $handlingWithAnimals = new HandlingWithAnimals($this->createProfessionLevel());
        self::assertFalse($handlingWithAnimals->canSootheRunawayAnimal());
        $handlingWithAnimals->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($handlingWithAnimals->canSootheRunawayAnimal());
        $handlingWithAnimals->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($handlingWithAnimals->canSootheRunawayAnimal());
        $handlingWithAnimals->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($handlingWithAnimals->canSootheRunawayAnimal());
    }
}