<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

use Granam\ExceptionsHierarchy\TestOfExceptionsHierarchy;
use PHPUnit\Framework\TestCase;

abstract class AbstractExceptionsHierarchyTest extends TestCase
{

    /**
     * @var TestOfExceptionsHierarchy
     */
    private $testOfExceptionsHierarchy;

    protected function setUp(): void
    {
        $this->testOfExceptionsHierarchy = new TestOfExceptionsHierarchy(
            $this->getTestedNamespace(),
            $this->getRootNamespace(),
            $this->getExceptionsSubDir(),
            $this->getExternalRootNamespaces(),
            $this->getExternalRootExceptionsSubDir()
        );
    }

    /**
     * @return TestOfExceptionsHierarchy
     */
    protected function getTestOfExceptionsHierarchy(): TestOfExceptionsHierarchy
    {
        return $this->testOfExceptionsHierarchy;
    }

    abstract protected function getTestedNamespace(): string;

    abstract protected function getRootNamespace(): string;

    protected function getExceptionsSubDir(): string
    {
        return 'Exceptions';
    }

    /**
     * @return string[]
     */
    protected function getExternalRootNamespaces(): array
    {
        return [];
    }

    protected function getExternalRootExceptionsSubDir(): string
    {
        return 'Exceptions';
    }

    /**
     * @test
     */
    public function My_exceptions_are_in_family_tree()
    {
        self::assertTrue($this->getTestOfExceptionsHierarchy()->My_exceptions_are_in_family_tree());
    }

    /**
     * @test
     * @depends My_exceptions_are_in_family_tree
     */
    public function My_exceptions_are_used()
    {
        self::assertTrue(
            $this->getTestOfExceptionsHierarchy()->My_exceptions_are_used(
                $this->getExceptionsUsageRootDir(),
                $this->getExceptionClassesSkippedFromUsageTest()
            )
        );
    }

    protected function getExceptionsUsageRootDir(): string
    {
        return ''; // empty for same dir as exceptions are or upper level moving against exceptions sub dir
    }

    /**
     * @return string[]
     */
    protected function getExceptionClassesSkippedFromUsageTest(): array
    {
        return [];
    }

}
