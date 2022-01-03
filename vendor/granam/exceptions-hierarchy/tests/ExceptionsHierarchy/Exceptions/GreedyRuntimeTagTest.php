<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy;

class GreedyRuntimeTagTest extends AbstractExceptionsHierarchyTest
{
    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(InvalidTagInterfaceHierarchy::class);
        $this->expectExceptionMessageMatches('~Runtime tag interface .+\\\Runtime can not be a logic tag~');
        parent::My_exceptions_are_in_family_tree();
    }

    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\GreedyRuntimeTag';
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
