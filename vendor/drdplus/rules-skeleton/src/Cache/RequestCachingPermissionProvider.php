<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use DrdPlus\RulesSkeleton\Request;
use Granam\Strict\Object\StrictObject;

class RequestCachingPermissionProvider extends StrictObject implements CachingPermissionProvider
{
    private \DrdPlus\RulesSkeleton\Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function isCachingAllowed(): bool
    {
        $cacheParameter = $this->request->getValue(Request::CACHE) ?? '';

        return ($cacheParameter === '' || !in_array($cacheParameter, [Request::DISABLE, 'disabled', '0'], true));
    }
}
