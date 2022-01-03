<?php declare(strict_types=1);

namespace Granam\ExceptionsHierarchy;

class TestOfExceptionsHierarchy
{
    /** @var string */
    private $testedNamespace;

    /** @var string */
    private $rootNamespace;

    /** @var string */
    private $exceptionsSubDir;

    /** @var string[] */
    private $externalRootNamespaces;

    /** @var string */
    private $externalRootExceptionsSubDir;

    /**
     * @param string $testedNamespace
     * @param string $rootNamespace
     * @param string $exceptionsSubDir
     * @param string[] $externalRootNamespaces
     * @param string $externalRootExceptionsSubDir
     * @throws \Granam\ExceptionsHierarchy\Exceptions\RootNamespaceHasToBeSuperior
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExternalRootNamespaceHasToBeSuperior
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    public function __construct(
        string $testedNamespace,
        string $rootNamespace,
        string $exceptionsSubDir = 'Exceptions',
        array $externalRootNamespaces = [],
        string $externalRootExceptionsSubDir = 'Exceptions'
    )
    {
        $this->externalRootNamespaces = []; // need to match type for checkExternalRootNamespaces

        $testedNamespace = $this->normalizeNamespace($testedNamespace);
        $rootNamespace = $this->normalizeNamespace($rootNamespace);
        $externalRootNamespaces = $this->normalizeNamespaces($externalRootNamespaces);
        $this->checkRootNamespace($rootNamespace, $testedNamespace);
        $this->checkExternalRootNamespaces($externalRootNamespaces, $externalRootExceptionsSubDir, $rootNamespace);

        $this->testedNamespace = $testedNamespace;
        $this->rootNamespace = $rootNamespace;
        $this->exceptionsSubDir = $this->normalizeSubDir($exceptionsSubDir);
        $this->externalRootNamespaces = $externalRootNamespaces;
        $this->externalRootExceptionsSubDir = $externalRootExceptionsSubDir;
    }

    /**
     * @param string $namespace
     * @return string
     */
    protected function normalizeNamespace(string $namespace): string
    {
        return '\\' . trim($namespace, '\\');
    }

    /**
     * @param string[] $namespaces
     * @return string[]
     */
    protected function normalizeNamespaces(array $namespaces): array
    {
        foreach ($namespaces as &$namespace) {
            $namespace = $this->normalizeNamespace($namespace);
        }

        return $namespaces;
    }

    /**
     * @param string $rootNamespace
     * @param string $testedNamespace
     * @throws \Granam\ExceptionsHierarchy\Exceptions\RootNamespaceHasToBeSuperior
     */
    protected function checkRootNamespace(string $rootNamespace, string $testedNamespace)
    {
        if (!preg_match('~^' . preg_quote($rootNamespace, '~') . '~', $testedNamespace)) {
            throw new Exceptions\RootNamespaceHasToBeSuperior(
                "Root namespace $rootNamespace should be leading of currently tested namespace $testedNamespace"
            );
        }
    }

    /**
     * @param array $externalRootNamespaces
     * @param string $externalRootExceptionsSubDir
     * @param string $rootNamespace
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExternalRootNamespaceHasToBeSuperior
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    protected function checkExternalRootNamespaces(array $externalRootNamespaces, string $externalRootExceptionsSubDir, string $rootNamespace)
    {
        if (!$externalRootNamespaces) {
            return;
        }
        foreach ($externalRootNamespaces as $externalRootNamespace) {
            if ($rootNamespace === $externalRootNamespace) {
                throw new Exceptions\ExternalRootNamespaceHasToBeSuperior(
                    "External root namespace $externalRootNamespace should differ to local root namespace $rootNamespace"
                );
            }
            if (strpos($externalRootNamespace, $rootNamespace) === 0) {
                throw new Exceptions\ExternalRootNamespaceHasToBeSuperior(
                    "External root namespace $externalRootNamespace should not be subordinate to local root namespace $rootNamespace"
                );
            }
            $this->My_tag_interfaces_are_in_hierarchy(
                $externalRootNamespace,
                $externalRootExceptionsSubDir,
                [] // no child namespaces to check
            );
        }
    }

    protected function normalizeDir(string $dir): string
    {
        $normalizedSlash = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir);

        return rtrim($normalizedSlash, DIRECTORY_SEPARATOR);
    }

    protected function normalizeSubDir(string $subDir): string
    {
        return ltrim($this->normalizeDir($subDir), DIRECTORY_SEPARATOR);
    }

    protected function getTestedNamespace(): string
    {
        return $this->testedNamespace;
    }

    protected function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

    protected function getExceptionsSubDir(): string
    {
        return $this->exceptionsSubDir;
    }

    /**
     * @return string[]
     */
    protected function getExternalRootNamespaces(): array
    {
        return $this->externalRootNamespaces;
    }

    protected function getExternalRootExceptionsSubDir(): string
    {
        return $this->externalRootExceptionsSubDir;
    }

    /**
     * @param string $testedNamespace
     * @param string $exceptionsSubDir
     * @param string[] $childNamespaces
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    protected function My_tag_interfaces_are_in_hierarchy(string $testedNamespace, string $exceptionsSubDir, array $childNamespaces)
    {
        $exceptionInterface = $this->assembleExceptionInterfaceClass($testedNamespace, $exceptionsSubDir);
        $this->checkExceptionInterfaces($exceptionInterface);

        $runtimeInterface = $this->assembleRuntimeInterfaceClass($testedNamespace, $exceptionsSubDir);
        $this->checkRuntimeInterfaces($runtimeInterface, $exceptionInterface);

        $logicInterface = $this->assembleLogicInterfaceClass($testedNamespace, $exceptionsSubDir);
        $this->checkLogicInterfaces($logicInterface, $exceptionInterface);

        $this->checkInterfaceCollision($runtimeInterface, $logicInterface);

        $this->checkChildInterfaces($childNamespaces, $exceptionInterface, $runtimeInterface, $logicInterface);
    }

    /**
     * @param string $exceptionInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkExceptionInterfaces(string $exceptionInterface)
    {
        $externalRootExceptionInterfaces = $this->getExternalRootExceptionInterfaceClasses();
        if ($externalRootExceptionInterfaces) {
            foreach ($externalRootExceptionInterfaces as $externalRootExceptionInterface) {
                $this->checkExceptionInterface($exceptionInterface, $externalRootExceptionInterface);
            }
        } else {
            $this->checkExceptionInterface($exceptionInterface, '');
        }
    }

    /**
     * @param string $runtimeInterface
     * @param string $exceptionInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkRuntimeInterfaces(string $runtimeInterface, string $exceptionInterface)
    {
        $externalRootRuntimeInterfaces = $this->getExternalRootRuntimeInterfaceClasses();
        if ($externalRootRuntimeInterfaces) {
            foreach ($externalRootRuntimeInterfaces as $externalRootRuntimeInterface) {
                $this->checkRuntimeInterface(
                    $runtimeInterface,
                    $exceptionInterface,
                    $externalRootRuntimeInterface
                );
            }
        } else {
            $this->checkRuntimeInterface($runtimeInterface, $exceptionInterface, '');
        }
    }

    /**
     * @param string $logicInterface
     * @param string $exceptionInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkLogicInterfaces(string $logicInterface, string $exceptionInterface)
    {
        $externalRootLogicInterfaces = $this->getExternalRootLogicInterfaceClasses();
        if ($externalRootLogicInterfaces) {
            foreach ($externalRootLogicInterfaces as $externalRootLogicInterface) {
                $this->checkLogicInterface($logicInterface, $exceptionInterface, $externalRootLogicInterface);
            }
        } else {
            $this->checkLogicInterface($logicInterface, $exceptionInterface, '');
        }
    }

    /**
     * @return string[]
     */
    private function getExternalRootExceptionInterfaceClasses(): array
    {
        $classes = [];
        foreach ($this->getExternalRootNamespaces() as $externalRootNamespace) {
            $classes[] = $this->assembleExceptionInterfaceClass($externalRootNamespace, $this->getExternalRootExceptionsSubDir());
        }

        return $classes;
    }

    /**
     * @return string[]
     */
    private function getExternalRootRuntimeInterfaceClasses(): array
    {
        $classes = [];
        foreach ($this->getExternalRootNamespaces() as $externalRootNamespace) {
            $classes[] = $this->assembleRuntimeInterfaceClass($externalRootNamespace, $this->getExternalRootExceptionsSubDir());
        }

        return $classes;
    }

    /**
     * @return string[]
     */
    private function getExternalRootLogicInterfaceClasses(): array
    {
        $classes = [];
        foreach ($this->getExternalRootNamespaces() as $externalRootNamespace) {
            $classes[] = $this->assembleLogicInterfaceClass($externalRootNamespace, $this->getExternalRootExceptionsSubDir());
        }

        return $classes;
    }

    /**
     * @param string $exceptionInterface
     * @param string $externalRootExceptionInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkExceptionInterface(string $exceptionInterface, string $externalRootExceptionInterface)
    {
        if (!interface_exists($exceptionInterface)) {
            throw new Exceptions\TagInterfaceNotFound("Tag interface $exceptionInterface not found");
        }
        if ($externalRootExceptionInterface && !is_a($exceptionInterface, $externalRootExceptionInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Tag interface $exceptionInterface should extends external parent tag interface $externalRootExceptionInterface"
            );
        }
    }

    /**
     * @param string $runtimeInterface
     * @param string $exceptionInterface
     * @param string $externalRootRuntimeInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkRuntimeInterface(string $runtimeInterface, string $exceptionInterface, string $externalRootRuntimeInterface)
    {
        if (!interface_exists($runtimeInterface)) {
            throw new Exceptions\TagInterfaceNotFound("Runtime tag interface $runtimeInterface not found");
        }
        if (!is_a($runtimeInterface, $exceptionInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Runtime tag interface $runtimeInterface is not a child of $exceptionInterface"
            );
        }
        if ($externalRootRuntimeInterface && !is_a($runtimeInterface, $externalRootRuntimeInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Tag interface $runtimeInterface should extends external parent tag interface $externalRootRuntimeInterface"
            );
        }
    }

    /**
     * @param string $logicInterface
     * @param string $exceptionInterface
     * @param string $externalRootLogicInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkLogicInterface(string $logicInterface, string $exceptionInterface, string $externalRootLogicInterface)
    {
        if (!interface_exists($logicInterface)) {
            throw new Exceptions\TagInterfaceNotFound("Logic tag interface $logicInterface not found");
        }
        if (!is_a($logicInterface, $exceptionInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Logic tag interface $logicInterface is not a child of $exceptionInterface"
            );
        }
        if ($externalRootLogicInterface && !is_a($logicInterface, $externalRootLogicInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Tag interface $logicInterface should extends external parent tag interface $externalRootLogicInterface"
            );
        }
    }

    /**
     * @param string $runtimeInterface
     * @param string $logicInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    private function checkInterfaceCollision(string $runtimeInterface, string $logicInterface)
    {
        if (is_a($runtimeInterface, $logicInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Runtime tag interface $runtimeInterface can not be a logic tag"
            );
        }
        if (is_a($logicInterface, $runtimeInterface, true)) {
            throw new Exceptions\InvalidTagInterfaceHierarchy(
                "Logic tag interface $logicInterface can not be a runtime tag"
            );
        }
    }

    /**
     * @param string[] $childNamespaces
     * @param string $exceptionInterface
     * @param string $runtimeInterface
     * @param string $logicInterface
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    private function checkChildInterfaces(array $childNamespaces, string $exceptionInterface, string $runtimeInterface, string $logicInterface)
    {
        foreach ($childNamespaces as $childNamespace) {
            $childExceptionInterface = $this->assembleExceptionInterfaceClass($childNamespace, $this->getExceptionsSubDir());
            if (!is_a($childExceptionInterface, $exceptionInterface, true)) {
                throw new Exceptions\InvalidExceptionHierarchy(
                    "Tag $childExceptionInterface should be child of $exceptionInterface"
                );
            }

            $childRuntimeInterface = $this->assembleRuntimeInterfaceClass($childNamespace, $this->getExceptionsSubDir());
            if (!is_a($childRuntimeInterface, $runtimeInterface, true)) {
                throw new Exceptions\InvalidExceptionHierarchy(
                    "Tag $childRuntimeInterface should be child of $runtimeInterface"
                );
            }

            $childLogicInterface = $this->assembleLogicInterfaceClass($childNamespace, $this->getExceptionsSubDir());
            if (!is_a($childLogicInterface, $logicInterface, true)) {
                throw new Exceptions\InvalidExceptionHierarchy(
                    "Tag $childLogicInterface should be child of $logicInterface"
                );
            }
        }
    }

    /**
     * @return bool
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionClassNotFoundByAutoloader
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     * @throws \Granam\ExceptionsHierarchy\Exceptions\TagInterfaceNotFound
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidTagInterfaceHierarchy
     */
    public function My_exceptions_are_in_family_tree(): bool
    {
        $childNamespaces = [];
        $testedNamespace = $this->getTestedNamespace();
        do {
            $this->My_tag_interfaces_are_in_hierarchy($testedNamespace, $this->getExceptionsSubDir(), $childNamespaces);
            $directory = $this->getNamespaceDirectory($testedNamespace);
            foreach ($this->getCustomExceptionsFrom($directory) as $customExceptionClass) {
                $this->My_exception_exists($customExceptionClass);
                $this->My_exception_is_properly_tagged($customExceptionClass);
                $this->My_custom_exception_follows_parent($customExceptionClass);
            }
            $alreadyInRoot = $testedNamespace === $this->getRootNamespace();
            $childNamespaces[] = $testedNamespace;
            $testedNamespace = $this->extractParentNamespace($testedNamespace, $this->getExceptionsSubDir());
        } while (!$alreadyInRoot && $testedNamespace);

        return true;
    }

    protected function getNamespaceDirectory(string $namespace): string
    {
        $exceptionTag = $this->assembleExceptionInterfaceClass($namespace, $this->getExceptionsSubDir());
        $exceptionTagReflection = new \ReflectionClass($exceptionTag);
        $filename = $exceptionTagReflection->getFileName();

        return dirname($filename);
    }

    /**
     * @param string $directory
     * @return string[]
     */
    protected function getCustomExceptionsFrom(string $directory): array
    {
        $customExceptions = [];
        foreach (scandir($directory) as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
                if (preg_match('~(namespace\s+(?<namespace>(\w+(\\\)?)+)).+(class|interface)\s+(?<className>\w+)~s', $content, $matches)
                    && !in_array($matches['className'], ['Exception', 'Runtime', 'Logic'], true)
                ) {
                    $customExceptions[] = $matches['namespace'] . '\\' . $matches['className'];
                }
            }
        }

        return $customExceptions;
    }

    /**
     * @param string $exceptionClass
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionClassNotFoundByAutoloader
     */
    protected function My_exception_exists(string $exceptionClass)
    {
        if (!class_exists($exceptionClass) && !interface_exists($exceptionClass)) {
            throw new Exceptions\ExceptionClassNotFoundByAutoloader(
                "Exception class nor interface {$exceptionClass} has not been found by auto-loader."
                . ' Do you follow auto-loader expectations like PSR naming standards?'
            );
        }
    }

    /**
     * @param string $exceptionClass
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    protected function My_exception_is_properly_tagged(string $exceptionClass)
    {
        $namespace = $this->extractNamespaceFromClass($exceptionClass);
        $this->checkIfIsBaseTagged($exceptionClass, $namespace);
        $this->checkTagCollision($exceptionClass, $namespace);

        if (class_exists($exceptionClass)) {
            $this->My_exception_is_child_of_proper_base_exception($exceptionClass);
        }
    }

    /**
     * @param string $exceptionClass
     * @param string $namespace
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly
     */
    private function checkIfIsBaseTagged(string $exceptionClass, string $namespace)
    {
        $isBaseTagged = is_a($exceptionClass, $this->assembleExceptionInterfaceClass($namespace), true);
        if (!$isBaseTagged) {
            throw new Exceptions\ExceptionIsNotTaggedProperly(
                (class_exists($exceptionClass) ? 'Class' : 'Tag interface')
                . " $exceptionClass has to be tagged by Exception interface"
            );
        }
    }

    /**
     * @param string $exceptionClass
     * @param string $namespace
     * @throws \Granam\ExceptionsHierarchy\Exceptions\ExceptionIsNotTaggedProperly
     */
    private function checkTagCollision(string $exceptionClass, string $namespace)
    {
        $isRuntime = $this->isRuntime($exceptionClass, $namespace);
        $isLogic = $this->isLogic($exceptionClass, $namespace);
        if ($isRuntime && $isLogic) {
            throw new Exceptions\ExceptionIsNotTaggedProperly(
                'Exception ' . (class_exists($exceptionClass) ? 'class' : 'interface')
                . " $exceptionClass can not be tagged by Runtime interface and Logic interface at the same time"
            );
        }
        if (!$isRuntime && !$isLogic) {
            throw new Exceptions\ExceptionIsNotTaggedProperly(
                'Exception ' . (class_exists($exceptionClass) ? 'class' : 'interface')
                . " $exceptionClass is not tagged by Runtime interface or even Logic interface"
            );
        }
    }

    private function isRuntime(string $exceptionClass, string $namespace): bool
    {
        return is_a($exceptionClass, $this->assembleRuntimeInterfaceClass($namespace), true);
    }

    private function isLogic(string $exceptionClass, string $namespace): bool
    {
        return is_a($exceptionClass, $this->assembleLogicInterfaceClass($namespace), true);
    }

    /**
     * @param string $exceptionClass
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    protected function My_exception_is_child_of_proper_base_exception(string $exceptionClass)
    {
        if (!is_a($exceptionClass, \Exception::class, true)) {
            throw new Exceptions\InvalidExceptionHierarchy("$exceptionClass should be child of \\Exception");
        }

        $namespace = $this->extractNamespaceFromClass($exceptionClass);
        if ($this->isRuntime($exceptionClass, $namespace)) {
            if (!is_a($exceptionClass, \RuntimeException::class, true)) {
                throw new Exceptions\InvalidExceptionHierarchy("$exceptionClass should be child of \\RuntimeException");
            }
        } else {
            if (!is_a($exceptionClass, \LogicException::class, true)) {
                throw new Exceptions\InvalidExceptionHierarchy("$exceptionClass should be child of \\LogicException");
            }
        }
    }

    protected function extractNamespaceFromClass(string $className): string
    {
        return $this->normalizeNamespace(preg_replace('~\w+$~', '', $className));
    }

    protected function assembleExceptionInterfaceClass(string $namespace, string $subDir = ''): string
    {
        return $this->assembleClassName($namespace, $subDir, 'Exception');
    }

    protected function assembleRuntimeInterfaceClass(string $namespace, string $subDir = ''): string
    {
        return $this->assembleClassName($namespace, $subDir, 'Runtime');
    }

    protected function assembleLogicInterfaceClass(string $namespace, string $subDir = ''): string
    {
        return $this->assembleClassName($namespace, $subDir, 'Logic');
    }

    private function assembleClassName(string $namespace, string $subDir, string $className): string
    {
        $namespace = $this->normalizeNamespace($namespace);

        return
            ($namespace === '\\' ? '' : $namespace)
            . ($subDir ?
                ('\\' . $subDir)
                : ''
            )
            . '\\' . $className;
    }

    /**
     * @param string $childNamespace
     * @param string $subDirToStrip
     * @return bool|string
     */
    protected function extractParentNamespace(string $childNamespace, string $subDirToStrip = '')
    {
        if ($childNamespace === '\\') {
            return false;
        }
        if ($subDirToStrip) {
            $childNamespace = preg_replace(
                '~[\\\]' . preg_quote($subDirToStrip, '~') . '[\\\]?$~',
                '',
                $childNamespace
            );
        }
        $roughParentNamespace = preg_replace('~[\\\]\w+[\\\]?$~', '', $childNamespace);

        return $this->normalizeNamespace($roughParentNamespace);
    }

    /**
     * @param string $customExceptionClass
     * @throws \Granam\ExceptionsHierarchy\Exceptions\InvalidExceptionHierarchy
     */
    protected function My_custom_exception_follows_parent(string $customExceptionClass)
    {
        $closestParent = $this->getClosestParentOfSameName($customExceptionClass);
        if ($closestParent && !is_a($customExceptionClass, $closestParent, true)) {
            throw new Exceptions\InvalidExceptionHierarchy(
                "Exception {$customExceptionClass} should extends parent {$closestParent}"
            );
        }
    }

    /**
     * @param string $className
     * @return bool|string
     */
    protected function getClosestParentOfSameName(string $className)
    {
        $baseName = $this->extractClassBaseName($className);
        $namespace = $this->extractNamespaceFromClass($className);

        while (($namespace = $this->extractParentNamespace($namespace)) !== false) {
            $soughtParentClass = $this->assembleClassName($namespace, $this->getExceptionsSubDir(), $baseName);
            if (class_exists($soughtParentClass)) {
                return $soughtParentClass;
            }
        }

        foreach ($this->getExternalRootNamespaces() as $externalRootNamespace) {
            do {
                $soughtParentClass = $externalRootNamespace . '\\' . $baseName;
                if (class_exists($soughtParentClass)) {
                    return $soughtParentClass;
                }
            } while (($externalRootNamespace = $this->extractParentNamespace($externalRootNamespace)) !== false);
        }

        return false;
    }

    protected function extractClassBaseName(string $className): string
    {
        preg_match('~(?<basename>\w+)$~', $className, $matches);

        return $matches['basename'];
    }

    /**
     * @param string $exceptionsUsageRootDir
     * @param string[] exceptionClassesSkippedFromUsageTest
     * @return bool
     * @throws \Granam\ExceptionsHierarchy\Exceptions\UnusedException
     * @throws \Granam\ExceptionsHierarchy\Exceptions\FolderCanNotBeRead
     */
    public function My_exceptions_are_used(string $exceptionsUsageRootDir, array $exceptionClassesSkippedFromUsageTest): bool
    {
        if ($exceptionsUsageRootDir !== '') {
            $exceptionsUsageRootDir = $this->normalizeDir($exceptionsUsageRootDir);
        } else {
            $exceptionsUsageRootDir = $this->getNamespaceDirectory($this->getRootNamespace());
            if ($this->getExceptionsSubDir()) {
                $exceptionsUsageRootDir = $this->normalizeDir(
                    preg_replace('~' . preg_quote($this->getExceptionsSubDir(), '~') . '$~', '', $exceptionsUsageRootDir)
                );
            }
        }
        $exceptionClassesSkippedFromUsageTest = array_map(
            static function ($skippedExceptionClass) {
                return ltrim($skippedExceptionClass, '\\');
            },
            $exceptionClassesSkippedFromUsageTest
        );
        $result = true;
        $testedNamespace = $this->getTestedNamespace();
        do {
            $directory = $this->getNamespaceDirectory($testedNamespace);
            foreach ($this->getCustomExceptionsFrom($directory) as $customExceptionClass) {
                if (!in_array($customExceptionClass, $exceptionClassesSkippedFromUsageTest, true)) {
                    $result = $this->My_exception_is_used($customExceptionClass, $exceptionsUsageRootDir);
                }
            }
            $alreadyInRoot = $testedNamespace === $this->getRootNamespace();
            $testedNamespace = $this->extractParentNamespace($testedNamespace, $this->getExceptionsSubDir());
        } while (!$alreadyInRoot && $testedNamespace);

        return $result;
    }

    /**
     * @param string $exceptionClass
     * @param string $exceptionsUsageRootDir
     * @return bool
     * @throws \Granam\ExceptionsHierarchy\Exceptions\UnusedException
     * @throws \Granam\ExceptionsHierarchy\Exceptions\FolderCanNotBeRead
     */
    protected function My_exception_is_used(string $exceptionClass, string $exceptionsUsageRootDir): bool
    {
        $exceptionClassBasename = preg_quote($this->extractClassBaseName($exceptionClass), '~');
        $searchForUsage = static function (string $dirToSearch) use (&$searchForUsage, $exceptionClassBasename) {
            foreach (new \DirectoryIterator($dirToSearch) as $folder) {
                if ($folder->isDot()) {
                    continue;
                }
                if ($folder->isFile()) {
                    if ($folder->isReadable()) {
                        $content = file_get_contents($folder->getRealPath());
                        if (preg_match('~(throw\s+new|extends|implements[\s\w\\\,]*)\s+[\w\\\]*' . $exceptionClassBasename . '[^\w\\\]+~i', $content)) {
                            return true;
                        }
                    }
                } elseif ($folder->isDir()) {
                    if ($searchForUsage($folder->getPathname())) {
                        return true;
                    }
                }
            }

            return false;
        };
        try {
            if ($searchForUsage($exceptionsUsageRootDir)) {
                return true;
            }
        } catch (\UnexpectedValueException $unexpectedValueException) {
            throw new Exceptions\FolderCanNotBeRead($unexpectedValueException->getMessage());
        }

        throw new Exceptions\UnusedException("Exception {$exceptionClass} is unused.");
    }

}
