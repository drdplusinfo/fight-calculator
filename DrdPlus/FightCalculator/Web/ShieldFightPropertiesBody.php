<?php
declare(strict_types=1);

namespace DrdPlus\FightCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\FightCalculator\Fight;
use DrdPlus\FightProperties\FightProperties;
use Gt\Dom\Element;
use Gt\Dom\HTMLDocument;

class ShieldFightPropertiesBody extends FightPropertiesBody
{
    /** @var ItemHoldingCode */
    private $currentShieldHolding;
    /** @var ItemHoldingCode */
    private $previousShieldHolding;

    public function __construct(
        ItemHoldingCode $currentShieldHolding,
        ItemHoldingCode $previousShieldHolding,
        FightProperties $currentFightProperties,
        FightProperties $previousFightProperties,
        Fight $fight,
        HtmlHelper $htmlHelper
    )
    {
        parent::__construct($currentFightProperties, $previousFightProperties, $fight, $htmlHelper);
        $this->currentShieldHolding = $currentShieldHolding;
        $this->previousShieldHolding = $previousShieldHolding;
    }

    public function getValue(): string
    {
        $shieldFightProperties = parent::getValue();
        $document = new HTMLDocument(<<<HTML
<!DOCTYPE html>
<html lang="cs">
<head>
  <title></title>
  <meta charset="utf-8">
</head>
<body>
    <span id="hint" class="hint">se štítem <a href="https://pph.drdplus.info/#boj_se_zbrani">jako zbraň</a></span>
    <div id="content">
        {$shieldFightProperties}
        <div class="col note">
          držen
          <span class="keyword {$this->getCssClassForChangeOfShieldHolding()}">
              {$this->getCurrentShieldHoldingHumanName()}
          </span>
        </div>
    </div>
</body>
</html>
HTML
        );
        $hint = $document->getElementById('hint');
        foreach ($document->getElementsByClassName('fight-property') as $fightProperty) {
            $fightProperty->appendChild($hint);
        }
        /** @var Element $content */
        $content = $document->getElementById('content');
        return $content->prop_get_innerHTML();
    }

    private function getCssClassForChangeOfShieldHolding(): string
    {
        return $this->previousShieldHolding->getValue() !== $this->currentShieldHolding->getValue()
            ? 'changed'
            : '';
    }

    private function getCurrentShieldHoldingHumanName(): string
    {
        return $this->currentShieldHolding->translateTo('cs');
    }
}