<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\GatewayConfiguration;
use DrdPlus\RulesSkeleton\Configurations\HomeButtonConfiguration;
use DrdPlus\RulesSkeleton\Configurations\MenuConfiguration;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\String\StringTools;

class ConfigurationTest extends AbstractContentTest
{
    private array $createdYamlTempFiles = [];

    /**
     * @test
     */
    public function I_can_use_both_config_distribution_as_well_as_local_yaml_files(): void
    {
        if ($this->isSkeletonChecked()) {
            self::assertFileExists(
                $this->getProjectRoot() . '/' . Configuration::CONFIG_LOCAL_YML,
                'Local configuration expected on skeleton for testing purpose'
            );
        }
        self::assertFileExists($this->getProjectRoot() . '/' . Configuration::CONFIG_DISTRIBUTION_YML);
    }

    /**
     * @test
     * @dataProvider provideCompleteLocalAndDistributionYamlContent
     * @param array $localYamlContent
     * @param array $distributionYamlContent
     * @param array $expectedYamlContent
     */
    public function I_can_create_it_from_yaml_files(array $localYamlContent, array $distributionYamlContent, array $expectedYamlContent): void
    {
        $yamlTestingDir = $this->getYamlTestingDir();
        $this->createYamlLocalConfig($localYamlContent, $yamlTestingDir);
        $this->createYamlDistributionConfig($distributionYamlContent, $yamlTestingDir);
        $dirs = $this->createDirs($yamlTestingDir);
        $configuration = Configuration::createFromYml($dirs);
        self::assertSame($expectedYamlContent, $configuration->getValues());
        self::assertSame($expectedYamlContent[Configuration::GOOGLE][Configuration::ANALYTICS_ID], $configuration->getGoogleAnalyticsId());
        self::assertSame($dirs, $configuration->getDirs());
    }

    protected function getYamlTestingDir(): string
    {
        $yamlTestingDir = \sys_get_temp_dir() . '/' . \uniqid(StringTools::getClassBaseName(static::class), true);
        self::assertTrue(\mkdir($yamlTestingDir), 'Testing temporary dir can not be created: ' . $yamlTestingDir);

        return $yamlTestingDir;
    }

    protected function createYamlLocalConfig(array $data, string $yamlTestingDir): string
    {
        $localYamlConfig = $yamlTestingDir . '/' . Configuration::CONFIG_LOCAL_YML;
        $this->createYamlFile($data, $localYamlConfig);
        $this->createdYamlTempFiles[] = $localYamlConfig;

        return $localYamlConfig;
    }

    private function createYamlFile(array $data, string $file): void
    {
        self::assertTrue(\yaml_emit_file($file, $data), 'Yaml file has not been created: ' . $file);
    }

    protected function createYamlDistributionConfig(array $data, string $yamlTestingDir): string
    {
        $distributionYamlConfig = $yamlTestingDir . '/' . Configuration::CONFIG_DISTRIBUTION_YML;
        $this->createYamlFile($data, $distributionYamlConfig);
        $this->createdYamlTempFiles[] = $distributionYamlConfig;

        return $distributionYamlConfig;
    }

    public function __destruct()
    {
        foreach ($this->createdYamlTempFiles as $createdYamlTempFile) {
            \unlink($createdYamlTempFile);
        }
    }

    public function provideCompleteLocalAndDistributionYamlContent(): array
    {
        $completeYamlContent = $this->getSomeValidConfigurationValues();
        $limitedWebSection = $completeYamlContent;
        $changedCompleteYamlContent = $completeYamlContent;

        return [
            [$completeYamlContent, [], $completeYamlContent],
            [$limitedWebSection, $completeYamlContent, $changedCompleteYamlContent],
            [$completeYamlContent, $limitedWebSection, $completeYamlContent],
        ];
    }

    protected function getSomeValidConfigurationValues(): array
    {
        return [
            Configuration::WEB => [
                Configuration::MENU => [
                    MenuConfiguration::POSITION_FIXED => false,
                    MenuConfiguration::HOME_BUTTON => [
                        HomeButtonConfiguration::SHOW_ON_ROUTES => true,
                        HomeButtonConfiguration::SHOW_ON_HOMEPAGE => true,
                        HomeButtonConfiguration::TARGET => 'foo',
                    ],
                ],
                Configuration::NAME => 'Foo',
                Configuration::TITLE_SMILEY => '',
                Configuration::GATEWAY => [
                    GatewayConfiguration::PROTECTED_ACCESS => true,
                ],
                Configuration::ESHOP_URL => 'https://example.com',
            ],
            Configuration::GOOGLE => [Configuration::ANALYTICS_ID => 'UA-121206931-999'],
        ];
    }

    public function Google_analytics_id_is_unique(): void
    {
        if ($this->isRulesSkeletonChecked()) {
            self::assertSame('UA-121206931-0', $this->getConfiguration()->getGoogleAnalyticsId());
        } else {
            self::assertNotSame(
                'UA-121206931-1',
                $this->getConfiguration()->getGoogleAnalyticsId(),
                'Some valid Google analytics should be used'
            );
        }
    }

    /**
     * @test
     * @dataProvider provideInvalidGoogleAnalyticsId
     * @param string $invalidGoogleAnalyticsId
     */
    public function I_can_not_create_it_with_invalid_google_analytics_id(string $invalidGoogleAnalyticsId): void
    {
        $this->expectException(\DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidGoogleAnalyticsId::class);
        $this->expectExceptionMessageMatches('~' . preg_quote($invalidGoogleAnalyticsId, '~') . '~');

        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::GOOGLE][Configuration::ANALYTICS_ID] = $invalidGoogleAnalyticsId;
        new Configuration($this->getDirs(), $completeSettings);
    }

    public function provideInvalidGoogleAnalyticsId(): array
    {
        return [
            'some strange string' => ['GoogleItself'],
            'valid but with white space' => [' UA-121206931-1'],
        ];
    }

    /**
     * @test
     */
    public function I_can_not_create_it_without_web_name(): void
    {
        $this->expectException(\DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidConfiguration::class);
        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::WEB][Configuration::NAME] = '';
        new Configuration($this->getDirs(), $completeSettings);
    }

    /**
     * @test
     */
    public function I_can_not_create_it_without_set_title_smiley(): void
    {
        $this->expectException(\DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidConfiguration::class);
        $completeSettings = $this->getSomeValidConfigurationValues();
        unset($completeSettings[Configuration::WEB][Configuration::TITLE_SMILEY]);
        new Configuration($this->getDirs(), $completeSettings);
    }

    /**
     * @test
     */
    public function I_can_create_it_with_title_smiley_as_null(): void
    {
        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::WEB][Configuration::TITLE_SMILEY] = null;
        $configuration = new Configuration($this->getDirs(), $completeSettings);
        self::assertSame('', $configuration->getTitleSmiley());
    }

    /**
     * @test
     */
    public function I_can_create_it_without_yaml_file_with_routes(): void
    {
        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::APPLICATION][Configuration::YAML_FILE_WITH_ROUTES] = null;
        $configuration = new Configuration($this->getDirs(), $completeSettings);
        self::assertSame('', $configuration->getYamlFileWithRoutes());
    }

    /**
     * @test
     */
    public function I_can_get_yaml_file_with_routes(): void
    {
        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::APPLICATION][Configuration::YAML_FILE_WITH_ROUTES] = 'foo';
        $configuration = new Configuration($this->getDirs(), $completeSettings);
        self::assertSame('foo', $configuration->getYamlFileWithRoutes());
    }

    /**
     * @test
     */
    public function I_will_get_default_home_button_target_if_none_custom_is_set()
    {
        $completeSettings = $this->getSomeValidConfigurationValues();
        $expectedHomeButtonTarget = $completeSettings[Configuration::WEB][Configuration::MENU][MenuConfiguration::HOME_BUTTON][HomeButtonConfiguration::TARGET];
        $configuration = new Configuration($this->getDirs(), $completeSettings);
        self::assertSame(
            $expectedHomeButtonTarget,
            $configuration->getMenuConfiguration()->getHomeButtonConfiguration()->getTarget()
        );
        self::assertSame(
            $expectedHomeButtonTarget,
            $configuration->getMenuConfiguration()->getHomeButtonConfiguration()->getTarget()
        );
    }

    /**
     * @test
     */
    public function I_can_overwrite_default_home_button_target()
    {
        $completeSettings = $this->getSomeValidConfigurationValues();
        $completeSettings[Configuration::WEB][Configuration::MENU][MenuConfiguration::HOME_BUTTON][HomeButtonConfiguration::TARGET] = '..';
        $configuration = new Configuration($this->getDirs(), $completeSettings);
        self::assertSame('..', $configuration->getMenuConfiguration()->getHomeButtonConfiguration()->getTarget());
    }

    /**
     * @test
     */
    public function I_can_ommit_eshop_url_on_free_access()
    {
        $values = $this->getSomeValidConfigurationValues();

        $values[Configuration::WEB][Configuration::GATEWAY][GatewayConfiguration::PROTECTED_ACCESS] = false;
        unset($values[Configuration::WEB][Configuration::ESHOP_URL]);

        $configuration = new Configuration($this->getDirs(), $values);

        self::assertFalse($configuration->getGatewayConfiguration()->hasProtectedAccess());
        self::assertSame('', $configuration->getEshopUrl());
    }

    /**
     * @test
     */
    public function I_can_not_ommit_eshop_url_on_protected_access()
    {
        $this->expectException(\DrdPlus\RulesSkeleton\Configurations\Exceptions\InvalidEshopUrl::class);
        $values = $this->getSomeValidConfigurationValues();

        $values[Configuration::WEB][Configuration::GATEWAY][GatewayConfiguration::PROTECTED_ACCESS] = true;
        unset($values[Configuration::WEB][Configuration::ESHOP_URL]);

        new Configuration($this->getDirs(), $values);
    }
}
