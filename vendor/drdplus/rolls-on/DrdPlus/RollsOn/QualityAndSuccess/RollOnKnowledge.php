<?php declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use DrdPlus\RollsOn\Traps\ShortRollOnIntelligence;
use Granam\Integer\IntegerInterface;

/**
 * See PPH page 130 right column, @link https://pph.drdplus.info/#znalosti_postavy
 */
class RollOnKnowledge extends ExtendedRollOnSuccess
{
    public const FATAL_FAILURE = 'fatal_failure';
    public const DOES_NOT_KNOW_ANSWER = 'does_not_know_answer';
    public const KNOWS_ANSWER = 'knows_answer';
    public const COMPLETE_SUCCESS = 'complete_success';

    /**
     * @param ShortRollOnIntelligence $shortRollOnIntelligence
     * @param int|IntegerInterface $difficulty
     */
    public function __construct(ShortRollOnIntelligence $shortRollOnIntelligence, $difficulty)
    {
        parent::__construct(
            new SimpleRollOnSuccess($difficulty - 3, $shortRollOnIntelligence, self::DOES_NOT_KNOW_ANSWER, self::FATAL_FAILURE),
            new SimpleRollOnSuccess($difficulty, $shortRollOnIntelligence, self::KNOWS_ANSWER),
            new SimpleRollOnSuccess($difficulty + 3, $shortRollOnIntelligence, self::COMPLETE_SUCCESS)
        );
    }
}