<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\RouteMatchingPathProvider;

class RoutedDirs extends Dirs
{
    private \DrdPlus\RulesSkeleton\RouteMatchingPathProvider $pathProvider;
    private ?string $relativeWebRoot = null;

    public function __construct(string $projectRoot, RouteMatchingPathProvider $pathProvider)
    {
        parent::__construct($projectRoot);
        $this->pathProvider = $pathProvider;
    }

    public function getWebRoot(): string
    {
        $webRoot = parent::getWebRoot();
        if ($this->getRelativeWebRoot() !== '') {
            $webRoot .= '/' . $this->getRelativeWebRoot();
        }
        return $webRoot;
    }

    protected function getRelativeWebRoot(): string
    {
        if ($this->relativeWebRoot === null) {
            $this->relativeWebRoot = $this->unifyRelativePath($this->pathProvider->getMatchingPath());
        }
        return $this->relativeWebRoot;
    }

    protected function unifyRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        return trim($path, '/');
    }

}
