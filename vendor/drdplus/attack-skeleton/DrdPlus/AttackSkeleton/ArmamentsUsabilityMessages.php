<?php
declare(strict_types=1);

namespace DrdPlus\AttackSkeleton;

use Granam\Strict\Object\StrictObject;

class ArmamentsUsabilityMessages extends StrictObject
{
    /** @var array|string[][] */
    private $messagesAbout = [];
    /** @var PossibleArmaments */
    private $possibleArmaments;

    public function __construct(PossibleArmaments $possibleArmaments)
    {
        $this->possibleArmaments = $possibleArmaments;
    }

    public function getMessagesAboutMeleeWeapons(): array
    {
        if (($this->messagesAbout['melee'] ?? null) === null) {
            $countOfUnusable = 0;
            foreach ($this->possibleArmaments->getPossibleMeleeWeapons() as $weaponCodesOfSameCategory) {
                $countOfUnusable += $this->countUnusable($weaponCodesOfSameCategory);
            }
            $this->addUnusableMessage($countOfUnusable, 'melee', 'zbraň na blízko', 'zbraně na blízko', 'zbraní na blízko');
        }

        return $this->messagesAbout['melee'];
    }

    /**
     * @param array|bool[][] $items
     * @return int
     */
    private function countUnusable(array $items): int
    {
        $count = 0;
        foreach ($items as $item) {
            if (!$item['canUseIt']) {
                $count++;
            }
        }

        return $count;
    }

    private function addUnusableMessage(int $countOfUnusable, string $key, string $single, string $few, string $many): void
    {
        $this->messagesAbout[$key] = $this->messagesAbout[$key] ?? [];
        if ($countOfUnusable > 0) {
            $word = $single;
            if ($countOfUnusable >= 5) {
                $word = $many;
            } elseif ($countOfUnusable >= 2) {
                $word = $few;
            }
            $this->messagesAbout[$key]['unusable'] = "Kvůli chybějící síle nemůžeš použít $countOfUnusable $word.";
        }
    }

    public function getMessagesAboutRangedWeapons(): array
    {
        if (($this->messagesAbout['ranged'] ?? null) === null) {
            $countOfUnusable = 0;
            foreach ($this->possibleArmaments->getPossibleRangedWeapons() as $weaponCodesOfSameCategory) {
                $countOfUnusable += $this->countUnusable($weaponCodesOfSameCategory);
            }
            $this->addUnusableMessage($countOfUnusable, 'ranged', 'zbraň na dálku', 'zbraně na dálku', 'zbraní na dálku');
        }

        return $this->messagesAbout['ranged'];
    }

    public function getMessagesAboutShields(): array
    {
        if (($this->messagesAbout['shields'] ?? null) === null) {
            $countOfUnusable = $this->countUnusable($this->possibleArmaments->getPossibleShields());
            $this->addUnusableMessage($countOfUnusable, 'shields', 'štít', 'štíty', 'štítů');
        }

        return $this->messagesAbout['shields'];
    }

    public function getMessagesAboutHelms(): array
    {
        if (($this->messagesAbout['helms'] ?? null) === null) {
            $this->addUnusableMessage($this->countUnusable($this->possibleArmaments->getPossibleHelms()), 'helms', 'helmu', 'helmy', 'helem');
        }

        return $this->messagesAbout['helms'];
    }

    public function getMessagesAboutBodyArmors(): array
    {
        if (($this->messagesAbout['armors'] ?? null) === null) {
            $countOfUnusable = $this->countUnusable($this->possibleArmaments->getPossibleBodyArmors());
            $this->addUnusableMessage($countOfUnusable, 'armors', 'zbroj', 'zbroje', 'zrojí');
        }

        return $this->messagesAbout['armors'];
    }

}