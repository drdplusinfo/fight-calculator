<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy;

class ExternalLogicTagMissingTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\DummyExceptionsHierarchy\ExternalLogicTagMissing';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionsSubDir(): string
    {
        return '';
    }

    protected function getExternalRootNamespaces(): array
    {
        // skipping some namespace chain up from root namespace
        return ['\Granam\ExceptionsHierarchy'];
    }

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        $this->expectException(InvalidTagInterfaceHierarchy::class);
        $this->expectExceptionMessageMatches('~^Tag interface .+\\\Logic should extends external parent tag interface .+~');
        parent::My_exceptions_are_in_family_tree();
    }

}
