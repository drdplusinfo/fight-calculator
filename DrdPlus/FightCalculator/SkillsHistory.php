<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\DateTimeProvider;
use DrdPlus\CalculatorSkeleton\StorageInterface;
use Granam\Strict\Object\StrictObject;

class SkillsHistory extends StrictObject
{
    /** @var array|string[] */
    private $skillToSkillRankNames;
    /** @var null|array */
    private $ranksHistoryValues;
    /** @var StorageInterface */
    private $ranksHistoryStorage;
    /** @var DateTimeProvider */
    private $dateTimeProvider;
    /** @var int|null */
    private $ttl;
    /** @var \DateTimeImmutable */
    private $ttlDate;

    /**
     * @param array|string[] $skillNamesToSkillRankNames
     * @param DateTimeProvider $dateTimeProvider
     * @param StorageInterface $ranksHistoryStorage
     * @param null|int $ttl
     */
    public function __construct(
        array $skillNamesToSkillRankNames,
        DateTimeProvider $dateTimeProvider,
        StorageInterface $ranksHistoryStorage,
        ?int $ttl
    )
    {
        $this->skillToSkillRankNames = $skillNamesToSkillRankNames;
        $this->ranksHistoryStorage = $ranksHistoryStorage;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->ttl = $ttl;
    }

    public function deleteSkillsHistory(): void
    {
        $this->ranksHistoryStorage->deleteAll();
    }

    public function saveSkillsHistory(array $valuesToRemember): void
    {
        $this->loadRanksHistoryValues(); // loads previous history as they would be overwritten now
        $ranksHistoryToSave = $this->getRanksHistoryToSave($valuesToRemember);
        $this->ranksHistoryStorage->storeValues($ranksHistoryToSave, $this->getTtlDate());
    }

    protected function getTtlDate(): \DateTimeImmutable
    {
        if ($this->ttlDate === null) {
            $this->ttlDate = $this->ttl !== null
                ? $this->dateTimeProvider->getNow()->modify('+' . $this->ttl . ' seconds')
                : $this->dateTimeProvider->getNow()->modify('+ 1 year');
        }
        return $this->ttlDate;
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