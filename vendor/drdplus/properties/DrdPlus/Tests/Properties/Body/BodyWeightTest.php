<?php declare(strict_types = 1);

declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Body;

use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Body\BodyWeight;
use DrdPlus\Properties\Body\BodyWeightInKg;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Measurements\Weight\WeightBonus;
use DrdPlus\Tests\BaseProperties\Partials\PropertyTest;

class BodyWeightTest extends PropertyTest
{
    use BodyPropertyTest;

    protected function getExpectedCodeClass(): string
    {
        return PropertyCode::class;
    }

    /**
     * @test
     */
    public function I_can_get_property_easily(): void
    {
        $weight = $this->createWeight(123 /* weight bonus */, 456.789 /* weight in kg */);
        $bodyWeight = BodyWeight::getIt($weight);
        self::assertSame(111, $bodyWeight->getValue());
        self::assertSame('111', (string)$bodyWeight);
        self::assertSame(PropertyCode::getIt(PropertyCode::BODY_WEIGHT), $bodyWeight->getCode());
        self::assertSame($weight, $bodyWeight->getWeight());
        $weightBonus = $bodyWeight->getWeightBonus();
        self::assertSame(123, $weightBonus->getValue());
        self::assertSame(BodyWeightInKg::getIt(456.789), $bodyWeight->getBodyWeightInKg());
    }

    /**
     * @param $weightBonusValue
     * @param $weightInKgValue
     * @return \Mockery\MockInterface|Weight
     */
    private function createWeight(int $weightBonusValue, float $weightInKgValue): Weight
    {
        $weightBonus = $this->mockery(WeightBonus::class);
        $weightBonus->shouldReceive('getValue')
            ->andReturn($weightBonusValue);
        $weight = $this->mockery(Weight::class);
        $weight->shouldReceive('getBonus')
            ->andReturn($weightBonus);
        $weight->shouldReceive('getKilograms')
            ->andReturn($weightInKgValue);

        return $weight;
    }
}