<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly;

class NotTaggedExceptionTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(ExceptionIsNotTaggedProperly::class);
        $this->expectExceptionMessageMatches('~^Class .+\\\NotTaggedExceptionWithout\\\IToughIAmTagged has to be tagged by Exception interface$~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\NotTaggedExceptionWithout';
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
            DummyExceptionsHierarchy\NotTaggedExceptionWithout\IToughIAmTagged::class,
        ];
    }

}
