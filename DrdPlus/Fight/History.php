<?php
namespace DrdPlus\Fight;

use Granam\Strict\Object\StrictObject;

class History extends StrictObject
{
    const FORGOT_FIGHT = 'forgot_fight';
    const FIGHT_HISTORY = 'fight_history';
    const RANKS_HISTORY = 'ranks_history';
    const FIGHT_HISTORY_TOKEN = 'fight_history_token';

    /** @var array */
    private $historyValues = [];
    /** @var array|string[] */
    private $skillToSkillRankNames;

    /**
     * @param array $valuesToRemember
     * @param bool $deleteFightHistory
     * @param array|string[] $skillNamesToSkillRankNames
     * @param bool $remember
     */
    public function __construct(
        array $skillNamesToSkillRankNames,
        bool $deleteFightHistory,
        array $valuesToRemember,
        bool $remember
    )
    {
        if ($deleteFightHistory) {
            $this->deleteHistory();
        }
        $this->skillToSkillRankNames = $skillNamesToSkillRankNames;
        if (count($valuesToRemember) > 0) {
            $year = (new \DateTime('+ 1 year'))->getTimestamp();
            if ($remember) {
                Cookie::setCookie(self::FORGOT_FIGHT, null, $year);
                Cookie::setCookie(self::FIGHT_HISTORY, serialize($valuesToRemember), $year);
                Cookie::setCookie(self::FIGHT_HISTORY_TOKEN, md5_file(__FILE__), $year);
                $this->addSelectedSkillsToHistory($valuesToRemember);
            } else {
                $this->deleteHistory();
                Cookie::setCookie(self::FORGOT_FIGHT, 1, $year);
            }
        } elseif (!$this->cookieHistoryIsValid()) {
            $this->deleteHistory();
        }
        if (!empty($_COOKIE[self::FIGHT_HISTORY])) {
            $this->historyValues = unserialize($_COOKIE[self::FIGHT_HISTORY], ['allowed_classes' => []]);
            if (!is_array($this->historyValues)) {
                $this->historyValues = [];
            }
        }
    }

    private function deleteHistory()
    {
        Cookie::setCookie(self::FIGHT_HISTORY_TOKEN, null);
        Cookie::setCookie(self::FIGHT_HISTORY, null);
        Cookie::setCookie(self::RANKS_HISTORY, null);
        $this->historyValues = [];
    }

    public function shouldRemember(): bool
    {
        return empty($_COOKIE[self::FORGOT_FIGHT]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (array_key_exists($name, $this->historyValues) && $this->cookieHistoryIsValid()) {
            return $this->historyValues[$name];
        }

        return null;
    }

    private function cookieHistoryIsValid(): bool
    {
        return !empty($_COOKIE[self::FIGHT_HISTORY_TOKEN])
            && $_COOKIE[self::FIGHT_HISTORY_TOKEN] === md5_file(__FILE__);
    }

    public function addSelectedSkillsToHistory(array $request)
    {
        $skillsToSave = [];
        foreach ($this->skillToSkillRankNames as $skillName => $rankName
        ) {
            if (array_key_exists($rankName, $request) && !empty($request[$skillName])) {
                // like melee_fight_skill => fight_unarmed => 0
                $skillsToSave[$rankName][$request[$skillName]] = $request[$rankName];
            }
        }
        if (count($skillsToSave) === 0) {
            return;
        }
        $ranksHistory = $this->getRanksHistory();
        foreach ($skillsToSave as $rankName => $rankValues) {
            // changed values are replaced because of string keys
            $ranksHistory[$rankName] = array_merge($ranksHistory[$rankName] ?? [], $rankValues);
        }
        $serialized = serialize($ranksHistory);
        Cookie::setCookie(self::RANKS_HISTORY, $serialized);
    }

    private function getRanksHistory(): array
    {
        return unserialize($_COOKIE[self::RANKS_HISTORY] ?? '', ['allowed_classes' => false]) ?: [];
    }

    public function getPreviousSkillRanks(string $skillRankInputName): array
    {
        return $this->getRanksHistory()[$skillRankInputName] ?? [];
    }
}