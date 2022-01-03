<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Cache\CacheIdProvider;
use DrdPlus\RulesSkeleton\CurrentWebVersion;
use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\Menu\MenuBodyInterface;
use DrdPlus\RulesSkeleton\Web\Tools\HtmlDocumentProcessorInterface;
use Granam\WebContentBuilder\HtmlDocument;
use Granam\WebContentBuilder\Web\Body;

class RulesHtmlDocumentPostProcessor implements HtmlDocumentProcessorInterface
{
    private \DrdPlus\RulesSkeleton\CurrentWebVersion $currentWebVersion;
    private \DrdPlus\RulesSkeleton\Web\Menu\MenuBodyInterface $menuBody;
    private \Granam\WebContentBuilder\Web\Body $body;
    private \DrdPlus\RulesSkeleton\Cache\CacheIdProvider $cacheIdProvider;

    public function __construct(
        MenuBodyInterface $menuBody,
        CurrentWebVersion $currentWebVersion,
        CacheIdProvider $cacheIdProvider
    )
    {
        $this->currentWebVersion = $currentWebVersion;
        $this->menuBody = $menuBody;
        $this->cacheIdProvider = $cacheIdProvider;
    }

    public function processDocument(HtmlDocument $htmlDocument): HtmlDocument
    {
        $this->injectCacheStamp($htmlDocument);
        $this->injectMenuWrapper($htmlDocument);
        $this->injectCacheId($htmlDocument);
        $this->injectBackgroundWallpaper($htmlDocument);
        return $htmlDocument;
    }

    private function injectCacheStamp(HtmlDocument $htmlDocument): void
    {
        $patchVersion = $this->currentWebVersion->getCurrentPatchVersion();
        $htmlDocument->documentElement->setAttribute('data-content-version', $patchVersion);
        $htmlDocument->documentElement->setAttribute('data-cached-at', \date(\DATE_ATOM));
    }

    private function injectMenuWrapper(HtmlDocument $htmlDocument): void
    {
        $menuWrapper = $htmlDocument->createElement('div');
        $menuWrapper->setAttribute('id', HtmlHelper::ID_MENU_WRAPPER);
        $menuWrapper->prop_set_innerHTML($this->menuBody->getValue());
        $htmlDocument->body->insertBefore($menuWrapper, $htmlDocument->body->firstElementChild);
    }

    private function injectCacheId(HtmlDocument $htmlDocument): void
    {
        $htmlDocument->documentElement->setAttribute(HtmlHelper::DATA_CACHE_STAMP, $this->cacheIdProvider->getCacheId());
    }

    private function injectBackgroundWallpaper(HtmlDocument $htmlDocument): void
    {
        $this->injectBackgroundWallpaperPart($htmlDocument, HtmlHelper::CLASS_BACKGROUND_WALLPAPER_RIGHT_PART);
        $this->injectBackgroundWallpaperPart($htmlDocument, HtmlHelper::CLASS_BACKGROUND_WALLPAPER_LEFT_PART);
    }

    private function injectBackgroundWallpaperPart(HtmlDocument $htmlDocument, string $htmlClass): void
    {
        $backgroundWallpaper = $htmlDocument->createElement('div');
        $backgroundWallpaper->classList->add($htmlClass);
        $backgroundWallpaper->classList->add(HtmlHelper::CLASS_BACKGROUND_WALLPAPER);
        $backgroundWallpaper->classList->add(HtmlHelper::CLASS_BACKGROUND_RELATED);
        $htmlDocument->body->insertBefore($backgroundWallpaper, $htmlDocument->body->firstElementChild);
    }
}
