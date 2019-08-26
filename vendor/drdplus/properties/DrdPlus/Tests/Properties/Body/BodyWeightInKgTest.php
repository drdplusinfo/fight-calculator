<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Body;

use DrdPlus\Properties\Body\BodyWeight;
use DrdPlus\Properties\Body\BodyWeightInKg;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightBonus;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Properties\Partials\AbstractFloatPropertyTest;

class BodyWeightInKgTest extends AbstractFloatPropertyTest
{
    use BodyPropertyTest;

    /**
     * @test
     */
    public function I_can_get_it_by_weight()
    {
        $bodyWeightInKg = BodyWeightInKg::getItByWeight($this->createWeight(123.456));
        self::assertInstanceOf(BodyWeightInKg::class, $bodyWeightInKg);
        self::assertSame(123.456, $bodyWeightInKg->getValue());
    }

    /**
     * @param float $weightInKilograms
     * @return Weight|\Mockery\MockInterface
     */
    private function createWeight(float $weightInKilograms): Weight
    {
        $weight = $this->mockery(Weight::class);
        $weight->shouldReceive('getKilograms')
            ->andReturn($weightInKilograms);

        return $weight;
    }

    /**
     * @test
     */
    public function I_can_get_body_weight()
    {
        $bodyWeightInKg = BodyWeightInKg::getIt(123.234);
        $bodyWeight = $bodyWeightInKg->getBodyWeight(Tables::getIt());
        self::assertInstanceOf(BodyWeight::class, $bodyWeight);
        self::assertSame(
            $bodyWeight->getValue(),
            (new Weight(123.234, Weight::KG, Tables::getIt()->getWeightTable()))->getBonus()->getValue() - 12
        );
    }

    /**
     * @test
     */
    public function I_can_get_weight()
    {
        $bodyWeightInKg = BodyWeightInKg::getIt(54.12);
        $weight = $bodyWeightInKg->getWeight(Tables::getIt());
        self::assertInstanceOf(Weight::class, $weight);
        self::assertSame(
            $weight->getValue(),
            (new Weight(123.234, Weight::KG, Tables::getIt()->getWeightTable()))->getValue()
        );
    }

    /**
     * @test
     */
    public function I_can_get_weight_bonus()
    {
        $bodyWeightInKg = BodyWeightInKg::getIt(54.12);
        $weightBonus = $bodyWeightInKg->getWeightBonus(Tables::getIt());
        self::assertInstanceOf(WeightBonus::class, $weightBonus);
        self::assertSame(
            $weightBonus->getValue(),
            (new Weight(123.234, Weight::KG, Tables::getIt()->getWeightTable()))->getBonus()->getValue()
        );
    }
}