<?php
namespace DrdPlus\FightCalculator;

use DrdPlus\CalculatorSkeleton\History;
use DrdPlus\RulesSkeleton\CookiesService;

class HistoryWithSkills extends History
{
    private const RANKS_HISTORY = 'ranks_history';

    /** @var array|string[] */
    private $skillToSkillRankNames;
    /** @var CookiesService */
    private $cookiesService;
    /** @var null|array */
    private $ranksHistory;

    /**
     * @param array|string[] $skillNamesToSkillRankNames
     * @param CookiesService $cookiesService ,
     * @param bool $deletePreviousHistory ,
     * @param array $valuesToRemember ,
     * @param bool $rememberHistory ,
     * @param string $cookiesPostfix ,
     * @param int $cookiesTtl = null
     */
    public function __construct(
        array $skillNamesToSkillRankNames,
        CookiesService $cookiesService,
        bool $deletePreviousHistory,
        array $valuesToRemember,
        bool $rememberHistory,
        string $cookiesPostfix,
        int $cookiesTtl = null
    )
    {
        $this->skillToSkillRankNames = $skillNamesToSkillRankNames;
        $this->cookiesService = $cookiesService;
        parent::__construct($cookiesService, $deletePreviousHistory, $valuesToRemember, $rememberHistory, $cookiesPostfix, $cookiesTtl);
    }

    protected function remember(array $valuesToRemember, \DateTime $cookiesTtlDate): void
    {
        parent::remember($valuesToRemember, $cookiesTtlDate);
        $this->addSelectedSkillsToHistory($valuesToRemember);
    }

    protected function deleteHistory(): void
    {
        parent::deleteHistory();
        $this->cookiesService->deleteCookie(self::RANKS_HISTORY);
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
        $this->cookiesService->setCookie(self::RANKS_HISTORY, $serialized);
    }

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