<?php declare(strict_types=1);

namespace Granam\WebContentBuilder;

use Granam\AssetsVersion\AssetsVersionInjector;
use Granam\Strict\Object\StrictObject;

class AssetsVersion extends StrictObject
{
    /** @var bool */
    private $scanDirsForCss;
    /** @var bool */
    private $scanDirsForHtml;
    /** @var bool */
    private $scanDirsForMarkdown;
    /** @var AssetsVersionInjector */
    private $assetsVersionInjector;

    public function __construct(bool $scanDirsForCss = null, bool $scanDirsForHtml = null, bool $scanDirsForMd = null)
    {
        if ($scanDirsForCss === null && $scanDirsForHtml === null && $scanDirsForMd === null) { // default is to scan for everything
            $this->scanDirsForCss = true;
            $this->scanDirsForHtml = true;
            $this->scanDirsForMarkdown = true;
        } else { // only selected file types will be searched
            $this->scanDirsForCss = $scanDirsForCss ?? false;
            $this->scanDirsForHtml = $scanDirsForHtml ?? false;
            $this->scanDirsForMarkdown = $scanDirsForMd ?? false;
        }
        $this->assetsVersionInjector = new AssetsVersionInjector(AssetsVersionInjector::PROBLEM_REPORT_AS_WARNING);
    }

    /**
     * @param string $documentRootDir
     * @param array $dirsToScan
     * @param array $excludeDirs
     * @param array $filesToEdit
     * @param bool $dryRun Want just count of files to change, without changing them in fact?
     * @return array list of changed files
     */
    public function addVersionsToAssetLinks(
        string $documentRootDir,
        array $dirsToScan,
        array $excludeDirs,
        array $filesToEdit,
        bool $dryRun
    ): array
    {
        $changedFiles = [];
        $documentRootDir = rtrim($documentRootDir, '/');
        $confirmedFilesToEdit = $this->getConfirmedFilesToEdit($dirsToScan, $excludeDirs, $filesToEdit);
        foreach ($confirmedFilesToEdit as $confirmedFileToEdit) {
            $content = file_get_contents($confirmedFileToEdit);
            if ($content === false) {
                trigger_error("File {$confirmedFileToEdit} is not readable, has to skip it", E_USER_WARNING);
                continue;
            }
            if ($content === '') {
                trigger_error("File {$confirmedFileToEdit} is empty", E_USER_WARNING);
                continue;
            }
            $replacedContent = $this->addVersionsToAssetLinksInContent($content, $documentRootDir);
            if ($replacedContent === $content) {
                continue;
            }
            if ($dryRun) {
                $changedFiles[] = $confirmedFileToEdit;
                continue;
            }
            if (!file_put_contents($confirmedFileToEdit, $replacedContent)) {
                trigger_error("Can not write to {$confirmedFileToEdit}", E_USER_WARNING);
                continue;
            }
            $changedFiles[] = $confirmedFileToEdit;
        }

        return $changedFiles;
    }

    private function getConfirmedFilesToEdit(array $dirsToScan, array $excludeDirs, array $filesToEdit): array
    {
        $confirmedFilesToEdit = [];
        $wantedFileExtensions = [];
        if ($this->scanDirsForCss) {
            $wantedFileExtensions[] = 'css';
        }
        if ($this->scanDirsForHtml) {
            $wantedFileExtensions[] = 'html';
            $wantedFileExtensions[] = 'htm';
        }
        if ($this->scanDirsForMarkdown) {
            $wantedFileExtensions[] = 'md';
        }
        $excludeDirs = $this->unifyFolderNames($excludeDirs);
        $wantedFileExtensionsRegexp = '(' . implode('|', $wantedFileExtensions) . ')';
        foreach ($dirsToScan as $dirToScan) {
            $directoryIterator = new \RecursiveDirectoryIterator(
                $dirToScan,
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                | \RecursiveDirectoryIterator::SKIP_DOTS
                | \RecursiveDirectoryIterator::UNIX_PATHS
                | \RecursiveDirectoryIterator::KEY_AS_FILENAME
                | \RecursiveDirectoryIterator::CURRENT_AS_SELF
            );
            /** @var \FilesystemIterator $folder */
            foreach (new \RecursiveIteratorIterator($directoryIterator) as $folderName => $folder) {
                $pathName = $folder->getPathname();
                $dirPath = dirname($pathName);
                if (preg_match('~/vendor($|/.+)~', $dirPath)) {
                    continue;
                }
                foreach ($excludeDirs as $excludeDir) {
                    if ($dirPath === $excludeDir || \strpos($dirPath, $excludeDir . '/') === 0) {
                        continue 2; // next folder
                    }
                }
                if (preg_match('~[.]' . $wantedFileExtensionsRegexp . '$~', $folderName)) {
                    $confirmedFilesToEdit[] = $pathName;
                }
            }
        }
        foreach ($filesToEdit as $fileToEdit) {
            if (!is_file($fileToEdit)) {
                trigger_error("A file does not exists: {$fileToEdit}", E_USER_WARNING);
                continue;
            }
            if (!is_readable($fileToEdit)) {
                trigger_error("A file can not be read: {$fileToEdit}", E_USER_WARNING);
                continue;
            }
            $confirmedFilesToEdit[] = $fileToEdit;
        }

        return array_unique($confirmedFilesToEdit);
    }

    private function unifyFolderNames(array $folders): array
    {
        return array_map(static function (string $folder) {
            return rtrim(str_replace('\\', '/', $folder), '/');
        }, $folders);
    }

    private function addVersionsToAssetLinksInContent(string $content, string $documentRootDir): string
    {
        return $this->assetsVersionInjector->addVersionsToAssetLinks($content, $documentRootDir);
    }
}
