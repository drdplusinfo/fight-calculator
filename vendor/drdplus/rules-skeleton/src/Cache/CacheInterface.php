<?php

namespace DrdPlus\RulesSkeleton\Cache;

interface CacheInterface extends CacheIdProvider
{
    public function cacheContent(string $content);

    public function getCachedContent(): string;

    public function getCacheDir(): string;

    public function isCacheValid(): bool;

    public function isInProduction(): bool;

    public function saveContentForDebug(string $content);
}
