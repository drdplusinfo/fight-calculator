<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Psychical;

use DrdPlus\Skills\Psychical\HandlingWithMagicalItems;

class HandlingWithMagicalItemsTest extends WithBonusToIntelligenceFromPsychicalTest
{
    /**
     * @test
     */
    public function I_can_find_out_if_automatically_recognizes_an_item()
    {
        $handlingWithMagicalItems = new HandlingWithMagicalItems($this->createProfessionLevel());
        self::assertFalse($handlingWithMagicalItems->automaticallyRecognizesSameMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($handlingWithMagicalItems->automaticallyRecognizesSameMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($handlingWithMagicalItems->automaticallyRecognizesSameMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($handlingWithMagicalItems->automaticallyRecognizesSameMagicalItemInvestigatedBefore());
    }

    /**
     * @test
     */
    public function I_can_find_out_if_automatically_recognizes_an_item_category()
    {
        $handlingWithMagicalItems = new HandlingWithMagicalItems($this->createProfessionLevel());
        self::assertFalse($handlingWithMagicalItems->automaticallyRecognizesCategoryOfMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($handlingWithMagicalItems->automaticallyRecognizesCategoryOfMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertFalse($handlingWithMagicalItems->automaticallyRecognizesCategoryOfMagicalItemInvestigatedBefore());

        $handlingWithMagicalItems->increaseSkillRank($this->createSkillPoint());
        self::assertTrue($handlingWithMagicalItems->automaticallyRecognizesCategoryOfMagicalItemInvestigatedBefore());
    }
}