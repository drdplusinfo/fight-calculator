<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

class DummyUrlMatcher extends StrictObject implements UrlMatcherInterface
{
    private ?\Symfony\Component\Routing\RequestContext $context = null;

    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function match($pathinfo)
    {
        return ['path' => '/'];
    }

}
