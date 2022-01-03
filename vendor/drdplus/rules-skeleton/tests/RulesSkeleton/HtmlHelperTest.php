<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\HtmlHelper;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\String\StringTools;
use Granam\WebContentBuilder\HtmlDocument;
use Gt\Dom\Element;

class HtmlHelperTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_create_id_from_any_name(): void
    {
        /** @var HtmlHelper $htmlHelperClass */
        $htmlHelperClass = static::getSutClass();
        self::assertSame('kuala_lumpur', $htmlHelperClass::toId('Kuala lumpur'));
        self::assertSame('krizaly_s_mrkvi', $htmlHelperClass::toId('Křížaly s mrkví'));
    }

    /**
     * @test
     */
    public function I_can_get_filtered_tables_from_content(): void
    {
        $htmlHelper = $this->createSut();

        $tablesWithIds = $htmlHelper->findTablesWithIds($this->getHtmlDocument());
        if (!$this->getTestsConfiguration()->hasTables()) {
            self::assertCount(0, $tablesWithIds);

            return;
        }
        self::assertGreaterThan(0, \count($tablesWithIds));
        self::assertEmpty($htmlHelper->findTablesWithIds($this->getHtmlDocument(), ['nonExistingTableId']));
        $someExpectedTableIds = $this->getTestsConfiguration()->getSomeExpectedTableIds();
        if (!$this->getTestsConfiguration()->hasTables()) {
            self::assertCount(0, $someExpectedTableIds, 'No tables expected');

            return;
        }
        self::assertNotEmpty($someExpectedTableIds, 'Some tables expected');
        foreach ($someExpectedTableIds as $someExpectedTableId) {
            $lowerExpectedTableId = HtmlHelper::toId($someExpectedTableId);
            self::assertArrayHasKey($lowerExpectedTableId, $tablesWithIds, 'A table ID is missing');
            $expectedTable = $tablesWithIds[$lowerExpectedTableId];
            self::assertInstanceOf(Element::class, $expectedTable);
            self::assertNotEmpty($expectedTable->innerHTML, "Table of ID $someExpectedTableId is empty");
            // intentionally to snake case to test proper ID case conversion
            $someCasedExpectedTableId = HtmlHelper::toId($someExpectedTableId);
            $singleTable = $htmlHelper->findTablesWithIds($this->getHtmlDocument(), [$someCasedExpectedTableId]);
            self::assertCount(1, $singleTable, 'No table has been found by ID ' . $someCasedExpectedTableId);
            self::assertArrayHasKey($lowerExpectedTableId, $tablesWithIds, 'ID is expected to be lower-cased');
        }
    }

    protected function createSut(): HtmlHelper
    {
        /** @var HtmlHelper $htmlHelperClass */
        $htmlHelperClass = static::getSutClass();
        $dirs = $this->getDirs();

        return $htmlHelperClass::createFromGlobals($dirs, $this->getEnvironment());
    }

    /**
     * @test
     */
    public function Filtering_tables_by_id_does_not_crash_on_table_without_id(): void
    {
        $htmlHelper = $this->createSut();

        $allTables = $htmlHelper->findTablesWithIds(new HtmlDocument(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Just a test</title>
  <meta charset="utf-8">
</head>
<body>
  <table>No ID here</table>
</body>
</html>
HTML
        ));
        self::assertCount(0, $allTables);
    }

    /**
     * @test
     */
    public function Same_table_ids_are_filtered_on_tables_only_mode(): void
    {
        if (!$this->getTestsConfiguration()->hasTables()) {
            self::assertCount(
                0,
                $this->getHtmlDocument()->getElementsByTagName('table'),
                sprintf(
                    "No tables with IDs expected as test configuration says by '%s'",
                    TestsConfiguration::HAS_TABLES
                )
            );

            return;
        }
        $someExpectedTableIds = $this->getTestsConfiguration()->getSomeExpectedTableIds();
        self::assertGreaterThan(
            0,
            \count($someExpectedTableIds),
            sprintf(
                "Some table IDs expected under '%s' as test configuration says by '%s'",
                TestsConfiguration::SOME_EXPECTED_TABLE_IDS,
                TestsConfiguration::HAS_TABLES
            )
        );
        $tableId = \current($someExpectedTableIds);

        $htmlHelper = $this->createSut();

        $tables = $htmlHelper->findTablesWithIds($this->getHtmlDocument(), [$tableId, $tableId]);
        self::assertCount(1, $tables);
    }

    /**
     * @test
     */
    public function It_will_not_add_anchor_into_anchor_with_id(): void
    {
        $htmlHelper = $this->createSut();

        $content = '<!DOCTYPE html>
<html lang="en"><body><a href="" id="someId">Foo</a></body></html>';
        $htmlDocument = new HtmlDocument($content);
        $htmlHelper->addAnchorsToIds($htmlDocument);
        self::assertSame($content, \trim($htmlDocument->saveHTML()));
    }

    /**
     * @test
     */
    public function Ids_are_turned_to_constant_like_diacritics_free_format(): void
    {
        $htmlHelper = $this->createSut();

        $originalId = 'Příliš # žluťoučký # kůň # úpěl # ďábelské # ódy';
        $htmlDocument = new HtmlDocument(<<<HTML
        <!DOCTYPE html>
<html lang="cs">
<head>
  <title>Just a test</title>
  <meta charset="utf-8">
</head>
<body>
  <div class="test" id="$originalId"></div>
</body>
</html>
HTML
        );
        $htmlHelper->unifyIds($htmlDocument);
        $divs = $htmlDocument->getElementsByClassName('test');
        self::assertCount(1, $divs);
        $div = $divs[0];
        $id = $div->id;
        self::assertNotEmpty($id);
        $expectedId = StringTools::toConstantLikeValue($originalId);
        self::assertSame($expectedId, $id);
        $this->Original_id_is_accessible_without_change_via_data_attribute($div, $originalId);
        $this->Original_id_can_be_used_as_anchor_via_inner_invisible_element($div, $originalId);
    }

    private function Original_id_is_accessible_without_change_via_data_attribute(Element $elementWithId, string $expectedOriginalId): void
    {
        $fetchedOriginalId = $elementWithId->getAttribute(HtmlHelper::DATA_ORIGINAL_ID);
        self::assertNotEmpty($fetchedOriginalId);
        self::assertSame($expectedOriginalId, $fetchedOriginalId);
    }

    private function Original_id_can_be_used_as_anchor_via_inner_invisible_element(Element $elementWithId, string $expectedOriginalId): void
    {
        $invisibleIdElements = $elementWithId->getElementsByClassName(HtmlHelper::CLASS_INVISIBLE_ID);
        self::assertCount(1, $invisibleIdElements);
        $invisibleIdElement = $invisibleIdElements[0];
        $invisibleId = $invisibleIdElement->id;
        self::assertNotEmpty($invisibleId);
        self::assertSame(\str_replace('#', '_', $expectedOriginalId), $invisibleId);
    }

    /**
     * @test
     * @dataProvider provideLinksToRemoteTables
     * @param array|string[] $links
     * @param string $expectedIframe
     * @param string $expectedIframeId
     */
    public function I_can_inject_iframes_with_remote_tables(array $links, string $expectedIframe, string $expectedIframeId): void
    {
        $htmlHelper = $this->createSut();

        $implodedLinks =  implode("\n", $links);
        $htmlDocument = new HtmlDocument(<<<HTML
        <!DOCTYPE html>
<html lang="cs">
<head>
  <title>Just a test</title>
  <meta charset="utf-8">
</head>
<body>
  $implodedLinks
    <a>Tohle je takový divný odkaz bez href, stává se.</a>
</body>
</html>
HTML
        );
        $htmlHelper->markExternalLinksByClass($htmlDocument);
        $htmlHelper->injectIframesWithRemoteTables($htmlDocument);
        $iframes = $htmlDocument->getElementsByTagName('iframe');
        self::assertCount(1, $iframes, 'Single iframe (with tables preview) expected');
        $iframe = $iframes->current();
        self::assertSame(
            $expectedIframe,
            $iframe->getAttribute('src'),
            "Something is bad with iframe\n" . $iframe->outerHTML
        );
        self::assertSame($expectedIframeId, $iframe->id, 'Expected ID made from iframe target domain');
    }

    public function provideLinksToRemoteTables(): array
    {
        return [
            'pph.drdplus.info' => [
                [
                    '<a href="https://pph.drdplus.info/#tabulka_vzdalenosti">Odkaz na tabulku vzdálenosti</a>',
                    '<a href="https://pph.drdplus.info/#tabulka_vzdalenosti">Druhý odkaz na tabulku vzdálenosti</a>',
                    '<a href="https://pph.drdplus.info/#tabulka_casu">Odkaz na tabulku času</a>',
                    '<a href="https://pph.drdplus.info/#tabulka_vzdalenosti">Třetí na tabulku vzdálenosti</a>',
                ],
                'https://pph.drdplus.info/?tables=tabulka_vzdalenosti,tabulka_casu',
                'pph.drdplus.info',
            ],
            'theurg.drdplus.loc' => [
                ['<a href="http://theurg.drdplus.loc/#tabulka_formuli">Odkaz na lokálního theurga a jeho tabulku formulí</a>'],
                'http://theurg.drdplus.loc/?tables=tabulka_formuli',
                'theurg.drdplus.loc',
            ],
        ];
    }

    /**
     * @test
     */
    public function I_can_mark_external_links_by_class(): void
    {
        $htmlHelper = $this->createSut();

        $htmlDocument = new HtmlDocument(<<<HTML
        <!DOCTYPE html>
<html lang="cs">
<head>
  <title>Just a test</title>
  <meta charset="utf-8">
</head>
<body>
  <a id="link_without_anchor">Link without anchor</a>
</body>
</html>
HTML
        );
        self::assertNull($htmlDocument->body->getAttribute(HtmlHelper::DATA_HAS_MARKED_EXTERNAL_URLS));
        $htmlHelper->markExternalLinksByClass($htmlDocument);
        self::assertSame('1', $htmlDocument->body->getAttribute(HtmlHelper::DATA_HAS_MARKED_EXTERNAL_URLS));
        $linkWithoutAnchor = $htmlDocument->getElementById('link_without_anchor');
        self::assertFalse($linkWithoutAnchor->classList->contains(HtmlHelper::CLASS_EXTERNAL_URL));
    }

    /**
     * @test
     */
    public function I_can_add_id_to_table(): void
    {
        $htmlHelper = $this->createSut();

        $htmlDocument = new HtmlDocument($content = <<<HTML
        <!DOCTYPE html>
<html lang="cs">
<table>
  <thead>
    <tr>
      <th>Tabulka <strong>noční míry</strong></th>
      <th>Tabulka denní míry</th>
    </tr>
  </thead>
</table>
</html>
HTML
        );
        self::assertSame($htmlDocument, $htmlHelper->addIdsToTables($htmlDocument));
        $firstTableWithId = $htmlDocument->getElementById($firstExpectedId = 'Tabulka noční míry');
        self::assertNotEmpty(
            $firstTableWithId,
            \sprintf('No element found by ID %s in content %s', $firstExpectedId, $htmlDocument->body->prop_get_outerHTML())
        );
        self::assertSame('th', $firstTableWithId->nodeName);
        $secondTableWithId = $htmlDocument->getElementById($secondExpectedId = 'Tabulka denní míry');
        self::assertNotEmpty(
            $secondTableWithId,
            \sprintf('No element found by ID %s in content %s', $secondExpectedId, $htmlDocument->body->prop_get_outerHTML())
        );
    }

    /**
     * @test
     */
    public function No_ids_are_added_to_table_with_id_in_head_cell(): void
    {
        $htmlHelper = $this->createSut();

        $htmlDocument = new HtmlDocument($content = <<<HTML
        <!DOCTYPE html>
<html lang="cs">
<table>
  <thead>
    <tr>
      <th>
        <span id="Tabulka pohybu">Tabulka pohybu</span> a k tomu <span id="Tabulka únavy z pohybu">Tabulka únavy z pohybu</span>
      </th>
    </tr>
  </thead>
</table>
</html>
HTML
        );
        self::assertSame($htmlDocument, $htmlHelper->addIdsToTables($htmlDocument));
        self::assertSame((new HtmlDocument($content))->saveHTML(), $htmlDocument->saveHTML());
    }
}
