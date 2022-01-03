<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class HomepageDetector extends StrictObject
{
    private \DrdPlus\RulesSkeleton\RouteMatchingPathProvider $pathProvider;

    public function __construct(RouteMatchingPathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    public function isHomepageRequested(): bool
    {
        try {
            $path = $this->pathProvider->getMatchingPath();
            return $path === '' || $path === '/';
        } catch (RouteNotFoundException | ResourceNotFoundException $notFoundException) {
            return false;
        }
    }
}
