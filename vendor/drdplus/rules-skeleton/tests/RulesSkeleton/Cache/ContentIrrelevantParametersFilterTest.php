<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Cache\ContentIrrelevantParametersFilter;
use PHPUnit\Framework\TestCase;

class ContentIrrelevantParametersFilterTest extends TestCase
{

    /**
     * @test
     */
    public function I_can_remove_content_irrelevant_parameters(): void
    {
        $contentIrrelevantParametersFilter = new ContentIrrelevantParametersFilter(['foo', 'bar']);
        self::assertSame(
            ['baz' => 123, 'qux' => false],
            $contentIrrelevantParametersFilter->filterContentIrrelevantParameters(['baz' => 123, 'qux' => false])
        );
        self::assertSame(
            ['baz' => 123, 'qux' => false],
            $contentIrrelevantParametersFilter->filterContentIrrelevantParameters([
                'foo' => 123,
                'baz' => 123,
                'qux' => false,
                'bar' => 'sdgseth',
            ])
        );
    }
}
