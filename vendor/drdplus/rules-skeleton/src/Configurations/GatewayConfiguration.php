<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

class GatewayConfiguration extends AbstractConfiguration
{
    public const PROTECTED_ACCESS = 'protected_access';

    public function __construct(array $values, array $pathToMenu)
    {
        $this->guardProtectedAccessIsSet($values, $pathToMenu);
        parent::__construct($values);
    }

    protected function guardProtectedAccessIsSet(array $values, array $pathToMenu): void
    {
        $this->guardConfigurationValueIsSet(static::PROTECTED_ACCESS, $values, $pathToMenu);
        $this->guardConfigurationValueIsBoolean(static::PROTECTED_ACCESS, $values, $pathToMenu);
    }

    public function hasProtectedAccess(): bool
    {
        return (bool)$this->getValues()[self::PROTECTED_ACCESS];
    }
}
