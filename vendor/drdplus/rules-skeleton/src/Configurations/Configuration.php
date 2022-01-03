<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Configurations;

use Granam\YamlReader\YamlFileReader;

class Configuration extends AbstractConfiguration
{
    public const CONFIG_LOCAL_YML = 'config.local.yml';
    public const CONFIG_DISTRIBUTION_YML = 'config.distribution.yml';

    public static function canCreateFromYml(Dirs $dirs): bool
    {
        return is_file($dirs->getProjectRoot() . '/' . static::CONFIG_DISTRIBUTION_YML)
            && is_readable($dirs->getProjectRoot() . '/' . static::CONFIG_DISTRIBUTION_YML);
    }

    public static function createFromYml(Dirs $dirs): Configuration
    {
        $globalConfig = new YamlFileReader($dirs->getProjectRoot() . '/' . static::CONFIG_DISTRIBUTION_YML);
        $config = $globalConfig->getValues();
        $localConfigFile = $dirs->getProjectRoot() . '/' . static::CONFIG_LOCAL_YML;
        if (file_exists($localConfigFile)) {
            $localConfig = new YamlFileReader($dirs->getProjectRoot() . '/' . static::CONFIG_LOCAL_YML);
            $config = array_replace_recursive($config, $localConfig->getValues());
        }

        return new static($dirs, $config);
    }

    // moved from web and deprecated by old way
    public const MENU_POSITION_FIXED = 'menu_position_fixed';
    public const SHOW_HOME_BUTTON_ON_HOMEPAGE = 'show_home_button_on_homepage';
    public const SHOW_HOME_BUTTON_ON_ROUTES = 'show_home_button_on_routes';
    public const HOME_BUTTON_TARGET = 'home_button_target';
    public const PROTECTED_ACCESS = 'protected_access';

    // web
    public const WEB = 'web';
    public const NAME = 'name';
    public const TITLE_SMILEY = 'title_smiley';
    public const ESHOP_URL = 'eshop_url';
    public const FAVICON = 'favicon';
    public const DEFAULT_PUBLIC_TO_LOCAL_URL_PART_REGEXP = 'default_public_to_local_url_part_regexp';
    public const DEFAULT_PUBLIC_TO_LOCAL_URL_PART_REPLACEMENT = 'default_public_to_local_url_part_replacement';
    public const MENU = 'menu';
    public const GATEWAY = 'gateway';
    // google
    public const GOOGLE = 'google';
    public const ANALYTICS_ID = 'analytics_id';
    // application
    public const APPLICATION = 'application';
    public const YAML_FILE_WITH_ROUTES = 'yaml_file_with_routes';
    public const DEFAULT_YAML_FILE_WITH_ROUTES = 'default_yaml_file_with_routes';

    private Dirs $dirs;
    private MenuConfiguration $menuConfiguration;
    private GatewayConfiguration $gatewayConfiguration;

    public function __construct(Dirs $dirs, array $values)
    {
        $this->dirs = $dirs;
        $this->menuConfiguration = $this->createMenuConfiguration($values);
        $this->gatewayConfiguration = $this->createGatewayConfiguration($values);

        $this->guardValidGoogleAnalyticsId($values);
        $this->guardNonEmptyWebName($values);
        $this->guardSetTitleSmiley($values);
        $this->guardValidEshopUrl($values);
        $this->guardValidFaviconUrl($values);

        parent::__construct($values);
    }

    // MENU

    protected function createMenuConfiguration(array $values): MenuConfiguration
    {
        $values = $this->upgradeFixedMenuPositionToNewWay($values);
        $values = $this->upgradeShowHomeButtonOnHomepageToNewWay($values);
        $values = $this->upgradeShowHomeButtonOnRoutesToNewWay($values);
        $values = $this->upgradeHomeButtonTargetToNewWay($values);

        $this->guardMenuConfigurationExists($values);
        return new MenuConfiguration($values[static::WEB][static::MENU], [static::WEB, static::MENU]);
    }

    protected function createGatewayConfiguration(array $values): GatewayConfiguration
    {
        $values = $this->upgradeProtectedAccessToNewWay($values);

        $this->guardGatewayConfigurationExists($values);
        return new GatewayConfiguration($values[static::WEB][static::GATEWAY], [static::WEB, static::GATEWAY]);
    }

    private function guardMenuConfigurationExists(array $values)
    {
        if (!is_array($values[static::WEB][static::MENU] ?? null)) {
            throw new Exceptions\InvalidConfiguration(
                sprintf("Missing configuration '%s'", implode('.', [static::WEB, static::MENU]))
            );
        }
    }

    private function guardGatewayConfigurationExists(array $values)
    {
        if (!is_array($values[static::WEB][static::GATEWAY] ?? null)) {
            throw new Exceptions\InvalidConfiguration(
                sprintf("Missing configuration '%s'", implode('.', [static::WEB, static::GATEWAY]))
            );
        }
    }

    protected function upgradeFixedMenuPositionToNewWay(array $values): array
    {
        $values[static::WEB] = $this->diveConfigurationStructure(
            static::MENU_POSITION_FIXED,
            static::MENU,
            MenuConfiguration::POSITION_FIXED,
            $values[static::WEB]
        );
        return $values;
    }

    protected function upgradeShowHomeButtonOnHomepageToNewWay(array $values): array
    {
        $values[static::WEB] = $this->diveConfigurationStructure(
            static::SHOW_HOME_BUTTON_ON_HOMEPAGE,
            static::MENU,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_HOMEPAGE,
            $values[static::WEB]
        );
        return $values;
    }

    protected function upgradeShowHomeButtonOnRoutesToNewWay(array $values): array
    {
        $values[static::WEB] = $this->diveConfigurationStructure(
            static::SHOW_HOME_BUTTON_ON_ROUTES,
            static::MENU,
            MenuConfiguration::SHOW_HOME_BUTTON_ON_ROUTES,
            $values[static::WEB]
        );
        return $values;
    }

    protected function upgradeHomeButtonTargetToNewWay(array $values): array
    {
        $values[static::WEB] = $this->diveConfigurationStructure(
            static::HOME_BUTTON_TARGET,
            static::MENU,
            MenuConfiguration::HOME_BUTTON_TARGET,
            $values[static::WEB]
        );
        return $values;
    }

    private function upgradeProtectedAccessToNewWay(array $values): array
    {
        $values[static::WEB] = $this->diveConfigurationStructure(
            static::PROTECTED_ACCESS,
            static::GATEWAY,
            GatewayConfiguration::PROTECTED_ACCESS,
            $values[static::WEB]
        );
        return $values;
    }

    // WEB

    /**
     * @param array $values
     * @throws \DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidGoogleAnalyticsId
     */
    private function guardValidGoogleAnalyticsId(array $values): void
    {
        if (!preg_match('~^UA-121206931-\d+$~', $values[static::GOOGLE][static::ANALYTICS_ID] ?? '')) {
            throw new Exceptions\InvalidGoogleAnalyticsId(
                sprintf(
                    'Expected something like UA-121206931-1 in configuration %s.%s, got %s',
                    static::GOOGLE,
                    static::ANALYTICS_ID,
                    $values[static::GOOGLE][static::ANALYTICS_ID] ?? 'nothing'
                )
            );
        }
    }

    /**
     * @param array $values
     * @throws \DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidConfiguration
     */
    private function guardNonEmptyWebName(array $values): void
    {
        if (($values[static::WEB][static::NAME] ?? '') === '') {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    'Expected some web name in configuration %s.%s',
                    static::WEB,
                    static::NAME
                )
            );
        }
    }

    /**
     * @param array $values
     * @throws \DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidConfiguration
     */
    private function guardSetTitleSmiley(array $values): void
    {
        if (!array_key_exists(static::TITLE_SMILEY, $values[static::WEB])) {
            throw new Exceptions\InvalidConfiguration(
                sprintf(
                    'Title smiley should be set in configuration %s.%s, even if just an empty string',
                    static::WEB,
                    static::TITLE_SMILEY
                )
            );
        }
    }

    /**
     * @param array $values
     * @throws \DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidEshopUrl
     */
    private function guardValidEshopUrl(array $values): void
    {
        if (!filter_var($values[static::WEB][static::ESHOP_URL] ?? '', FILTER_VALIDATE_URL)
            && $this->getGatewayConfiguration()->hasProtectedAccess()
        ) {
            throw new Exceptions\InvalidEshopUrl(
                sprintf(
                    'Given e-shop URL is not valid, expected some URL in configuration %s.%s, got %s',
                    static::WEB,
                    static::ESHOP_URL,
                    $values[static::WEB][static::ESHOP_URL] ?? 'nothing'
                )
            );
        }
    }

    private function guardValidFaviconUrl(array $values): void
    {
        $favicon = $values[static::WEB][static::FAVICON] ?? null;
        if ($favicon === null) {
            return;
        }
        if (!filter_var($favicon, FILTER_VALIDATE_URL)
            && !file_exists($this->getDirs()->getProjectRoot() . '/' . ltrim($favicon, '/'))
        ) {
            throw new Exceptions\GivenFaviconHasNotBeenFound("Favicon $favicon is not an URL neither readable file");
        }
    }

    public function getMenuConfiguration(): MenuConfiguration
    {
        return $this->menuConfiguration;
    }

    public function getGatewayConfiguration(): GatewayConfiguration
    {
        return $this->gatewayConfiguration;
    }

    public function getDirs(): Dirs
    {
        return $this->dirs;
    }

    public function getGoogleAnalyticsId(): string
    {
        return $this->getValues()[static::GOOGLE][static::ANALYTICS_ID];
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\MenuConfiguration::isPositionFixed instead
     */
    public function isMenuPositionFixed(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use %s instead',
                static::class . '::' . __FUNCTION__,
                MenuConfiguration::class . '::' . 'isPositionFixed'
            ),
            E_USER_DEPRECATED
        );
        return $this->getMenuConfiguration()->isPositionFixed();
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnHomePage instead
     */
    public function isShowHomeButtonOnHomepage(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use %s instead',
                static::class . '::' . __FUNCTION__,
                HomeButtonConfiguration::class . '::' . 'showOnHomePage'
            ),
            E_USER_DEPRECATED
        );
        return $this->getMenuConfiguration()->getHomeButtonConfiguration()->isShownOnHomePage();
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::showOnRoutes instead
     */
    public function isShowHomeButtonOnRoutes(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use %s instead',
                static::class . '::' . __FUNCTION__,
                HomeButtonConfiguration::class . '::' . 'showOnRoutes'
            ),
            E_USER_DEPRECATED
        );
        return $this->getMenuConfiguration()->getHomeButtonConfiguration()->isShownOnRoutes();
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration::getTarget instead
     */
    public function getHomeButtonTarget(): string
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use %s instead',
                static::class . '::' . __FUNCTION__,
                HomeButtonConfiguration::class . '::' . 'getTarget'
            ),
            E_USER_DEPRECATED
        );
        return $this->getMenuConfiguration()->getHomeButtonConfiguration()->getTarget();
    }

    public function getWebName(): string
    {
        return $this->getValues()[static::WEB][static::NAME];
    }

    public function getTitleSmiley(): string
    {
        return (string)$this->getValues()[static::WEB][static::TITLE_SMILEY];
    }

    /**
     * @deprecated
     * Use \DrdPlus\RulesSkeleton\Configurations\GatewayConfiguration::hasProtectedAccess instead
     */
    public function hasProtectedAccess(): bool
    {
        trigger_error(
            sprintf(
                '%s is deprecated, use %s instead',
                static::class . '::' . __FUNCTION__,
                GatewayConfiguration::class . '::' . 'hasProtectedAccess'
            ),
            E_USER_DEPRECATED
        );
        return $this->getGatewayConfiguration()->hasProtectedAccess();
    }

    public function getEshopUrl(): string
    {
        return $this->getValues()[self::WEB][self::ESHOP_URL] ?? '';
    }

    public function getFavicon(): string
    {
        return $this->getValues()[static::WEB][static::FAVICON] ?? '';
    }

    public function getYamlFileWithRoutes(): string
    {
        return $this->getValues()[static::APPLICATION][static::YAML_FILE_WITH_ROUTES] ?? '';
    }

    public function getDefaultYamlFileWithRoutes(): string
    {
        return $this->getValues()[static::APPLICATION][static::DEFAULT_YAML_FILE_WITH_ROUTES] ?? 'routes.yml';
    }

}
