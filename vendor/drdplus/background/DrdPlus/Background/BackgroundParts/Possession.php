<?php
declare(strict_types = 1);

namespace DrdPlus\Background\BackgroundParts;

use DrdPlus\Codes\History\ExceptionalityCode;
use DrdPlus\Background\BackgroundParts\Partials\AbstractAncestryDependent;
use DrdPlus\Tables\Measurements\Price\Price;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveInteger;

class Possession extends AbstractAncestryDependent
{
    /**
     * @param PositiveInteger $spentBackgroundPoints
     * @param Ancestry $ancestry
     * @param Tables $tables
     * @return Possession|AbstractAncestryDependent
     * @throws \DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints
     */
    public static function getIt(PositiveInteger $spentBackgroundPoints, Ancestry $ancestry, Tables $tables)
    {
        return self::createIt($spentBackgroundPoints, $ancestry, $tables);
    }

    public static function getExceptionalityCode(): ExceptionalityCode
    {
        return ExceptionalityCode::getIt(ExceptionalityCode::POSSESSION);
    }

    /**
     * @var Price
     */
    private $belongingsPrice;

    public function getPrice(Tables $tables): Price
    {
        if ($this->belongingsPrice === null) {
            $priceValue = $tables->getPossessionTable()->getPossessionAsGoldCoins($this->getSpentBackgroundPoints());
            $this->belongingsPrice = new Price($priceValue, Price::GOLD_COIN);
        }

        return $this->belongingsPrice;
    }

}