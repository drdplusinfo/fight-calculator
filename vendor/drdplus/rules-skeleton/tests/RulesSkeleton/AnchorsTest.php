<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\HtmlHelper;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\String\StringTools;
use Gt\Dom\Element;
use Gt\Dom\HTMLDocument;

class AnchorsTest extends AbstractContentTest
{

    private const ID_WITH_ALLOWED_ELEMENTS_ONLY = 'with_allowed_elements_only';

    /** @var HTMLDocument[]|array */
    private static ?array $externalHtmlDocuments = null;

    /**
     * @test
     */
    public function Anchors_to_same_document_point_to_existing_ids(): void
    {
        $anchorsToSameDocument = $this->getAnchorsToSameDocument();
        if (!$this->getTestsConfiguration()->hasAnchorsToSameDocument()) {
            self::assertCount(
                0,
                $anchorsToSameDocument,
                sprintf(
                    "No anchors pointing to same document expected as tests config says by '%s'. But there are IDs to make anchors from: %s",
                    TestsConfiguration::HAS_ANCHORS_TO_SAME_DOCUMENT,
                    "\n" . implode("\n", $anchorsToSameDocument)
                )
            );

            return;
        }
        self::assertNotEmpty(
            $anchorsToSameDocument,
            sprintf("Some local anchors expected as tests config says by '%s'", TestsConfiguration::HAS_ANCHORS_TO_SAME_DOCUMENT)
        );
        $targets = [];
        $missingTargets = [];
        $htmlDocument = $this->getHtmlDocument();
        foreach ($anchorsToSameDocument as $anchor) {
            $expectedId = \substr($anchor, 1); // just remove leading #
            $target = $htmlDocument->getElementById($expectedId);
            if (!$target) {
                $missingTargets[] = $expectedId;
            } else {
                $targets[$expectedId] = $target;
            }
        }
        self::assertCount(
            0,
            $missingTargets,
            'Some local anchors point to non-existing IDs: ' . implode(
                ',',
                array_map(
                    static fn(string $id) => "'$id'",
                    $missingTargets
                )
            )
        );
        foreach ($targets as $expectedId => $target) {
            foreach ($this->classesAllowingInnerLinksTobeHidden() as $classAllowingInnerLinksTobeHidden) {
                if ($target->classList->contains($classAllowingInnerLinksTobeHidden)) {
                    return;
                }
            }
            self::assertStringNotContainsString('hidden', (string)$target->className, "Inner link of ID $expectedId should not be hidden");
            self::assertDoesNotMatchRegularExpression('~(display:\s*none|visibility:\s*hidden)~', (string)$target->getAttribute('style'));
        }
    }

    private function classesAllowingInnerLinksTobeHidden(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    private function getAnchorsToSameDocument(): array
    {
        $anchorsToSameDocument = [];
        /** @var Element $localLink */
        foreach ($this->getLocalLinks() as $localLink) {
            if (\strpos($localLink, '#') === 0) {
                $anchorsToSameDocument[] = $localLink;
            }
        }

        return $anchorsToSameDocument;
    }

    /**
     * @test
     */
    public function I_can_reach_every_local_link()
    {
        $this->goIn();
        foreach ($this->getLocalLinks() as $localLink) {
            $url = $this->getTestsConfiguration()->getLocalUrl();
            $url = rtrim($url, '/') . '/' . ltrim($localLink, '/');
            $responseHttpCode = $this->fetchContentFromUrl($url, false)['responseHttpCode'];
            $this->assertSame(
                200,
                $responseHttpCode,
                "Can not fetch content from local URL $url"
            );
        }
        $this->goOut();
    }

    /**
     * @return array|string[]
     */
    private function getLocalLinks(): array
    {
        return $this->getLinks()['local'];
    }

    /**
     * @return array[]|string[][]
     */
    private function getLinks(): array
    {
        static $links;
        if ($links) {
            return $links;
        }
        $externalLinks = [];
        $localLinks = [];
        $others = [];
        /** @var Element $anchor */
        foreach ($this->getHtmlDocument()->getElementsByTagName('a') as $anchor) {
            $link = (string)$anchor->getAttribute('href');
            $urlParts = parse_url($link);
            if (!empty($urlParts['host'])) {
                $externalLinks[] = $link;
            } elseif (empty($urlParts['scheme'])) {
                $localLinks[] = $link;
            } else {
                $others[] = $link;
            }
        }
        return [
            'local' => array_unique($localLinks),
            'external' => array_unique($externalLinks),
            'others' => array_unique($others),
        ];
    }

    private static array $checkedExternalAnchors = [];

    /**
     * @test
     */
    public function All_external_anchors_can_be_reached(): void
    {
        $skippedExternalUrls = [];
        foreach ($this->getExternalLinks() as $originalLink) {
            $link = $this->turnToLocalLink($originalLink);
            if (in_array($link, self::$checkedExternalAnchors, true)) {
                continue;
            }
            if (!$this->isLinkAccessible($link)) { // we are offline
                $skippedExternalUrls[] = $link;
            } else {
                $responseHttpCode = false;
                $redirectUrl = '';
                $error = '';
                $isDrdPlus = $this->isDrdPlusLink($link);
                $tempFileName = 'external_anchor_response_code_' . \md5($link) . '.tmp';
                if (!$isDrdPlus) {
                    $responseHttpCode = (int)$this->getFromCache($tempFileName);
                }
                if (!$responseHttpCode) {
                    [
                        'responseHttpCode' => $responseHttpCode,
                        'redirectUrl' => $redirectUrl,
                        'error' => $error,
                    ] = $this->fetchContentFromUrl($link, false /* just headers*/);
                    if (!$isDrdPlus && $responseHttpCode >= 200 && $responseHttpCode < 300) {
                        $this->cacheContent((string)$responseHttpCode, $tempFileName, $isDrdPlus, $responseHttpCode);
                    }
                }
                self::assertTrue(
                    $responseHttpCode >= 200 && $responseHttpCode < 300,
                    "Could not reach $link, got response code $responseHttpCode and redirect URL '$redirectUrl' ($error)"
                );
            }
            self::$checkedExternalAnchors[] = $link;
        }
        if ($skippedExternalUrls) {
            self::markTestSkipped(
                'Some external URLs have been skipped as we are probably offline: ' .
                print_r($skippedExternalUrls, true)
            );
        }
    }

    private function isDrdPlusLink(string $link): bool
    {
        return str_contains($link, 'drdplus.loc') || str_contains($link, 'drdplus.info');
    }

    protected function isLinkAccessible(string $link): bool
    {
        $hostname = parse_url($link, \PHP_URL_HOST);

        return $hostname !== false
            // already an IP, or DNS name translatable to an IP (if can not be translated, then gethostbyname returns given hostname back)
            && (\filter_var($hostname, \FILTER_VALIDATE_IP) || gethostbyname($hostname) !== $hostname);
    }

    private function getFromCache(string $cacheFileBaseName): string
    {
        $tempFile = \sys_get_temp_dir() . '/' . $cacheFileBaseName;
        if (!\file_exists($tempFile)) {
            return '';
        }
        if (\filemtime($tempFile) > (\time() - 3600)) {
            return \file_get_contents($tempFile);
        }
        \unlink($tempFile);

        return '';
    }

    private function cacheContent(string $content, string $cacheFileBaseName, bool $isDrdPlus, int $responseCode): bool
    {
        if ($isDrdPlus || $responseCode < 200 || $responseCode >= 300) {
            return false;
        }
        self::assertNotSame('', $content, 'Given content to cache is empty');
        $tempDir = \sys_get_temp_dir() . '/frontend-skeleton';
        self::assertTrue(
            \file_exists($tempDir) || \mkdir($tempDir, 0775) || \is_dir($tempDir),
            "Can not create dir for cached test content $tempDir"
        );
        $tempFile = $tempDir . '/' . $cacheFileBaseName;
        self::assertNotEmpty(\file_put_contents($tempFile, $content), "Nothing has been saved to $tempFile");

        return true;
    }

    /**
     * @test
     */
    public function External_anchors_with_hashes_point_to_existing_ids(): void
    {
        $externalAnchorsWithHash = $this->getExternalAnchorsWithHash();
        if (!$this->getTestsConfiguration()->hasExternalAnchorsWithHashes()) {
            self::assertCount(
                0,
                $externalAnchorsWithHash,
                sprintf(
                    "No external anchors expected as tests configuration says by '%s', got %s",
                    TestsConfiguration::HAS_EXTERNAL_ANCHORS_WITH_HASHES,
                    implode(',', $externalAnchorsWithHash)
                )
            );

            return;
        }
        self::assertNotEmpty(
            $externalAnchorsWithHash,
            sprintf(
                "Some external anchors expected as test configuration says by '%s'",
                TestsConfiguration::HAS_EXTERNAL_ANCHORS_WITH_HASHES
            )
        );
        $skippedExternalUrls = [];
        foreach ($externalAnchorsWithHash as $originalLink) {
            $link = $this->turnToLocalLink($originalLink);
            if (!$this->isLinkAccessible($link)) {
                $skippedExternalUrls[] = $link;
                continue;
            }
            $html = $this->getExternalHtmlDocument($link);
            $expectedId = \substr($link, \strpos($link, '#') + 1); // just remove leading #
            $target = $html->getElementById($expectedId);
            self::assertNotEmpty(
                $target,
                'No element found by ID ' . $expectedId . ' in a document with URL ' . $link
                . ($link !== $originalLink ? ' (originally ' . $originalLink . ')' : '')
            );
            self::assertDoesNotMatchRegularExpression('~(display:\s*none|visibility:\s*hidden)~', (string)$target->getAttribute('style'));
        }
        if ($skippedExternalUrls) {
            self::markTestSkipped(
                'Some external URLs have been skipped as we are probably offline: ' .
                print_r($skippedExternalUrls, true)
            );
        }
    }

    /**
     * @return array|string[]
     */
    private function getExternalAnchorsWithHash(): array
    {
        $externalAnchorsWithHash = [];
        foreach ($this->getExternalLinks() as $anchor) {
            if (\strpos($anchor, '#') > 0) {
                $externalAnchorsWithHash[] = $anchor;
            }
        }

        return $externalAnchorsWithHash;
    }

    private function getExternalHtmlDocument(string $href): HTMLDocument
    {
        $link = \substr($href, 0, \strpos($href, '#') ?: null);
        if ((self::$externalHtmlDocuments[$link] ?? null) === null) {
            $isDrdPlus = false;
            if ($this->isDrdPlusLink($link)) {
                self::assertNotEmpty(
                    preg_match('~//(?<subDomain>[^.]+([.][^.]+)*)\.drdplus\.~', $link),
                    "Expected some sub-domain in link $link"
                );
                $isDrdPlus = true;
            }
            $content = '';
            $tempFileName = 'external_anchor_content_' . \md5($link) . '.tmp';
            if (!$isDrdPlus) {
                $content = $this->getFromCache($tempFileName);
            }
            if (!$content) {
                [
                    'responseHttpCode' => $responseHttpCode,
                    'redirectUrl' => $redirectUrl,
                    'error' => $error,
                    'content' => $content,
                ] = $this->fetchContentFromUrl($link, true /* fetch body */, $this->getPostDataToFetchContent($isDrdPlus));
                self::assertTrue(
                    $responseHttpCode >= 200 && $responseHttpCode < 300,
                    "Could not reach $link, got response code $responseHttpCode and redirect URL '$redirectUrl' ($error)"
                );
                self::assertNotEmpty($content, 'Nothing has been fetched from URL ' . $link);
                $this->cacheContent($content, $tempFileName, $isDrdPlus, $responseHttpCode);
            }
            self::$externalHtmlDocuments[$link] = @new HTMLDocument($content);
            if ($isDrdPlus) {
                self::assertCount(
                    0,
                    self::$externalHtmlDocuments[$link]->getElementsByTagName('form'),
                    'Seems we have not passed ownership check for ' . $href
                );
            }
        }

        return self::$externalHtmlDocuments[$link];
    }

    /**
     * @test
     */
    public function Anchor_to_id_self_is_not_created_if_contains_anchor_element(): void
    {
        $document = $this->getHtmlDocument();
        $noAnchorsForMe = $document->getElementById(StringTools::toConstantLikeValue('no-anchor-for-me'));
        if (!$noAnchorsForMe && !$this->isSkeletonChecked()) {
            self::assertFalse(false, 'Nothing to test here');

            return;
        }
        self::assertNotEmpty($noAnchorsForMe, "Missing testing element with ID 'no-anchor-for-me'");
        $links = $noAnchorsForMe->getElementsByTagName('a');
        self::assertNotEmpty($links);
        $idLink = '#' . $noAnchorsForMe->getAttribute('id');
        /** @var \DOMElement $link */
        foreach ($links as $link) {
            self::assertNotSame($idLink, $link->getAttribute('href') ?? '', "No anchor pointing to ID self expected: $idLink");
        }
    }

    /**
     * @test
     */
    public function Original_ids_do_not_have_links_to_self(): void
    {
        $document = $this->getHtmlDocument();
        $originalIds = $document->getElementsByClassName(HtmlHelper::CLASS_INVISIBLE_ID);
        if (!$this->getTestsConfiguration()->hasIds()) {
            self::assertCount(
                0,
                $originalIds,
                'No original IDs, identified by CSS class ' . HtmlHelper::CLASS_INVISIBLE_ID . ' expected, got '
                . implode("\n", array_map(static fn(Element $element) => $element->outerHTML, $this->collectionToArray($originalIds)))
            );

            return;
        }
        self::assertNotEmpty(
            $originalIds,
            sprintf(
                "Expected some IDs identified by a HTML class '%s' as test configuration says by '%s'",
                HtmlHelper::CLASS_INVISIBLE,
                TestsConfiguration::HAS_IDS
            )
        );
        foreach ($originalIds as $originalId) {
            self::assertSame('', $originalId->innerHTML);
        }
    }

    private function collectionToArray(\Iterator $collection): array
    {
        $array = [];
        foreach ($collection as $item) {
            $array[] = $item;
        }

        return $array;
    }

    /**
     * @test
     */
    public function Only_allowed_elements_are_moved_into_injected_link(): void
    {
        $document = $this->getHtmlDocument();
        $withAllowedElementsOnly = $document->getElementById(self::ID_WITH_ALLOWED_ELEMENTS_ONLY);
        if (!$withAllowedElementsOnly && !$this->isSkeletonChecked()) {
            self::assertFalse(false, 'Nothing to test here');

            return;
        }
        self::assertNotEmpty(
            $withAllowedElementsOnly,
            'Missing testing HTML element with ID ' . self::ID_WITH_ALLOWED_ELEMENTS_ONLY
        );
        $anchors = $withAllowedElementsOnly->getElementsByTagName('a');
        self::assertCount(1, $anchors);
        $anchor = $anchors->item(0);
        self::assertNotNull($anchor);
        self::assertSame('#' . self::ID_WITH_ALLOWED_ELEMENTS_ONLY, (string)$anchor->getAttribute('href'));
        foreach ($anchor->childNodes as $childNode) {
            self::assertContains($childNode->nodeName, ['#text', 'span', 'b', 'strong', 'i']);
        }
    }

    /**
     * @test
     */
    public function Calculation_does_not_have_another_calculation_inside(): void
    {
        if (!$this->getTestsConfiguration()->hasCalculations()) {
            self::assertFalse(false, 'No calculations in current document');

            return;
        }
        $document = $this->getHtmlDocument();
        $calculations = $document->getElementsByClassName(HtmlHelper::CLASS_CALCULATION);
        self::assertNotEmpty(
            $calculations,
            sprintf(
                "Some calculations expected as test configuration says by '%s'",
                TestsConfiguration::HAS_CALCULATIONS
            )
        );
        foreach ($calculations as $calculation) {
            foreach ($calculation->children as $child) {
                $innerCalculations = $child->getElementsByClassName(HtmlHelper::CLASS_CALCULATION);
                self::assertCount(
                    0,
                    $innerCalculations,
                    'Calculation should not has another calculation inside: ' . $calculation->outerHTML
                );
            }
        }
    }

    /**
     * @test
     */
    public function Links_to_altar_uses_https(): void
    {
        $linksToAltar = [];
        foreach ($this->getExternalLinks() as $link) {
            if (!\strpos($link, 'altar.cz')) {
                continue;
            }
            $linksToAltar[] = $link;
        }
        if (!$this->getTestsConfiguration()->hasLinksToAltar()) {
            self::assertCount(
                0,
                $linksToAltar,
                sprintf(
                    "No link to Altar expected as test configuration says by '%s'",
                    TestsConfiguration::HAS_LINKS_TO_ALTAR
                )
            );

            return;
        }
        self::assertNotEmpty($linksToAltar, sprintf("Expected some links to Altar as test configuration says by '%s'", TestsConfiguration::HAS_LINKS_TO_ALTAR));
        foreach ($linksToAltar as $linkToAltar) {
            self::assertStringStartsWith('https', $linkToAltar, "Every link to Altar should be via https: '$linkToAltar'");
        }
    }

    /**
     * @test
     */
    public function No_links_point_to_local_hosts(): void
    {
        $urlsWithLocalHosts = [];
        /** @var Element $anchor */
        foreach ($this->getHtmlDocument(['mode' => 'prod' /* do not turn links to local */])->getElementsByTagName('a') as $anchor) {
            $href = (string)$anchor->getAttribute('href');
            self::assertNotEmpty($href);
            $hostname = parse_url($href, PHP_URL_HOST) ?: null;
            if ($hostname === null) { // local link with anchor or query only
                continue;
            }
            if (preg_match('~[.]loc#~', $hostname) || gethostbyname($hostname) === '127.0.0.1') {
                $urlsWithLocalHosts[] = $anchor->outerHTML;
            }
        }
        self::assertCount(0, $urlsWithLocalHosts, "There are forgotten local URLs \n" . implode(",\n", $urlsWithLocalHosts));
    }

    private function getPostDataToFetchContent(bool $isDrdPlus): array
    {
        return $isDrdPlus ? ['trial' => 1] : [];
    }

    /**
     * @return array|string[]
     */
    private function getExternalLinks(): array
    {
        static $externalLinks = [];
        if (!$externalLinks) {
            $externalLinks = $this->getLinks()['external'];
            $externalLinks[] = $this->getTestsConfiguration()->getExpectedPublicUrl();
            $externalLinks = array_unique($externalLinks);
        }

        return $externalLinks;
    }

    /**
     * @test
     */
    public function All_anchors_point_to_syntactically_valid_links(): void
    {
        foreach ($this->getLicenceSwitchers() as $licenceSwitcher) {
            $licenceSwitcher();
            $invalidAnchors = $this->parseInvalidAnchors($this->getContent());
            self::assertCount(
                0,
                $invalidAnchors,
                'Some anchors from content points to invalid links ' . implode(',', $invalidAnchors)
            );
            $invalidAnchors = $this->parseInvalidAnchors($this->getGatewayContent());
            self::assertCount(
                0,
                $invalidAnchors,
                'Some anchors from ownership confirmation points to invalid links ' . implode(',', $invalidAnchors)
            );
        }
    }

    /**
     * @param string $content
     * @return array
     */
    private function parseInvalidAnchors(string $content): array
    {
        preg_match_all('~(?<invalidAnchors><a[^>]+href="(?:(?![#?]|https?|[.]?/|mailto).)+[^>]+>)~', $content, $matches);

        return $matches['invalidAnchors'];
    }

    /**
     * @test
     */
    public function I_can_go_directly_to_eshop_item_page(): void
    {
        if (!$this->getTestsConfiguration()->canBeBoughtOnEshop()) {
            self::assertFalse(false, 'Can not be bought');

            return;
        }
        $eshopUrl = $this->getConfiguration()->getEshopUrl();
        $eshopUrlRegexp = $this->getTestsConfiguration()->getExpectedEshopUrlRegexp();
        self::assertMatchesRegularExpression(
            $eshopUrlRegexp,
            $eshopUrl,
            sprintf(
                "Expected configured valid URL to Altar e-shop as test configuration says by '%s'",
                TestsConfiguration::CAN_BE_BOUGHT_ON_ESHOP
            )
        );
        self::assertSame($eshopUrl, $this->getLinkToEshop()->getAttribute('href') ?? '', 'Expected different link to e-shop');
    }

    private function getLinkToEshop(): Element
    {
        $rulesAuthorsElements = $this->getHtmlDocument()->body->getElementsByClassName(HtmlHelper::CLASS_RULES_AUTHORS);
        self::assertGreaterThan(
            0,
            $rulesAuthorsElements->count(),
            sprintf(
                'Link to eshop expected in %s as %s configuration says',
                HtmlHelper::CLASS_RULES_AUTHORS,
                TestsConfiguration::CAN_BE_BOUGHT_ON_ESHOP
            )
        );
        self::assertCount(
            1,
            $rulesAuthorsElements,
            sprintf("Expected one '%s' class, got %d of them", HtmlHelper::CLASS_RULES_AUTHORS, $rulesAuthorsElements->count())
        );
        $rulesAuthors = $rulesAuthorsElements->current();
        $lastElement = $rulesAuthors->lastElementChild;
        self::assertSame(
            'a',
            $lastElement->tagName,
            'Expected link to eshop to be the last child of ' . HtmlHelper::CLASS_RULES_AUTHORS
        );
        $href = $lastElement->getAttribute('href') ?? '';
        $host = parse_url($href, \PHP_URL_HOST);
        if ($host === 'obchod.altar.cz') {
            return $lastElement;
        }
        self::fail(
            sprintf(
                'Missing a link to eshop obchod.altar.cz in ' . HtmlHelper::CLASS_RULES_AUTHORS,
                $rulesAuthors->outerHTML
            )
        );
    }

    /**
     * @test
     */
    public function Links_to_vukogvazd_uses_https(): void
    {
        $linksToVukogvazd = [];
        foreach ($this->getExternalLinks() as $link) {
            if (str_contains($link, 'vukogvazd.cz')) {
                $linksToVukogvazd[] = $link;
            }
        }
        if (count($linksToVukogvazd) === 0) {
            self::assertFalse(false, 'No links to Vukogvazd have been found');
        } else {
            foreach ($linksToVukogvazd as $linkToVukogvazd) {
                self::assertStringStartsWith('https', $linkToVukogvazd, "Every link to vukogvazd should be via https: '$linkToVukogvazd'");
            }
        }
    }

    /**
     * @test
     */
    public function Character_sheet_comes_from_drdplus_info(): void
    {
        $linksToCharacterSheet = [];
        foreach ($this->getExternalLinks() as $link) {
            $link = $this->turnToLocalLink($link);
            if (str_contains($link, 'charakternik.pdf')) {
                $linksToCharacterSheet[] = $link;
            }
        }
        if (!$this->getTestsConfiguration()->hasCharacterSheet()) {
            self::assertCount(
                0,
                $linksToCharacterSheet,
                sprintf("No links to PDF character sheet expected as test configuration says by '%s'", TestsConfiguration::HAS_CHARACTER_SHEET)
            );

            return;
        }
        self::assertGreaterThan(
            0,
            count($linksToCharacterSheet),
            sprintf("PDF character sheet expected  as test configuration says by '%s'", TestsConfiguration::HAS_CHARACTER_SHEET)
        );
        $expectedOriginalLink = 'https://www.drdplus.info/pdf/charakternik.pdf';
        $expectedLink = $this->turnToLocalLink($expectedOriginalLink);
        foreach ($linksToCharacterSheet as $linkToCharacterSheet) {
            self::assertSame(
                $expectedLink,
                $linkToCharacterSheet,
                "Every link to PDF character sheet should lead to $expectedOriginalLink"
            );
        }
    }

    /**
     * @test
     */
    public function Journal_comes_from_drdplus_info(): void
    {
        $linksToJournal = [];
        foreach ($this->getExternalLinks() as $link) {
            $link = $this->turnToLocalLink($link);
            if (preg_match('~/denik_\w+\.pdf$~', $link)) {
                $linksToJournal[] = $link;
            }
        }
        if (!$this->getTestsConfiguration()->hasLinksToJournals() && !$this->getTestsConfiguration()->hasLinkToSingleJournal()) {
            self::assertCount(
                0,
                $linksToJournal,
                sprintf(
                    "No links to PDF journal expected as test configuration says by '%s' and '%s'",
                    TestsConfiguration::HAS_LINKS_TO_JOURNALS,
                    TestsConfiguration::HAS_LINK_TO_SINGLE_JOURNAL
                )
            );

            return;
        }
        self::assertGreaterThan(
            0,
            count($linksToJournal),
            sprintf(
                "PDF journals expected as test configuration says by '%s' or '%s'",
                TestsConfiguration::HAS_LINKS_TO_JOURNALS,
                TestsConfiguration::HAS_LINK_TO_SINGLE_JOURNAL
            )
        );
        if (!$this->getTestsConfiguration()->hasLinkToSingleJournal()) {
            foreach ($linksToJournal as $linkToJournal) {
                self::assertMatchesRegularExpression(
                    '~^' . preg_quote($this->turnToLocalLink('https://www.drdplus.info'), '~') . '/pdf/deniky/denik_\w+[.]pdf$~',
                    $linkToJournal,
                    'Every link to PDF journal should lead to https://www.drdplus.info/pdf/deniky/denik_foo.pdf'
                );
            }

            return;
        }
        self::assertTrue($this->getTestsConfiguration()->hasLinksToJournals());
        $expectedOriginalLink = $this->getExpectedLinkToJournal();
        $expectedLink = $this->turnToLocalLink($expectedOriginalLink);
        foreach ($linksToJournal as $linkToJournal) {
            self::assertSame(
                $expectedLink,
                $linkToJournal,
                "Every link to PDF journal should lead to $expectedOriginalLink"
            );
        }
    }

    private function getExpectedLinkToJournal(): string
    {
        return 'https://www.drdplus.info/pdf/deniky/denik_' . StringTools::toConstantLikeValue($this->getProfessionName()) . '.pdf';
    }

    private function getProfessionName(): string
    {
        $currentPageTitle = $this->getCurrentPageTitle();
        self::assertSame(
            1,
            preg_match('~\s(?<lastWord>\w+)$~u', $currentPageTitle, $matches),
            "No last word found in '$currentPageTitle'"
        );
        $lastWord = $matches['lastWord'];

        return \rtrim($lastWord, 'aeiouy');
    }

    /**
     * @test
     */
    public function Buttons_should_not_have_links_inside(): void
    {
        foreach ($this->getLicenceSwitchers() as $licenceSwitcher) {
            $licenceSwitcher();
            $buttons = $this->getHtmlDocument()->getElementsByTagName('button');
            if (!$this->getTestsConfiguration()->hasButtons()) {
                self::assertCount(0, $buttons, 'No buttons to test');
                return;
            }
            foreach ($buttons as $button) {
                $buttonAnchors = $button->getElementsByTagName('a');
                self::assertCount(0, $buttonAnchors, 'No anchors expected in button: ' . $button->outerHTML);
            }
        }
    }
}
