<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\DateTimeProvider;
use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\CalculatorSkeleton\StorageInterface;

class HistoryWithSkills extends History
{
    /** @var array|string[] */
    private $skillToSkillRankNames;
    /** @var null|array */
    private $ranksHistoryValues;
    /** @var StorageInterface */
    private $ranksHistoryStorage;

    /**
     * @param array|string[] $skillNamesToSkillRankNames
     * @param StorageInterface $historyStorage ,
     * @param DateTimeProvider $dateTimeProvider ,
     * @param StorageInterface $ranksHistoryStorage ,
     * @param null|int $ttl
     */
    public function __construct(
        array $skillNamesToSkillRankNames,
        StorageInterface $historyStorage,
        DateTimeProvider $dateTimeProvider,
        StorageInterface $ranksHistoryStorage,
        ?int $ttl
    )
    {
        $this->skillToSkillRankNames = $skillNamesToSkillRankNames;
        parent::__construct($historyStorage, $dateTimeProvider, $ttl);
        $this->ranksHistoryStorage = $ranksHistoryStorage;
    }

    public function deleteHistory(): void
    {
        $this->ranksHistoryStorage->deleteAll();
        parent::deleteHistory();
    }

    public function saveHistory(array $valuesToRemember): void
    {
        parent::saveHistory($valuesToRemember);
        $this->loadRanksHistoryValues(); // loads previous history as they would be overwritten now
        $ranksHistoryToSave = $this->getRanksHistoryToSave($valuesToRemember);
        $this->ranksHistoryStorage->storeValues($ranksHistoryToSave, $this->getTtlDate());
    }

    private function loadRanksHistoryValues(): void
    {
        $this->ranksHistoryValues = $this->ranksHistoryStorage->getValues();
    }

    private function getRanksHistoryToSave(array $valuesToRemember): array
    {
        $skillsToSave = [];
        foreach ($this->skillToSkillRankNames as $skillName => $rankName) {
            if (\array_key_exists($rankName, $valuesToRemember) && !empty($valuesToRemember[$skillName])) {
                // like melee_fight_skill => fight_unarmed => 0
                $skillsToSave[$rankName][$valuesToRemember[$skillName]] = $valuesToRemember[$rankName];
            }
        }
        if (\count($skillsToSave) === 0) {
            return [];
        }
        $ranksHistory = $this->getRanksHistory();
        foreach ($skillsToSave as $rankName => $rankValues) {
            // changed values are replaced because of string keys
            $ranksHistory[$rankName] = \array_merge($ranksHistory[$rankName] ?? [], $rankValues);
        }
        return $ranksHistory;
    }

    private function getRanksHistory(): array
    {
        if ($this->ranksHistoryValues === null) {
            $this->loadRanksHistoryValues();
        }
        return $this->ranksHistoryValues;
    }

    public function getPreviousSkillRanks(string $skillRankInputName): array
    {
        return $this->getRanksHistory()[$skillRankInputName] ?? [];
    }
}