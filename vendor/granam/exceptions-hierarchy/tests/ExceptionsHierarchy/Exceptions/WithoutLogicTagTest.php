<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class WithoutLogicTagTest extends AbstractExceptionsHierarchyTest
{

    /**
     * I_am_stopped_if_runtime_tag_is_missing
     *
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound::class);
        $this->expectExceptionMessageMatches('~^Logic tag interface .+\\\Logic not found$~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\WithoutLogicTag';
    }

    protected function getExceptionsSubDir(): string
    {
        return '';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

}
