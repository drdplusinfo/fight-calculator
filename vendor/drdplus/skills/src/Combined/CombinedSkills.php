<?php declare(strict_types=1);

namespace DrdPlus\Skills\Combined;

use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Skills\SkillTypeCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Skills\SameTypeSkills;
use DrdPlus\Tables\Tables;

class CombinedSkills extends SameTypeSkills
{
    public const COMBINED = SkillTypeCode::COMBINED;

    private ?\DrdPlus\Skills\Combined\BigHandwork $bigHandwork = null;
    private ?\DrdPlus\Skills\Combined\Cooking $cooking = null;
    private ?\DrdPlus\Skills\Combined\Dancing $dancing = null;
    private ?\DrdPlus\Skills\Combined\DuskSight $duskSight = null;
    private ?\DrdPlus\Skills\Combined\FightWithBows $fightWithBows = null;
    private ?\DrdPlus\Skills\Combined\FightWithCrossbows $fightWithCrossbows = null;
    private ?\DrdPlus\Skills\Combined\FirstAid $firstAid = null;
    private ?\DrdPlus\Skills\Combined\HandlingWithAnimals $handlingWithAnimals = null;
    private ?\DrdPlus\Skills\Combined\Handwork $handwork = null;
    private ?\DrdPlus\Skills\Combined\Gambling $gambling = null;
    private ?\DrdPlus\Skills\Combined\Herbalism $herbalism = null;
    private ?\DrdPlus\Skills\Combined\HuntingAndFishing $huntingAndFishing = null;
    private ?\DrdPlus\Skills\Combined\Knotting $knotting = null;
    private ?\DrdPlus\Skills\Combined\Painting $painting = null;
    private ?\DrdPlus\Skills\Combined\Pedagogy $pedagogy = null;
    private ?\DrdPlus\Skills\Combined\PlayingOnMusicInstrument $playingOnMusicInstrument = null;
    private ?\DrdPlus\Skills\Combined\Seduction $seduction = null;
    private ?\DrdPlus\Skills\Combined\Showmanship $showmanship = null;
    private ?\DrdPlus\Skills\Combined\Singing $singing = null;
    private ?\DrdPlus\Skills\Combined\Statuary $statuary = null;
    private ?\DrdPlus\Skills\Combined\Teaching $teaching = null;

    protected function populateAllSkills(ProfessionLevel $professionLevel)
    {
        $this->bigHandwork = new BigHandwork($professionLevel);
        $this->cooking = new Cooking($professionLevel);
        $this->dancing = new Dancing($professionLevel);
        $this->duskSight = new DuskSight($professionLevel);
        $this->fightWithBows = new FightWithBows($professionLevel);
        $this->fightWithCrossbows = new FightWithCrossbows($professionLevel);
        $this->firstAid = new FirstAid($professionLevel);
        $this->gambling = new Gambling($professionLevel);
        $this->handlingWithAnimals = new HandlingWithAnimals($professionLevel);
        $this->handwork = new Handwork($professionLevel);
        $this->herbalism = new Herbalism($professionLevel);
        $this->huntingAndFishing = new HuntingAndFishing($professionLevel);
        $this->knotting = new Knotting($professionLevel);
        $this->painting = new Painting($professionLevel);
        $this->pedagogy = new Pedagogy($professionLevel);
        $this->playingOnMusicInstrument = new PlayingOnMusicInstrument($professionLevel);
        $this->seduction = new Seduction($professionLevel);
        $this->showmanship = new Showmanship($professionLevel);
        $this->singing = new Singing($professionLevel);
        $this->statuary = new Statuary($professionLevel);
        $this->teaching = new Teaching($professionLevel);
    }

    public function getUnusedFirstLevelCombinedSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedFirstLevelSkillPointsValue($this->getFirstLevelCombinedPropertiesSum($professionLevels));
    }

    private function getFirstLevelCombinedPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getFirstLevelKnackModifier() + $professionLevels->getFirstLevelCharismaModifier();
    }

    public function getUnusedNextLevelsCombinedSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedNextLevelsSkillPointsValue($this->getNextLevelsCombinedPropertiesSum($professionLevels));
    }

    private function getNextLevelsCombinedPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getNextLevelsKnackModifier() + $professionLevels->getNextLevelsCharismaModifier();
    }

    /**
     * @return \Traversable|\ArrayIterator|CombinedSkill[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([
            $this->getBigHandwork(),
            $this->getCooking(),
            $this->getDancing(),
            $this->getDuskSight(),
            $this->getFightWithBows(),
            $this->getFightWithCrossbows(),
            $this->getFirstAid(),
            $this->getGambling(),
            $this->getHandlingWithAnimals(),
            $this->getHandwork(),
            $this->getHerbalism(),
            $this->getHuntingAndFishing(),
            $this->getKnotting(),
            $this->getPainting(),
            $this->getPedagogy(),
            $this->getPlayingOnMusicInstrument(),
            $this->getSeduction(),
            $this->getShowmanship(),
            $this->getSinging(),
            $this->getStatuary(),
            $this->getTeaching(),
        ]);
    }

    public function getBigHandwork(): BigHandwork
    {
        return $this->bigHandwork;
    }

    public function getCooking(): Cooking
    {
        return $this->cooking;
    }

    public function getDancing(): Dancing
    {
        return $this->dancing;
    }

    public function getDuskSight(): DuskSight
    {
        return $this->duskSight;
    }

    public function getFightWithBows(): FightWithBows
    {
        return $this->fightWithBows;
    }

    public function getFightWithCrossbows(): FightWithCrossbows
    {
        return $this->fightWithCrossbows;
    }

    public function getFirstAid(): FirstAid
    {
        return $this->firstAid;
    }

    public function getHandlingWithAnimals(): HandlingWithAnimals
    {
        return $this->handlingWithAnimals;
    }

    public function getHandwork(): Handwork
    {
        return $this->handwork;
    }

    public function getGambling(): Gambling
    {
        return $this->gambling;
    }

    public function getHerbalism(): Herbalism
    {
        return $this->herbalism;
    }

    public function getHuntingAndFishing(): HuntingAndFishing
    {
        return $this->huntingAndFishing;
    }

    public function getKnotting(): Knotting
    {
        return $this->knotting;
    }

    public function getPainting(): Painting
    {
        return $this->painting;
    }

    public function getPedagogy(): Pedagogy
    {
        return $this->pedagogy;
    }

    public function getPlayingOnMusicInstrument(): PlayingOnMusicInstrument
    {
        return $this->playingOnMusicInstrument;
    }

    public function getSeduction(): Seduction
    {
        return $this->seduction;
    }

    public function getShowmanship(): Showmanship
    {
        return $this->showmanship;
    }

    public function getSinging(): Singing
    {
        return $this->singing;
    }

    public function getStatuary(): Statuary
    {
        return $this->statuary;
    }

    public function getTeaching(): Teaching
    {
        return $this->teaching;
    }

    /**
     * @param RangedWeaponCode $rangeWeaponCode
     * @param Tables $tables
     * @return int
     * @throws \DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon
     */
    public function getMalusToFightNumberWithShootingWeapon(RangedWeaponCode $rangeWeaponCode, Tables $tables): int
    {
        $rankValue = $this->getFightWithShootingWeaponRankValue($rangeWeaponCode);

        return $tables->getMissingWeaponSkillTable()->getFightNumberMalusForSkillRank($rankValue);
    }

    /**
     * @param RangedWeaponCode $rangeWeaponCode
     * @return int
     * @throws \DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon
     */
    private function getFightWithShootingWeaponRankValue(RangedWeaponCode $rangeWeaponCode): int
    {
        if ($rangeWeaponCode->isBow()) {
            return $this->getFightWithBows()->getCurrentSkillRank()->getValue();
        }
        if ($rangeWeaponCode->isCrossbow()) {
            return $this->getFightWithCrossbows()->getCurrentSkillRank()->getValue();
        }
        throw new Exceptions\CombinedSkillsDoNotHowToUseThatWeapon(
            "Given range weapon {$rangeWeaponCode} is not affected by combined skills"
            . ' (only shooting weapons using knack are)'
        );
    }

    /**
     * @param RangedWeaponCode $rangeWeaponCode
     * @param Tables $tables
     * @return int
     * @throws \DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon
     */
    public function getMalusToAttackNumberWithShootingWeapon(RangedWeaponCode $rangeWeaponCode, Tables $tables): int
    {
        $rankValue = $this->getFightWithShootingWeaponRankValue($rangeWeaponCode);

        return $tables->getMissingWeaponSkillTable()->getAttackNumberMalusForSkillRank($rankValue);
    }

    /**
     * @param RangedWeaponCode $rangeWeaponCode
     * @param Tables $tables
     * @return int
     * @throws \DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon
     */
    public function getMalusToCoverWithShootingWeapon(RangedWeaponCode $rangeWeaponCode, Tables $tables): int
    {
        $rankValue = $this->getFightWithShootingWeaponRankValue($rangeWeaponCode);

        return $tables->getMissingWeaponSkillTable()->getCoverMalusForSkillRank($rankValue);
    }

    /**
     * @param RangedWeaponCode $rangeWeaponCode
     * @param Tables $tables
     * @return int
     * @throws \DrdPlus\Skills\Combined\Exceptions\CombinedSkillsDoNotHowToUseThatWeapon
     */
    public function getMalusToBaseOfWoundsWithShootingWeapon(RangedWeaponCode $rangeWeaponCode, Tables $tables): int
    {
        $rankValue = $this->getFightWithShootingWeaponRankValue($rangeWeaponCode);

        return $tables->getMissingWeaponSkillTable()->getBaseOfWoundsMalusForSkillRank($rankValue);
    }
}
