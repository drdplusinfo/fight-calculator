<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton\Web;

use DrdPlus\RulesSkeleton\HtmlHelper;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class CalculatorDebugContactsBody extends StrictObject implements BodyInterface
{
    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        $classWithoutAnchorToId = HtmlHelper::CLASS_WITHOUT_ANCHOR_TO_ID;
        return <<<HTML
<div id="debug_contacts" class="{$classWithoutAnchorToId}">
  Máš nápad 😀? Vidíš chybu 😱?️ Pošli nám to
  <a href="mailto:info@drdplus.info">mailem</a>, nahlaš na
  <a href="https://www.facebook.com/drdplus.info">Facebooku</a> nebo
  <a href="https://rpgforum.cz/forum/viewtopic.php?f=238&t=14870">řekni na rpgforu
    <img alt="RPG forum icon" src="/images/generic/skeleton/rules-rpgforum-ico.png">
  </a>. Děkujeme!
</div>
HTML;
    }

}
