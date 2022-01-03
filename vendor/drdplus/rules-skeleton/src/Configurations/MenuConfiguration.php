<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

class MenuConfiguration extends AbstractShowOnConfiguration
{
    public const POSITION_FIXED = 'position_fixed';
    public const ITEMS = 'items';
    public const HOME_BUTTON = 'home_button';

    // DEPRECATED
    public const SHOW_HOME_BUTTON_ON_HOMEPAGE = 'show_home_button_on_homepage';
    public const SHOW_HOME_BUTTON_ON_ROUTES = 'show_home_button_on_routes';
    public const HOME_BUTTON_TARGET = 'home_button_target';

    private array $pathToMenu;
    private \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration $homeButtonConfiguration;

    public function __construct(array $values, array $pathToMenu)
    {
        $this->pathToMenu = $pathToMenu;

        $values = $this->ensureFixedMenuPosition($values);
        $this->guardItemsAreArrayOrNothing($values);

        $this->homeButtonConfiguration = $this->createHomeButtonConfiguration($values);

        parent::__construct($values, $pathToMenu);
    }

    private function createHomeButtonConfiguration(array $values): HomeButtonConfiguration
    {
        $values = $this->upgradeShowOfHomeButtonOnHomepageToNewWay($values);
        $values = $this->upgradeShowOfHomeButtonOnRoutesToNewWay($values);
        $values = $this->upgradeHomeButtonTargetToNewWay($values);

        $this->guardHomeButtonConfigurationExists($values);
        $pathToHomeButton = array_merge($this->pathToMenu, [static::HOME_BUTTON]);
        return new HomeButtonConfiguration($values[static::HOME_BUTTON], $pathToHomeButton);
    }

    private function guardHomeButtonConfigurationExists(array $values)
    {
        $this->guardConfigurationValueIsSet(static::HOME_BUTTON, $values, $this->pathToMenu);
        $this->guardConfigurationValueIsObject(static::HOME_BUTTON, $values, $this->pathToMenu);
    }

    private function ensureFixedMenuPosition(array $values): array
    {
        $values = $this->ensureConfigurationValue(static::POSITION_FIXED, $values, false);
        $this->guardConfigurationValueIsSet(static::POSITION_FIXED, $values, $this->pathToMenu);
        $this->guardConfigurationValueIsBoolean(static::POSITION_FIXED, $values, $this->pathToMenu);
        return $values;
    }

    private function upgradeShowOfHomeButtonOnHomepageToNewWay(array $values): array
    {
        return $this->diveConfigurationStructure(
            static::SHOW_HOME_BUTTON_ON_HOMEPAGE,
            static::HOME_BUTTON,
            HomeButtonConfiguration::SHOW_ON_HOMEPAGE,
            $values
        );
    }

    private function upgradeShowOfHomeButtonOnRoutesToNewWay(array $values): array
    {
        return $this->diveConfigurationStructure(
            static::SHOW_HOME_BUTTON_ON_ROUTES,
            static::HOME_BUTTON,
            HomeButtonConfiguration::SHOW_ON_ROUTES,
            $values
        );
    }

    private function upgradeHomeButtonTargetToNewWay(array $values): array
    {
        return $this->diveConfigurationStructure(
            static::HOME_BUTTON_TARGET,
            static::HOME_BUTTON,
            HomeButtonConfiguration::TARGET,
            $values
        );
    }

    private function guardItemsAreArrayOrNothing(array $values)
    {
        if (!array_key_exists(static::ITEMS, $values)) {
            return;
        }
        $this->guardConfigurationValueIsObject(static::ITEMS, $values, $this->pathToMenu);
    }

    public function isPositionFixed(): bool
    {
        return (bool)$this->getValues()[static::POSITION_FIXED];
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnHomePage instead
     */
    public function isShowHomeButtonOnHomepage(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnHomePage instead',
                __FUNCTION__
            ),
            E_USER_DEPRECATED
        );
        return $this->getHomeButtonConfiguration()->isShownOnHomePage();
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnRoutes instead
     */
    public function isShowHomeButtonOnRoutes(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnRoutes instead',
                __FUNCTION__
            ),
            E_USER_DEPRECATED
        );
        return $this->getHomeButtonConfiguration()->isShownOnRoutes();
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::getTarget instead
     */
    public function getHomeButtonTarget(): string
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::getTarget instead',
                __FUNCTION__
            ),
            E_USER_DEPRECATED
        );
        return $this->getHomeButtonConfiguration()->getTarget();
    }

    public function getHomeButtonConfiguration(): HomeButtonConfiguration
    {
        return $this->homeButtonConfiguration;
    }

    /**
     * @return string[]
     */
    public function getItems(): array
    {
        return $this->getValues()[static::ITEMS] ?? [];
    }

    public function isShownOnGateway(): bool
    {
        return parent::isShownOnGateway()
            && ($this->hasSomeItems() || $this->getHomeButtonConfiguration()->isShownOnGateway());
    }

    private function hasSomeItems(): bool
    {
        return count($this->getItems()) > 0;
    }

    public function isShownOnHomepage(): bool
    {
        return parent::isShownOnHomePage()
            && ($this->hasSomeItems() || $this->getHomeButtonConfiguration()->isShownOnHomePage());
    }

    public function isShownOnRoutes(): bool
    {
        return parent::isShownOnRoutes()
            && ($this->hasSomeItems() || $this->getHomeButtonConfiguration()->isShownOnRoutes());
    }
}
