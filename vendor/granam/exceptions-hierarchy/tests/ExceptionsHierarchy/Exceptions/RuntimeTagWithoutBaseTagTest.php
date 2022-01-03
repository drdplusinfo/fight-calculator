<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class RuntimeTagWithoutBaseTagTest extends AbstractExceptionsHierarchyTest
{

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy::class);
        $this->expectExceptionMessageMatches('~^Runtime tag interface .+ is not a child of .+\\\Exception$$~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\RuntimeTagWithoutBaseTag';
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
