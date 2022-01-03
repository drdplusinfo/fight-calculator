<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Configurations\GatewayConfiguration;
use Granam\Strict\Object\StrictObject;

class Ticket extends StrictObject
{
    private \DrdPlus\RulesSkeleton\Configurations\GatewayConfiguration $gatewayConfiguration;
    private \DrdPlus\RulesSkeleton\UsagePolicy $usagePolicy;

    public function __construct(
        GatewayConfiguration $gatewayConfiguration,
        UsagePolicy $usagePolicy
    )
    {
        $this->gatewayConfiguration = $gatewayConfiguration;
        $this->usagePolicy = $usagePolicy;
    }

    public function canPassIn(): bool
    {
        if (!$this->gatewayConfiguration->hasProtectedAccess()) {
            return true; // free for all
        }
        if ($this->usagePolicy->isVisitorBot()) {
            return true; // bots are welcome
        }
        if ($this->usagePolicy->hasVisitorConfirmedOwnership()) {
            return true; // already confirmed owner
        }
        if ($this->usagePolicy->isVisitorUsingValidTrial()) {
            return true; // everyone can try it
        }
        return false; // access is protected and visitor does not authorized self
    }
}
