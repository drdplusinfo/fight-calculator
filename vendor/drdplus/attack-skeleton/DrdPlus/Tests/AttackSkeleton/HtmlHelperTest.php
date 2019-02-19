<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Tests\AttackSkeleton\Partials\AttackCalculatorTestTrait;
use Granam\Integer\IntegerObject;
use Granam\String\StringObject;

class HtmlHelperTest extends \DrdPlus\Tests\CalculatorSkeleton\HtmlHelperTest
{
    use AttackCalculatorTestTrait;

    /**
     * @test
     */
    public function I_can_get_local_url_with_scalar_but_non_string_additional_parameters(): void
    {
        $htmlHelper = $this->getHtmlHelper();
        $encodedBrackets0 = \urlencode('[0]');
        $encodedBrackets1 = \urlencode('[1]');
        self::assertSame(
            '?just+SOME+boolean+PrOpErTy=1&some+number=123'
            . "&just+an+array+with+non-string+content$encodedBrackets0=-5"
            . "&just+an+array+with+non-string+content$encodedBrackets1=",
            $htmlHelper->getLocalUrlWithQuery([
                'just SOME boolean PrOpErTy' => true,
                'some number' => 123,
                'just an array with non-string content' => [
                    -5,
                    false,
                ],
            ])
        );
    }

    /**
     * @test
     */
    public function I_can_get_checked_property(): void
    {
        $htmlHelper = $this->getHtmlHelper();
        self::assertSame('checked', $htmlHelper->getChecked(new StringObject('foo'), new StringObject('foo')));
        self::assertSame('checked', $htmlHelper->getChecked(123, '123'));
        self::assertSame('', $htmlHelper->getChecked(new StringObject('foo'), new StringObject('bar')));
    }

    /**
     * @test
     */
    public function I_can_get_selected_property(): void
    {
        $htmlHelper = $this->getHtmlHelper();
        self::assertSame('selected', $htmlHelper->getSelected(new StringObject('foo'), new StringObject('foo')));
        self::assertSame('selected', $htmlHelper->getSelected(new IntegerObject(555), new StringObject('555')));
        self::assertSame('', $htmlHelper->getSelected(new StringObject('foo'), new StringObject('bar')));
    }

    /**
     * @test
     */
    public function I_can_get_disabled_property(): void
    {
        $htmlHelper = $this->getHtmlHelper();
        self::assertSame('disabled', $htmlHelper->getDisabled(false));
        self::assertSame('', $htmlHelper->getDisabled(true));
    }

    /**
     * @test
     * @dataProvider provideIntegersToFormat
     * @param $inputValue
     * @param string $expectedFormattedValue
     */
    public function I_can_get_formatted_integer($inputValue, string $expectedFormattedValue): void
    {
        $htmlHelper = $this->getHtmlHelper();
        self::assertSame($expectedFormattedValue, $htmlHelper->formatInteger($inputValue));
    }

    public function provideIntegersToFormat(): array
    {
        return [
            'positive integer' => [123, '+123'],
            'zero integer' => [0, '+0'],
            'negative integer' => [-555, '-555'],
            'positive integer object' => [new IntegerObject(123), '+123'],
            'zero integer object' => [new IntegerObject(0), '+0'],
            'negative integer object' => [new IntegerObject(-555), '-555'],
        ];
    }
}