<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Background\BackgroundParts\Partials;

use DrdPlus\Codes\History\ExceptionalityCode;
use DrdPlus\Background\BackgroundPoints;
use DrdPlus\Background\BackgroundParts\Partials\AbstractBackgroundAdvantage;
use DrdPlus\Tests\Background\AbstractTestOfEnum;
use Granam\Integer\PositiveInteger;
use Granam\Integer\PositiveIntegerObject;

abstract class AbstractBackgroundAdvantageTest extends AbstractTestOfEnum
{

    /**
     * @test
     * @dataProvider provideSpentBackgroundPoints
     * @param int $spentBackgroundPointsValue
     */
    public function I_can_get_spent_background_points($spentBackgroundPointsValue)
    {
        $sut = $this->createSutToTestSpentBackgroundPoints(new PositiveIntegerObject($spentBackgroundPointsValue));
        self::assertSame($spentBackgroundPointsValue, $sut->getValue()); // default enum value getter
        $spentBackgroundPoints = $sut->getSpentBackgroundPoints();
        self::assertSame($spentBackgroundPointsValue, $spentBackgroundPoints->getValue());
        self::assertSame($spentBackgroundPoints, $sut->getSpentBackgroundPoints(), 'Expected same instance');
    }

    public function provideSpentBackgroundPoints(): array
    {
        return [[0], [1], [2], [3], [4], [5], [6], [7], [8]];
    }

    /**
     * @test
     */
    public function I_can_not_get_ancestry_background_points_with_invalid_value()
    {
        $this->expectException(\DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints::class);
        $this->expectExceptionMessageRegExp('~9~');
        $this->createSutToTestSpentBackgroundPoints(new PositiveIntegerObject(9));
    }

    /**
     * @param PositiveInteger $spentBackgroundPoints
     * @return AbstractBackgroundAdvantage
     */
    abstract protected function createSutToTestSpentBackgroundPoints(PositiveInteger $spentBackgroundPoints);

    /**
     * @param int $value
     * @return \Mockery\MockInterface|BackgroundPoints
     */
    protected function createBackgroundPoints(int $value): BackgroundPoints
    {
        $backgroundPoints = $this->mockery(BackgroundPoints::class);
        $backgroundPoints->shouldReceive('getValue')
            ->andReturn($value);

        return $backgroundPoints;
    }

    /**
     * @test
     */
    public function I_can_get_expected_exceptionality_code()
    {
        /** @var AbstractBackgroundAdvantage $sutClass */
        $sutClass = self::getSutClass();
        self::assertSame($this->getExpectedExceptionalityCode(), $sutClass::getExceptionalityCode());
    }

    private function getExpectedExceptionalityCode()
    {
        $sutName = strtolower(preg_replace('~^(?:\w+[\\\]){1,6}(\w+)$~', '$1', self::getSutClass()));
        if ($sutName === 'skillpointsfrombackground') {
            $sutName = 'skills';
        }
        foreach (ExceptionalityCode::getPossibleValues() as $exceptionality) {
            if (strpos($sutName, $exceptionality) === 0) {
                return ExceptionalityCode::getIt($exceptionality);
            }
        }

        throw new \LogicException('No exceptionality matches for ' . self::getSutClass()
            . ' (was searching for \'' . $sutName . '\')');
    }

    /**
     * @test
     */
    public function I_have_to_define_exceptionality_code_in_getter()
    {
        $this->expectException(\DrdPlus\Background\BackgroundParts\Partials\Exceptions\UnknownExceptionality::class);
        BrokenSut::getExceptionalityCode();
    }
}

class BrokenSut extends AbstractBackgroundAdvantage
{

}