<?php declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class CurrentArmamentsValuesTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_get_current_custom_helm_values()
    {
        $helmValues = [
            CurrentArmamentsValues::CUSTOM_HELM_NAME => 'foo',
            CurrentArmamentsValues::CUSTOM_HELM_REQUIRED_STRENGTH => 123,
            CurrentArmamentsValues::CUSTOM_HELM_RESTRICTION => 111,
            CurrentArmamentsValues::CUSTOM_HELM_PROTECTION => 222,
            CurrentArmamentsValues::CUSTOM_HELM_WEIGHT => 333,
        ];
        $currentArmamentsValues = new CurrentArmamentsValues($this->createCurrentValues($helmValues));
        self::assertSame(
            ['foo' => $helmValues],
            $currentArmamentsValues->getCurrentCustomHelmsValues()
        );
    }

    /**
     * @param array $plainValues
     * @return CurrentValues|MockInterface
     */
    private function createCurrentValues(array $plainValues): CurrentValues
    {
        $currentValues = $this->mockery(CurrentValues::class);
        $currentValues->shouldReceive('getCurrentValue')
            ->andReturnUsing(function (string $valueName) use ($plainValues) {
                return $plainValues[$valueName] ?? null;
            });
        return $currentValues;
    }

    /**
     * @test
     */
    public function I_can_get_current_custom_body_armor_values()
    {
        $bodyArmorValues = [
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_NAME => 'bar',
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_REQUIRED_STRENGTH => 123,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_RESTRICTION => 111,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_PROTECTION => 222,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_WEIGHT => 333,
            CurrentArmamentsValues::CUSTOM_BODY_ARMOR_ROUNDS_TO_PUT_ON => 444,
        ];
        $currentArmamentsValues = new CurrentArmamentsValues($this->createCurrentValues($bodyArmorValues));
        self::assertSame(
            ['bar' => $bodyArmorValues],
            $currentArmamentsValues->getCurrentCustomBodyArmorsValues()
        );
    }
}
