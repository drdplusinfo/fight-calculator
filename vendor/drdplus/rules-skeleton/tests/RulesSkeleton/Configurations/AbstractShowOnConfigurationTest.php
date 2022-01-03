<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Configurations;

use DrdPlus\RulesSkeleton\Configurations\AbstractShowOnConfiguration;
use PHPUnit\Framework\TestCase;

class AbstractShowOnConfigurationTest extends TestCase
{

    /**
     * @test
     * @dataProvider providePlacesToShown
     * @param array $showOnPlaces
     * @param bool $shouldBeShownOnGateway
     * @param bool $shouldBeShownOnHomepage
     * @param bool $shouldBeShownOnRoutes
     */
    public function I_can_create_it_without_defining_if_show_home_button_on(
        array $showOnPlaces,
        bool $shouldBeShownOnGateway,
        bool $shouldBeShownOnHomepage,
        bool $shouldBeShownOnRoutes
    ): void
    {
        $showOnConfiguration = $this->createShownOnConfiguration($showOnPlaces);
        self::assertSame($shouldBeShownOnGateway, $showOnConfiguration->isShownOnGateway());
        self::assertSame($shouldBeShownOnHomepage, $showOnConfiguration->isShownOnHomePage());
        self::assertSame($shouldBeShownOnRoutes, $showOnConfiguration->isShownOnRoutes());
    }

    public function providePlacesToShown(): array
    {
        return [
            'no explicit show on' => [
                [],
                true,
                true,
                true,
            ],
            'explicitly everywhere' => [
                [
                    AbstractShowOnConfiguration::SHOW_ON_HOMEPAGE => true,
                    AbstractShowOnConfiguration::SHOW_ON_ROUTES => true,
                    AbstractShowOnConfiguration::SHOW_ON_GATEWAY => true,
                ],
                true,
                true,
                true,
            ],
            'except explicitly on gateway' => [
                [
                    AbstractShowOnConfiguration::SHOW_ON_HOMEPAGE => true,
                    AbstractShowOnConfiguration::SHOW_ON_ROUTES => true,
                ],
                true,
                true,
                true,
            ],
            'except explicitly on homepage' => [
                [
                    AbstractShowOnConfiguration::SHOW_ON_ROUTES => true,
                    AbstractShowOnConfiguration::SHOW_ON_GATEWAY => true,
                ],
                true,
                true,
                true,
            ],
            'except explicitly on routes' => [
                [
                    AbstractShowOnConfiguration::SHOW_ON_HOMEPAGE => true,
                    AbstractShowOnConfiguration::SHOW_ON_GATEWAY => true,
                ],
                true,
                true,
                true,
            ],
            'nowhere' => [
                [
                    AbstractShowOnConfiguration::SHOW_ON_GATEWAY => false,
                    AbstractShowOnConfiguration::SHOW_ON_HOMEPAGE => false,
                    AbstractShowOnConfiguration::SHOW_ON_ROUTES => false,
                ],
                false,
                false,
                false,
            ],
        ];
    }

    private function createShownOnConfiguration(array $values): AbstractShowOnConfiguration
    {
        return new class($values) extends AbstractShowOnConfiguration {
            public function __construct(array $values)
            {
                parent::__construct($values, ['foo']);
            }
        };
    }
}
