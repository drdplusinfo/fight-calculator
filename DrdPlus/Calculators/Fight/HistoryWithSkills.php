<?php
namespace DrdPlus\Calculators\Fight;

use DrdPlus\Configurator\Skeleton\Cookie;
use DrdPlus\Configurator\Skeleton\History;

class HistoryWithSkills extends History
{
    private const RANKS_HISTORY = 'ranks_history';

    /** @var array|string[] */
    private $skillToSkillRankNames;

    /**
     * @param array|string[] $skillNamesToSkillRankNames
     * @param bool $deleteFightHistory
     * @param array $valuesToRemember
     * @param bool $rememberCurrent
     * @param string $cookiesPostfix
     * @param int $cookiesTtl = null
     */
    public function __construct(
        array $skillNamesToSkillRankNames,
        bool $deleteFightHistory,
        array $valuesToRemember,
        bool $rememberCurrent,
        string $cookiesPostfix,
        int $cookiesTtl = null
    )
    {
        $this->skillToSkillRankNames = $skillNamesToSkillRankNames;
        parent::__construct($deleteFightHistory, $valuesToRemember, $rememberCurrent, $cookiesPostfix, $cookiesTtl);
    }

    protected function remember(array $valuesToRemember, int $cookiesTtl): void
    {
        parent::remember($valuesToRemember, $cookiesTtl);
        $this->addSelectedSkillsToHistory($valuesToRemember);
    }

    protected function deleteHistory(): void
    {
        parent::deleteHistory();
        Cookie::setCookie(self::RANKS_HISTORY, null);
    }

    private function addSelectedSkillsToHistory(array $request): void
    {
        $skillsToSave = [];
        foreach ($this->skillToSkillRankNames as $skillName => $rankName
        ) {
            if (\array_key_exists($rankName, $request) && !empty($request[$skillName])) {
                // like melee_fight_skill => fight_unarmed => 0
                $skillsToSave[$rankName][$request[$skillName]] = $request[$rankName];
            }
        }
        if (\count($skillsToSave) === 0) {
            return;
        }
        $ranksHistory = $this->getRanksHistory();
        foreach ($skillsToSave as $rankName => $rankValues) {
            // changed values are replaced because of string keys
            $ranksHistory[$rankName] = \array_merge($ranksHistory[$rankName] ?? [], $rankValues);
        }
        $serialized = \serialize($ranksHistory);
        Cookie::setCookie(self::RANKS_HISTORY, $serialized);
    }

    /** @var null|array */
    private $ranksHistory;

    private function getRanksHistory(): array
    {
        if ($this->ranksHistory === null) {
            $this->ranksHistory = \unserialize($_COOKIE[self::RANKS_HISTORY] ?? '', ['allowed_classes' => false]) ?: [];
        }

        return $this->ranksHistory;
    }

    public function getPreviousSkillRanks(string $skillRankInputName): array
    {
        return $this->getRanksHistory()[$skillRankInputName] ?? [];
    }
}