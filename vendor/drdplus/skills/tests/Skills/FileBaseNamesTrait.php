<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

trait FileBaseNamesTrait
{
    protected function getNamespace(): string
    {
        return preg_replace('~[\\\]Tests([\\\].+)[\\\]\w+$~', '$1', static::class);
    }

    protected function getFileBaseNames(string $namespace): array
    {
        $sutNamespaceToDirRelativePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $sutNamespaceToDirRelativePath = preg_replace('~^DrdPlus/Skills/~', 'src/', $sutNamespaceToDirRelativePath);

        $sutDir = rtrim($this->getProjectRootDir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . rtrim($sutNamespaceToDirRelativePath, DIRECTORY_SEPARATOR);

        self::assertDirectoryExists($sutDir);

        $files = scandir($sutDir, SCANDIR_SORT_NONE);

        return array_filter($files, static fn($filename) => $filename !== '.' && $filename !== '..');
    }

    private function getProjectRootDir(): string
    {
        return PROJECT_ROOT_DIR;
    }
}
