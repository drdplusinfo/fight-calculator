<?php declare(strict_types=1);

namespace DrdPlus\Skills\Psychical;

use DrdPlus\Codes\Skills\SkillTypeCode;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Skills\SameTypeSkills;

class PsychicalSkills extends SameTypeSkills
{

    public const PSYCHICAL = SkillTypeCode::PSYCHICAL;

    /**
     * @var Astronomy
     */
    private $astronomy;
    /**
     * @var Botany
     */
    private $botany;
    /**
     * @var EtiquetteOfGangland
     */
    private $etiquetteOfGangland;
    /**
     * @var ForeignLanguage
     */
    private $foreignLanguage;
    /**
     * @var GeographyOfACountry
     */
    private $geographyOfACountry;
    /**
     * @var HandlingWithMagicalItems
     */
    private $handlingWithMagicalItems;
    /**
     * @var Historiography
     */
    private $historiography;
    /**
     * @var KnowledgeOfACity
     */
    private $knowledgeOfACity;
    /**
     * @var KnowledgeOfWorld
     */
    private $knowledgeOfWorld;
    /**
     * @var MapsDrawing
     */
    private $mapsDrawing;
    /**
     * @var Mythology
     */
    private $mythology;
    /**
     * @var ReadingAndWriting
     */
    private $readingAndWriting;
    /**
     * @var SocialEtiquette
     */
    private $socialEtiquette;
    /**
     * @var Technology
     */
    private $technology;
    /**
     * @var Theology
     */
    private $theology;
    /**
     * @var Zoology
     */
    private $zoology;

    protected function populateAllSkills(ProfessionLevel $professionLevel)
    {
        $this->astronomy = new Astronomy($professionLevel);
        $this->botany = new Botany($professionLevel);
        $this->etiquetteOfGangland = new EtiquetteOfGangland($professionLevel);
        $this->foreignLanguage = new ForeignLanguage($professionLevel);
        $this->geographyOfACountry = new GeographyOfACountry($professionLevel);
        $this->handlingWithMagicalItems = new HandlingWithMagicalItems($professionLevel);
        $this->historiography = new Historiography($professionLevel);
        $this->knowledgeOfACity = new KnowledgeOfACity($professionLevel);
        $this->knowledgeOfWorld = new KnowledgeOfWorld($professionLevel);
        $this->mapsDrawing = new MapsDrawing($professionLevel);
        $this->mythology = new Mythology($professionLevel);
        $this->readingAndWriting = new ReadingAndWriting($professionLevel);
        $this->socialEtiquette = new SocialEtiquette($professionLevel);
        $this->technology = new Technology($professionLevel);
        $this->theology = new Theology($professionLevel);
        $this->zoology = new Zoology($professionLevel);
    }

    public function getUnusedFirstLevelPsychicalSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedFirstLevelSkillPointsValue($this->getFirstLevelPhysicalPropertiesSum($professionLevels));
    }

    private function getFirstLevelPhysicalPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getFirstLevelWillModifier() + $professionLevels->getFirstLevelIntelligenceModifier();
    }

    public function getUnusedNextLevelsPsychicalSkillPointsValue(ProfessionLevels $professionLevels): int
    {
        return $this->getUnusedNextLevelsSkillPointsValue($this->getNextLevelsPsychicalPropertiesSum($professionLevels));
    }

    private function getNextLevelsPsychicalPropertiesSum(ProfessionLevels $professionLevels): int
    {
        return $professionLevels->getNextLevelsWillModifier() + $professionLevels->getNextLevelsIntelligenceModifier();
    }

    /**
     * @return \Traversable|\ArrayIterator|PsychicalSkill[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([
            $this->getAstronomy(),
            $this->getBotany(),
            $this->getEtiquetteOfGangland(),
            $this->getForeignLanguage(),
            $this->getGeographyOfACountry(),
            $this->getHandlingWithMagicalItems(),
            $this->getHistoriography(),
            $this->getKnowledgeOfACity(),
            $this->getKnowledgeOfWorld(),
            $this->getMapsDrawing(),
            $this->getMythology(),
            $this->getReadingAndWriting(),
            $this->getSocialEtiquette(),
            $this->getTechnology(),
            $this->getTheology(),
            $this->getZoology(),
        ]);
    }

    public function getAstronomy(): Astronomy
    {
        return $this->astronomy;
    }

    public function getBotany(): Botany
    {
        return $this->botany;
    }

    public function getEtiquetteOfGangland(): EtiquetteOfGangland
    {
        return $this->etiquetteOfGangland;
    }

    public function getForeignLanguage(): ForeignLanguage
    {
        return $this->foreignLanguage;
    }

    public function getGeographyOfACountry(): GeographyOfACountry
    {
        return $this->geographyOfACountry;
    }

    public function getHandlingWithMagicalItems(): HandlingWithMagicalItems
    {
        return $this->handlingWithMagicalItems;
    }

    public function getHistoriography(): Historiography
    {
        return $this->historiography;
    }

    public function getKnowledgeOfACity(): KnowledgeOfACity
    {
        return $this->knowledgeOfACity;
    }

    public function getKnowledgeOfWorld(): KnowledgeOfWorld
    {
        return $this->knowledgeOfWorld;
    }

    public function getMapsDrawing(): MapsDrawing
    {
        return $this->mapsDrawing;
    }

    public function getMythology(): Mythology
    {
        return $this->mythology;
    }

    public function getReadingAndWriting(): ReadingAndWriting
    {
        return $this->readingAndWriting;
    }

    public function getSocialEtiquette(): SocialEtiquette
    {
        return $this->socialEtiquette;
    }

    public function getTechnology(): Technology
    {
        return $this->technology;
    }

    public function getTheology(): Theology
    {
        return $this->theology;
    }

    public function getZoology(): Zoology
    {
        return $this->zoology;
    }

    public function getBonusToAttackNumberAgainstFreeWillAnimal(): int
    {
        return $this->getZoology()->getBonusToAttackNumberAgainstFreeWillAnimal();
    }

    public function getBonusToCoverAgainstFreeWillAnimal(): int
    {
        return $this->getZoology()->getBonusToCoverAgainstFreeWillAnimal();
    }

    public function getBonusToBaseOfWoundsAgainstFreeWillAnimal(): int
    {
        return $this->getZoology()->getBonusToBaseOfWoundsAgainstFreeWillAnimal();
    }
}