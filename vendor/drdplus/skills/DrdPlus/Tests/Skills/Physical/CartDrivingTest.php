<?php declare(strict_types=1);

declare(strict_types = 1);

namespace DrdPlus\Tests\Skills\Physical;

use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Skills\Physical\CartDriving;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use Granam\Tests\Tools\TestWithMockery;

class CartDrivingTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_malus_to_cart_driving()
    {
        $cartDriving = new CartDriving($this->createProfessionLevel());

        self::assertSame(0, $cartDriving->getCurrentSkillRank()->getValue());
        self::assertSame(-3, $cartDriving->getMalusToMovementSpeed());

        $cartDriving->increaseSkillRank($this->createSkillPoint());
        self::assertSame(1, $cartDriving->getCurrentSkillRank()->getValue());
        self::assertSame(-2, $cartDriving->getMalusToMovementSpeed());

        $cartDriving->increaseSkillRank($this->createSkillPoint());
        self::assertSame(2, $cartDriving->getCurrentSkillRank()->getValue());
        self::assertSame(-1, $cartDriving->getMalusToMovementSpeed());

        $cartDriving->increaseSkillRank($this->createSkillPoint());
        self::assertSame(3, $cartDriving->getCurrentSkillRank()->getValue());
        self::assertSame(0, $cartDriving->getMalusToMovementSpeed());
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    private function createProfessionLevel()
    {
        return $this->mockery(ProfessionFirstLevel::class);
    }

    /**
     * @return \Mockery\MockInterface|PhysicalSkillPoint
     */
    private function createSkillPoint(): PhysicalSkillPoint
    {
        $physicalSkillPoint = $this->mockery(PhysicalSkillPoint::class);
        $physicalSkillPoint->shouldReceive('getValue')
            ->andReturn(1);

        return $physicalSkillPoint;
    }
}