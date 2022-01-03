<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use Granam\Strict\Object\StrictObject;
use Symfony\Component\Config\FileLocator;

class RouterCacheDirProvider extends StrictObject
{
    private \Symfony\Component\Config\FileLocator $fileLocator;
    private string $routesFile;
    private \DrdPlus\RulesSkeleton\Cache\WebCache $routerCache;

    public function __construct(
        FileLocator $fileLocator,
        string $routesFile,
        WebCache $routerCache
    )
    {
        $this->fileLocator = $fileLocator;
        $this->routesFile = $routesFile;
        $this->routerCache = $routerCache;
    }

    public function getRouterCacheDir(): string
    {
        $routedCacheDir = $this->routerCache->getCacheDir();
        if ($this->routesFile !== '') {
            $subDirParts = [];
            foreach ((array)$this->fileLocator->locate($this->routesFile) as $routesFilepath) {
                $subDirParts[] = md5_file($routesFilepath);
            }
            if ($subDirParts) {
                $routedCacheDir .= '/' . implode('_', $subDirParts);
            }
        }
        return $routedCacheDir;
    }
}
