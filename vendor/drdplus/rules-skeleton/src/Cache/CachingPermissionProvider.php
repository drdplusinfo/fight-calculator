<?php

namespace DrdPlus\RulesSkeleton\Cache;

interface CachingPermissionProvider
{
    public function isCachingAllowed(): bool;
}
