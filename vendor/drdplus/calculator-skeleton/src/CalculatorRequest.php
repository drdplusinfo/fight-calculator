<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use DeviceDetector\Parser\Bot;
use DrdPlus\RulesSkeleton\Environment;
use DrdPlus\RulesSkeleton\Request;

/**
 * @method static createFromGlobals(Bot $botParser, Environment $environment)
 */
class CalculatorRequest extends Request
{
    public const DELETE_HISTORY = 'delete_history';

    public function isRequestedHistoryDeletion(): bool
    {
        return (bool)$this->getValueFromPost(static::DELETE_HISTORY);
    }
}
