<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly;
use Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\GreedyException\BothRuntimeAndLogicTagged;

class GreedyExceptionTest extends AbstractExceptionsHierarchyTest
{

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(ExceptionIsNotTaggedProperly::class);
        $this->expectExceptionMessageMatches('~ can not be tagged by Runtime interface and Logic interface at the same time$~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\GreedyException';
    }

    protected function getExceptionsSubDir(): string
    {
        return '';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [
            BothRuntimeAndLogicTagged::class,
        ];
    }

}
