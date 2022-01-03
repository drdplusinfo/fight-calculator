<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Web\Menu;

use DrdPlus\RulesSkeleton\Configurations\MenuConfiguration;
use DrdPlus\RulesSkeleton\HomepageDetector;
use DrdPlus\RulesSkeleton\Ticket;
use Granam\Strict\Object\StrictObject;

class MenuBody extends StrictObject implements MenuBodyInterface
{
    private \DrdPlus\RulesSkeleton\Configurations\MenuConfiguration $menuConfiguration;
    private \DrdPlus\RulesSkeleton\HomepageDetector $homepageDetector;
    private \DrdPlus\RulesSkeleton\Ticket $ticket;

    public function __construct(
        MenuConfiguration $menuConfiguration,
        HomepageDetector $homepageDetector,
        Ticket $ticket
    )
    {
        $this->menuConfiguration = $menuConfiguration;
        $this->homepageDetector = $homepageDetector;
        $this->ticket = $ticket;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        if (!$this->isShown()) {
            return '';
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        $menuConfiguration = $this->menuConfiguration;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $homepageDetector = $this->homepageDetector;
        ob_start();
        include __DIR__ . '/content/menu.php';
        return ob_get_clean();
    }

    protected function isShown(): bool
    {
        if (!$this->ticket->canPassIn()) {
            return $this->getMenuConfiguration()->isShownOnGateway();
        }
        if ($this->homepageDetector->isHomepageRequested()) {
            return $this->getMenuConfiguration()->isShownOnHomepage();
        }
        return $this->getMenuConfiguration()->isShownOnRoutes();
    }

    protected function getMenuConfiguration(): MenuConfiguration
    {
        return $this->menuConfiguration;
    }
}
