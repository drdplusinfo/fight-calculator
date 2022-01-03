<?php

namespace DrdPlus\RulesSkeleton\Cache;

interface CacheIdProvider
{
    public function getCacheId(): string;
}
