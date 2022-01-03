<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class TagWithoutTypeTest extends AbstractExceptionsHierarchyTest
{

    /**
     * I_am_stopped_on_tag_without_type
     *
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly::class);
        $this->expectExceptionMessageMatches('~ is not tagged by Runtime interface or even Logic interface$~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\TagWithoutType';
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
            DummyExceptionsHierarchy\TagWithoutType\IAmTagWithoutType::class,
        ];
    }

}
