<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Configurations\ProjectUrlConfiguration;
use Granam\WebContentBuilder\HtmlDocument;
use Gt\Dom\Element;
use Gt\Dom\HTMLCollection;

class HtmlHelper extends \Granam\WebContentBuilder\HtmlHelper
{
    public const ID_AUTHORS = 'autori';
    public const ID_MENU = 'menu';
    public const ID_MENU_WRAPPER = 'menu_wrapper';
    public const ID_META_REDIRECT = 'meta_redirect';
    public const ID_DEBUG_CONTACTS = 'debug_contacts';
    public const ID_HOME_BUTTON = 'home_button';
    public const ID_TABLE_OF_CONTENTS = 'table_of_contents';

    public const CLASS_RULES_AUTHORS = 'rules-authors';
    public const CLASS_CALCULATION = 'calculation';
    public const CLASS_COVERED_BY_CODE = 'covered-by-code';
    public const CLASS_QUOTE = 'quote';
    public const CLASS_BACKGROUND_RELATED = 'background-related';
    public const CLASS_BACKGROUND_IMAGE = 'background-image';
    public const CLASS_BACKGROUND_WALLPAPER = 'background-wallpaper';
    public const CLASS_BACKGROUND_WALLPAPER_LEFT_PART = 'background-wallpaper-left-part';
    public const CLASS_BACKGROUND_WALLPAPER_RIGHT_PART = 'background-wallpaper-right-part';
    public const CLASS_GENERIC = 'generic';
    public const CLASS_NOTE = 'note';
    public const CLASS_EXCLUDED = 'excluded';
    public const CLASS_INVISIBLE = 'invisible';
    public const CLASS_DELIMITER = 'delimiter';
    public const CLASS_CONTENT = 'content';
    public const CLASS_RESULT = 'result';
    public const CLASS_EXTERNAL_URL = 'external-url';
    public const CLASS_HIDDEN = 'hidden';
    public const CLASS_FORMULA = 'formula';
    public const CLASS_SOURCE_CODE_TITLE = 'source-code-title';
    public const CLASS_TABLES_RELATED = 'tables-related';
    public const CLASS_ROOT_PATH_ROUTE = 'root-path-route';
    public const CLASS_ROOTED_FROM_PATH_PREFIX = 'routed-from-path';

    public const DATA_CACHE_STAMP = 'data-cache-stamp';
    public const DATA_CACHED_AT = 'data-cached-at';
    public const DATA_HAS_MARKED_EXTERNAL_URLS = 'data-has-marked-external-urls';

    public static function createFromGlobals(Dirs $dirs, Environment $environment): HtmlHelper
    {
        return new static(
            $dirs,
            $environment->isOnForcedDevelopmentMode(),
            !empty($_GET['hide']) && \strpos(\trim($_GET['hide']), 'cover') === 0
        );
    }

    private bool $inDevMode;
    private bool $shouldHideCovered;

    public function __construct(Dirs $dirs, bool $inDevMode, bool $shouldHideCovered)
    {
        parent::__construct($dirs);
        $this->inDevMode = $inDevMode;
        $this->shouldHideCovered = $shouldHideCovered;
    }

    /**
     * @param HtmlDocument $htmlDocument
     * @param array|string[] $requiredIds filter of required tables by their IDs
     * @return array|Element[]
     */
    public function findTablesWithIds(HtmlDocument $htmlDocument, array $requiredIds = []): array
    {
        $requiredIds = \array_filter($requiredIds, 'trim');
        $requiredIds = \array_unique($requiredIds);
        $unifiedRequiredIds = [];
        foreach ($requiredIds as $requiredId) {
            if ($requiredId === '') {
                continue;
            }
            $unifiedRequiredId = static::toId($requiredId);
            $unifiedRequiredIds[$unifiedRequiredId] = $unifiedRequiredId;
        }
        $tablesWithIds = [];
        /** @var Element $table */
        foreach ($htmlDocument->getElementsByTagName('table') as $table) {
            $tableId = $this->getFirstIdFrom($table);
            if ($tableId === null) {
                continue;
            }
            $unifiedTableId = static::toId($tableId);
            $tablesWithIds[$unifiedTableId] = $table;
        }
        if (!$unifiedRequiredIds) {
            return $tablesWithIds; // all of them, no filter
        }

        return \array_intersect_key($tablesWithIds, $unifiedRequiredIds);
    }

    public function findBackgroundRelatedElements(HtmlDocument $htmlDocument): HTMLCollection
    {
        return $htmlDocument->getElementsByClassName(self::CLASS_BACKGROUND_RELATED);
    }

    public function findTablesRelatedElements(HtmlDocument $htmlDocument): HTMLCollection
    {
        return $htmlDocument->getElementsByClassName(self::CLASS_TABLES_RELATED);
    }

    public function markExternalLinksByClass(HtmlDocument $htmlDocument): HtmlDocument
    {
        /** @var Element $anchor */
        foreach ($htmlDocument->getElementsByTagName('a') as $anchor) {
            if (!$anchor->classList->contains(self::CLASS_INTERNAL_URL)
                && \preg_match('~^(https?:)?//[^#]~', (string)$anchor->getAttribute('href'))
            ) {
                $anchor->classList->add(self::CLASS_EXTERNAL_URL);
            }
        }
        $htmlDocument->body->setAttribute(self::DATA_HAS_MARKED_EXTERNAL_URLS, '1');

        return $htmlDocument;
    }

    /**
     * @param HtmlDocument $htmlDocument
     * @return Element[]
     */
    public function getExternalAnchors(HtmlDocument $htmlDocument): array
    {
        $externalAnchors = [];
        foreach ($htmlDocument->getElementsByTagName('a') as $anchor) {
            if ($this->isAnchorExternal($anchor)) {
                $externalAnchors[] = $anchor;
            }
        }

        return $externalAnchors;
    }

    /**
     * @param HtmlDocument $htmlDocument
     * @return Element[]
     */
    public function getInternalAnchors(HtmlDocument $htmlDocument): array
    {
        $internalAnchors = [];
        foreach ($htmlDocument->getElementsByTagName('a') as $anchor) {
            if (!$this->isAnchorExternal($anchor)) {
                $internalAnchors[] = $anchor;
            }
        }

        return $internalAnchors;
    }

    protected function isAnchorExternal(Element $anchor): bool
    {
        if ($anchor->tagName !== 'a') {
            throw new Exceptions\ExpectedAnchorElement(
                sprintf('Expected anchor element, got %s (%s)', $anchor->tagName, $anchor->innerHTML)
            );
        }

        return !$anchor->classList->contains(self::CLASS_INTERNAL_URL)
            && ($anchor->classList->contains(self::CLASS_EXTERNAL_URL) || $this->isLinkExternal((string)$anchor->getAttribute('href')));
    }

    /**
     * @param HtmlDocument $htmlDocument
     * @return HtmlDocument
     * @throws \LogicException
     */
    public function injectIframesWithRemoteTables(HtmlDocument $htmlDocument): HtmlDocument
    {
        $remoteDrdPlusLinks = [];
        foreach ($this->getExternalAnchors($htmlDocument) as $anchor) {
            if (!\preg_match(
                '~(?<protocol>(?:https?:)?//)(?<host>[[:alpha:]]+[.]drdplus[.](?:info|loc))/[^#]*#(?<tableId>tabulka_\w+)~',
                (string)$anchor->getAttribute('href'),
                $matches
            )) {
                continue;
            }
            $remoteDrdPlusLinks[$matches['host']][$matches['protocol']][] = $matches['tableId'];
        }
        if (\count($remoteDrdPlusLinks) === 0) {
            return $htmlDocument;
        }
        $body = $htmlDocument->body;
        foreach ($remoteDrdPlusLinks as $remoteDrdPlusHost => $protocolToTableIds) {
            foreach ($protocolToTableIds as $protocol => $tableIds) {
                $iFrame = $htmlDocument->createElement('iframe');
                $body->appendChild($iFrame);
                $iFrame->setAttribute('id', $remoteDrdPlusHost); // we will target that iframe via JS by remote host name
                $iFrame->setAttribute(
                    'src',
                    "{$protocol}{$remoteDrdPlusHost}/?tables=" . htmlspecialchars(implode(',', array_unique($tableIds)))
                );
                $iFrame->setAttribute('class', static::CLASS_HIDDEN);
            }
        }

        return $htmlDocument;
    }

    public function addIdsToTables(HtmlDocument $htmlDocument): HtmlDocument
    {
        foreach ($htmlDocument->getElementsByTagName('caption') as $caption) {
            if ($this->getFirstIdFrom($caption) !== null) { // there is already some ID on this CAPTION or on some of its children
                continue;
            }
            $captionContent = \trim($caption->textContent);
            if ($captionContent === '') {
                continue;
            }
            $caption->setAttribute('id', $captionContent);
        }
        /** @var Element $headerCell */
        foreach ($htmlDocument->getElementsByTagName('th') as $headerCell) {
            if ($this->getFirstIdFrom($headerCell) !== null) { // there is already some ID on this TH or on some of its children
                continue;
            }
            $headerCellContent = \trim($headerCell->textContent);
            if ($headerCellContent === '') {
                continue;
            }
            if (\strpos($headerCellContent, 'Tabulka') === false) {
                continue;
            }
            $headerCell->setAttribute('id', $headerCellContent);
        }

        return $htmlDocument;
    }

    public function prepareSourceCodeLinks(HtmlDocument $htmlDocument): void
    {
        if (!$this->inDevMode) {
            foreach ($htmlDocument->getElementsByClassName(self::CLASS_SOURCE_CODE_TITLE) as $withSourceCode) {
                $withSourceCode->className = \str_replace(self::CLASS_SOURCE_CODE_TITLE, self::CLASS_HIDDEN, $withSourceCode->className);
                $withSourceCode->removeAttribute('data-source-code');
            }
        } else {
            foreach ($htmlDocument->getElementsByClassName(self::CLASS_SOURCE_CODE_TITLE) as $withSourceCode) {
                $withSourceCode->appendChild($sourceCodeLink = new Element('a', 'source code'));
                $sourceCodeLink->setAttribute('class', 'source-code');
                $sourceCodeLink->setAttribute('href', $withSourceCode->getAttribute('data-source-code'));
            }
        }
    }

    public function resolveDisplayMode(HtmlDocument $html): void
    {
        if ($this->inDevMode) {
            $this->removeImages($html->body);
        } else {
            $this->removeClassesAboutCodeCoverage($html->body);
        }
        if (!$this->inDevMode || !$this->shouldHideCovered) {
            return;
        }
        $classesToHide = [self::CLASS_COVERED_BY_CODE, self::CLASS_QUOTE, self::CLASS_GENERIC, self::CLASS_NOTE, self::CLASS_EXCLUDED, self::CLASS_RULES_AUTHORS];
        foreach ($classesToHide as $classToHide) {
            foreach ($html->getElementsByClassName($classToHide) as $nodeToHide) {
                $nodeToHide->className = \str_replace($classToHide, self::CLASS_HIDDEN, $nodeToHide->className);
            }
        }
    }

    private function removeImages(Element $html): void
    {
        do {
            $somethingRemoved = false;
            /** @var Element $image */
            foreach ($html->getElementsByTagName('img') as $image) {
                $image->remove();
                $somethingRemoved = true;
            }
        } while ($somethingRemoved); // do not know why, but some nodes are simply skipped on first removal so have to remove them again
    }

    private function removeClassesAboutCodeCoverage(Element $html): void
    {
        $classesToRemove = [self::CLASS_COVERED_BY_CODE, self::CLASS_GENERIC, self::CLASS_EXCLUDED];
        foreach ($html->children as $child) {
            foreach ($classesToRemove as $classToRemove) {
                $child->classList->remove($classToRemove);
            }
            $this->removeClassesAboutCodeCoverage($child);
        }
    }

    public function replaceDiacriticsFromDrdPlusAnchorHashes(HtmlDocument $htmlDocument): HtmlDocument
    {
        $this->replaceDiacriticsFromAnchorHashes(
            $htmlDocument,
            '~(^$|drdplus[.](?:loc|info))~',
            '~blog[.]drdplus[.](?:loc|info)~'
        );

        return $htmlDocument;
    }
}
