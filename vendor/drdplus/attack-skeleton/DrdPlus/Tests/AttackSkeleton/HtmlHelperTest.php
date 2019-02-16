<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DrdPlus\Tests\AttackSkeleton\Partials\AttackCalculatorTestTrait;

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
}