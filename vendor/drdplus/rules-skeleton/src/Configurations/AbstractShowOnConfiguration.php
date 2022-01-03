<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

abstract class AbstractShowOnConfiguration extends AbstractConfiguration
{
    public const SHOW_ON_GATEWAY = 'show_on_gateway';
    public const SHOW_ON_HOMEPAGE = 'show_on_homepage';
    public const SHOW_ON_ROUTES = 'show_on_routes';

    protected function __construct(array $values, array $pathToHomeButton)
    {
        $values = $this->ensureShowOnGateway($values, $pathToHomeButton);
        $values = $this->ensureShowOnHomePage($values, $pathToHomeButton);
        $values = $this->ensureShowOnRoutes($values, $pathToHomeButton);
        parent::__construct($values);
    }

    protected function ensureShowOnGateway(array $values, array $pathToMenu): array
    {
        $values = $this->ensureConfigurationValue(self::SHOW_ON_GATEWAY, $values, true);
        $this->guardConfigurationValueIsSet(static::SHOW_ON_GATEWAY, $values, $pathToMenu);
        $this->guardConfigurationValueIsBoolean(static::SHOW_ON_GATEWAY, $values, $pathToMenu);
        return $values;
    }

    protected function ensureShowOnHomePage(array $values, array $pathToMenu): array
    {
        $values = $this->ensureConfigurationValue(self::SHOW_ON_HOMEPAGE, $values, true);
        $this->guardConfigurationValueIsSet(static::SHOW_ON_HOMEPAGE, $values, $pathToMenu);
        $this->guardConfigurationValueIsBoolean(static::SHOW_ON_HOMEPAGE, $values, $pathToMenu);
        return $values;
    }

    protected function ensureShowOnRoutes(array $values, array $pathToMenu): array
    {
        $values = $this->ensureConfigurationValue(self::SHOW_ON_ROUTES, $values, true);
        $this->guardConfigurationValueIsSet(static::SHOW_ON_ROUTES, $values, $pathToMenu);
        $this->guardConfigurationValueIsBoolean(static::SHOW_ON_ROUTES, $values, $pathToMenu);
        return $values;
    }

    public function isShownOnGateway(): bool
    {
        return (bool)$this->getValues()[self::SHOW_ON_GATEWAY];
    }

    public function isShownOnHomePage(): bool
    {
        return (bool)$this->getValues()[self::SHOW_ON_HOMEPAGE];
    }

    public function isShownOnRoutes(): bool
    {
        return (bool)$this->getValues()[self::SHOW_ON_ROUTES];
    }

    protected function isShown(): bool
    {
        return $this->isShownOnGateway() || $this->isShownOnHomePage() || $this->isShownOnRoutes();
    }
}
