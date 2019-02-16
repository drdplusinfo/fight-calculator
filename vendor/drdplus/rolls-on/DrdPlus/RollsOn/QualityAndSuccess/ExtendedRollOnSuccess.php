<?php
declare(strict_types=1);

namespace DrdPlus\RollsOn\QualityAndSuccess;

use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

class ExtendedRollOnSuccess extends StrictObject implements RollOnSuccess
{

    /** @var SimpleRollOnSuccess[] */
    private $rollsOnSuccess;
    /** @var RollOnQuality */
    private $rollOnQuality;

    /**
     * @param SimpleRollOnSuccess $firstSimpleRollOnSuccess
     * @param SimpleRollOnSuccess|null $secondSimpleRollOnSuccess
     * @param SimpleRollOnSuccess|null $thirdSimpleRollOnSuccess
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\ExpectedSimpleRollsOnSuccessOnly
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EveryDifficultyShouldBeUnique
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EverySuccessCodeShouldBeUnique
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\RollOnQualityHasToBeTheSame
     */
    public function __construct(
        SimpleRollOnSuccess $firstSimpleRollOnSuccess,
        SimpleRollOnSuccess $secondSimpleRollOnSuccess = null,
        SimpleRollOnSuccess $thirdSimpleRollOnSuccess = null
        // any number of SimpleRollOnSuccess ...
    )
    {
        $this->rollsOnSuccess = $this->grabOrderedRollsOnSuccess(func_get_args());
        $this->rollOnQuality = $this->grabRollOnQuality($this->rollsOnSuccess);
    }

    /**
     * @param array $constructorArguments
     * @return array|SimpleRollOnSuccess[]
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\ExpectedSimpleRollsOnSuccessOnly
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EveryDifficultyShouldBeUnique
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EverySuccessCodeShouldBeUnique
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\RollOnQualityHasToBeTheSame
     */
    private function grabOrderedRollsOnSuccess(array $constructorArguments): array
    {
        $simpleRollsOnSuccess = $this->removeNulls($constructorArguments);
        $this->guardSimpleRollsOnSuccessOnly($simpleRollsOnSuccess);
        $this->guardUniqueDifficulties($simpleRollsOnSuccess);
        $this->guardUniqueSuccessCodes($simpleRollsOnSuccess);
        $this->guardSameRollOnQuality($simpleRollsOnSuccess);

        return $this->sortByDifficultyDescending($simpleRollsOnSuccess);
    }

    private function removeNulls(array $values): array
    {
        return \array_filter(
            $values,
            function ($value) {
                return $value !== null;
            }
        );
    }

    /**
     * @param array $simpleRollsOnSuccess
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\ExpectedSimpleRollsOnSuccessOnly
     */
    private function guardSimpleRollsOnSuccessOnly(array $simpleRollsOnSuccess)
    {
        foreach ($simpleRollsOnSuccess as $simpleRollOnSuccess) {
            if (!$simpleRollOnSuccess instanceof SimpleRollOnSuccess) {
                throw new Exceptions\ExpectedSimpleRollsOnSuccessOnly(
                    'Expected only ' . SimpleRollOnSuccess::class . ' (or null), got '
                    . ValueDescriber::describe($simpleRollOnSuccess)
                );
            }
        }
    }

    /**
     * @param array|SimpleRollOnSuccess[] $simpleRollsOnSuccess
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EveryDifficultyShouldBeUnique
     */
    private function guardUniqueDifficulties(array $simpleRollsOnSuccess)
    {
        $difficulties = [];
        /** @var SimpleRollOnSuccess $simpleRollOnSuccess */
        foreach ($simpleRollsOnSuccess as $simpleRollOnSuccess) {
            $difficulties[] = $simpleRollOnSuccess->getDifficulty();
        }
        if ($difficulties !== array_unique($difficulties)) {
            throw new Exceptions\EveryDifficultyShouldBeUnique(
                'Expected only unique difficulties, got ' . implode(',', $difficulties)
            );
        }
    }

    /**
     * @param array|SimpleRollOnSuccess[] $simpleRollsOnSuccess
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\EverySuccessCodeShouldBeUnique
     */
    private function guardUniqueSuccessCodes(array $simpleRollsOnSuccess)
    {
        $successCodes = [];
        foreach ($simpleRollsOnSuccess as $simpleRollOnSuccess) {
            if ($simpleRollOnSuccess->isSuccess()) {
                $successCodes[] = $simpleRollOnSuccess->getResult();
            }
        }
        if ($successCodes !== array_unique($successCodes)) {
            throw new Exceptions\EverySuccessCodeShouldBeUnique(
                'Expected only unique success codes, got ' . implode(',', $successCodes)
            );
        }
    }

    /**
     * @param array|SimpleRollOnSuccess[] $simpleRollsOnSuccess
     * @throws \DrdPlus\RollsOn\QualityAndSuccess\Exceptions\RollOnQualityHasToBeTheSame
     */
    private function guardSameRollOnQuality(array $simpleRollsOnSuccess)
    {
        /** @var RollOnQuality $rollOnQuality */
        $rollOnQuality = null;
        foreach ($simpleRollsOnSuccess as $simpleRollOnSuccess) {
            if ($rollOnQuality === null) {
                $rollOnQuality = $simpleRollOnSuccess->getRollOnQuality();
            } else {
                $secondRollOnQuality = $simpleRollOnSuccess->getRollOnQuality();
                if ($rollOnQuality->getValue() !== $secondRollOnQuality->getValue()
                    || $rollOnQuality->getPreconditionsSum() !== $secondRollOnQuality->getPreconditionsSum()
                    || $rollOnQuality->getRoll()->getValue() !== $secondRollOnQuality->getRoll()->getValue()
                    || $rollOnQuality->getRoll()->getRolledNumbers() !== $secondRollOnQuality->getRoll()->getRolledNumbers()
                ) {
                    throw new Exceptions\RollOnQualityHasToBeTheSame(
                        'Expected same roll of quality for every roll on success, got one with '
                        . $this->describeRollOnQuality($rollOnQuality)
                        . ' and another with ' . $this->describeRollOnQuality($simpleRollOnSuccess->getRollOnQuality())
                    );
                }
            }
        }
    }

    private function describeRollOnQuality(RollOnQuality $rollOnQuality): string
    {
        return "sum of preconditions: {$rollOnQuality->getPreconditionsSum()}, value: {$rollOnQuality->getValue()}"
            . ", roll value {$rollOnQuality->getRoll()->getValue()}, rolled numbers "
            . \implode(',', $rollOnQuality->getRoll()->getRolledNumbers());
    }

    /**
     * @param array|SimpleRollOnSuccess[] $simpleRollsOnSuccess
     * @return array|SimpleRollOnSuccess[]
     */
    private function sortByDifficultyDescending(array $simpleRollsOnSuccess): array
    {
        \usort($simpleRollsOnSuccess, function (SimpleRollOnSuccess $simpleRollOnSuccess, SimpleRollOnSuccess $anotherSimpleRollOnSuccess) {
            // with lesser difficulty on top (descending order)
            return $anotherSimpleRollOnSuccess->getDifficulty() <=> $simpleRollOnSuccess->getDifficulty();
        });

        return $simpleRollsOnSuccess;
    }

    /**
     * @param array|SimpleRollOnSuccess[] $simpleRollsOnSuccess
     * @return RollOnQuality
     */
    private function grabRollOnQuality(array $simpleRollsOnSuccess): RollOnQuality
    {
        /** @var SimpleRollOnSuccess $simpleRollOnSuccess */
        $simpleRollOnSuccess = current($simpleRollsOnSuccess);

        return $simpleRollOnSuccess->getRollOnQuality();
    }

    public function getRollOnQuality(): RollOnQuality
    {
        return $this->rollOnQuality;
    }

    public function isSuccess(): bool
    {
        return $this->getResultSimpleRollOnSuccess()->isSuccess();
    }

    protected function getResultSimpleRollOnSuccess(): SimpleRollOnSuccess
    {
        foreach ($this->rollsOnSuccess as $rollOnSuccess) {
            if ($rollOnSuccess->isSuccess()) {
                return $rollOnSuccess; // the first successful roll (they are ordered from highest difficulty)
            }
        }

        return \end($this->rollsOnSuccess); // the roll with lowest (yet not passed) difficulty
    }

    public function isFailure(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * @return string|int|float|bool
     */
    public function getResult()
    {
        return $this->getResultSimpleRollOnSuccess()->getResult();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getResultSimpleRollOnSuccess()->getResult();
    }
}