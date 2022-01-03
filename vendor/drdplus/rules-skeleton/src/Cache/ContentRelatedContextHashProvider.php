<?php

namespace DrdPlus\RulesSkeleton\Cache;

interface ContentRelatedContextHashProvider
{
    public function getContextHash(): string;
}
