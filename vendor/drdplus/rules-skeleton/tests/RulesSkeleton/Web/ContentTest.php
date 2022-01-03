<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Web\Main\MainContent;
use Granam\WebContentBuilder\HtmlDocument;
use Gt\Dom\Element;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class ContentTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_get_content_for_tests(): void
    {
        self::assertSame($this->getHtmlDocument()->saveHTML(), $this->getContent());
    }

    /**
     * @test
     */
    public function I_can_get_body(): void
    {
        self::assertNotEmpty($this->getHtmlDocument()->body->innerHTML);
    }

    /**
     * @test
     */
    public function Body_has_container_bootstrap_class(): void
    {
        self::assertTrue(
            $this->getHtmlDocument()->body->classList->contains('container'),
            'Body should has "container" class to be usable for Bootstrap'
        );
    }

    /**
     * @test
     * @dataProvider provideLinkToDrdPlus
     * @param string $linkToDrdPlus
     * @param string $expectedLinkWithoutDiacritics
     */
    public function Link_to_drdplus_has_removed_diacritics_from_hash(
        string $linkToDrdPlus,
        string $expectedLinkWithoutDiacritics
    ): void
    {
        $rulesMainContent = new MainContent(
            $this->createHtmlHelper(),
            $this->getEnvironment(),
            $this->createEmptyHead(),
            $this->createMainBody(sprintf('<a href="%s">Some link</a>', $linkToDrdPlus))
        );
        self::assertStringContainsString($expectedLinkWithoutDiacritics, $rulesMainContent->getValue());
    }

    public function provideLinkToDrdPlus(): array
    {
        return [
            ['https://pph.drdplus.info/foo#Příprava postavy', $this->turnToLocalLink('https://pph.drdplus.info') . '/foo#priprava_postavy'],
            ['https://bojovnik.drdplus.info/#Nekonečné kopí', $this->turnToLocalLink('https://bojovnik.drdplus.info') . '/#nekonecne_kopi'],
        ];
    }

    /**
     * @test
     * @dataProvider provideLinkOutOfDrdPlus
     * @param string $linkOutOfDrdPlus
     */
    public function Link_outside_of_drdplus_has_untouched_diacritics_in_hash(string $linkOutOfDrdPlus): void
    {
        self::assertDoesNotMatchRegularExpression('~drdplus[.](loc|info)~', $linkOutOfDrdPlus);
        $rulesMainContent = new MainContent(
            $this->createHtmlHelper(),
            $this->getEnvironment(),
            $this->createEmptyHead(),
            $this->createMainBody(\sprintf('<a href="%s">Some link</a>', $linkOutOfDrdPlus))
        );
        $hash = \substr($linkOutOfDrdPlus, \strpos($linkOutOfDrdPlus, '#') + 1);
        $expectedLinkOutOfDrdPlus = \str_replace('#' . $hash, '#' . \rawurlencode($hash), $linkOutOfDrdPlus);
        self::assertStringContainsString($expectedLinkOutOfDrdPlus, $rulesMainContent->getValue());
    }

    public function provideLinkOutOfDrdPlus(): array
    {
        return [
            ['https://obchod.altar.cz/drd-sada-zakladnich-prirucek-p-295.html#Copak to tu máme'],
        ];
    }

    /**
     * @test
     */
    public function Link_to_blog_is_not_broken(): void
    {
        $rulesMainContent = new MainContent(
            $this->createHtmlHelper(),
            $this->getEnvironment(),
            $this->createEmptyHead(),
            $this->createMainBody('<a href="https://blog.drdplus.info/#!index.md">To blog</a>')
        );
        self::assertStringContainsString($this->turnToLocalLink('https://blog.drdplus.info') . '/#!index.md', $rulesMainContent->getValue());
    }
}
