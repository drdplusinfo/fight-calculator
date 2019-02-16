<?php
declare(strict_types=1);

namespace DrdPlus\Skills;

use DrdPlus\Codes\Armaments\ShieldCode;
use DrdPlus\Codes\Armaments\WeaponCode;
use DrdPlus\Codes\Skills\CombinedSkillCode;
use DrdPlus\Codes\Skills\PhysicalSkillCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\Skills\PsychicalSkillCode;
use DrdPlus\Codes\Armaments\RangedWeaponCode;
use DrdPlus\Codes\Armaments\WeaponlikeCode;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Person\ProfessionLevels\LevelRank;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevels;
use DrdPlus\Person\ProfessionLevels\ProfessionNextLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Professions\Fighter;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Skills\Combined\BigHandwork;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\Cooking;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Combined\CombinedSkills;
use DrdPlus\Skills\Physical\Athletics;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Physical\PhysicalSkills;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Physical\Swimming;
use DrdPlus\Skills\Psychical\Astronomy;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkills;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use DrdPlus\Skills\Psychical\ReadingAndWriting;
use DrdPlus\Professions\Profession;
use DrdPlus\Armourer\Armourer;
use DrdPlus\Tables\Armaments\Shields\ShieldUsageSkillTable;
use DrdPlus\Tables\Armaments\Weapons\MissingWeaponSkillTable;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class SkillsTest extends TestWithMockery
{

    /**
     * @test
     * @dataProvider provideValidSkillsCombination
     * @param ProfessionLevels $professionLevels
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param PhysicalSkills $physicalSkills
     * @param PsychicalSkills $psychicalSkills
     * @param CombinedSkills $combinedSkills
     */
    public function I_can_create_it(
        ProfessionLevels $professionLevels,
        SkillPointsFromBackground $skillsFromBackground,
        PhysicalSkills $physicalSkills,
        PsychicalSkills $psychicalSkills,
        CombinedSkills $combinedSkills
    )
    {
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );

        self::assertSame($physicalSkills, $skills->getPhysicalSkills());
        self::assertSame($psychicalSkills, $skills->getPsychicalSkills());
        self::assertSame($combinedSkills, $skills->getCombinedSkills());
        self::assertEquals(
            $sortedExpectedSkills = $this->getSortedExpectedSkills(
                $physicalSkills->getIterator()->getArrayCopy(),
                $psychicalSkills->getIterator()->getArrayCopy(),
                $combinedSkills->getIterator()->getArrayCopy()
            ),
            $this->getSortedGivenSkills($skills)
        );
        self::assertSame(
            \array_merge(
                PhysicalSkillCode::getPossibleValues(),
                PsychicalSkillCode::getPossibleValues(),
                CombinedSkillCode::getPossibleValues()
            ),
            $skills->getCodesOfAllSkills()
        );
        $learnedSkills = $skills->getCodesOfLearnedSkills();
        \sort($learnedSkills);
        self::assertEquals(
            $expectedCodesOfLearnedSkills = \array_map(
                function (Skill $skill) {
                    return $skill->getName();
                },
                $sortedExpectedSkills
            ),
            $learnedSkills
        );
        self::assertNotEmpty($expectedCodesOfLearnedSkills);
        self::assertEquals(
            \array_diff($this->getAllSkillCodes(), $expectedCodesOfLearnedSkills),
            $skills->getCodesOfNotLearnedSkills()
        );
        self::assertEquals(
            $skills->getIterator()->getArrayCopy(),
            \array_merge(
                $physicalSkills->getIterator()->getArrayCopy(),
                $psychicalSkills->getIterator()->getArrayCopy(),
                $combinedSkills->getIterator()->getArrayCopy()
            )
        );
        self::assertCount(\count($sortedExpectedSkills), $skills);
    }

    /**
     * @return array|string[]
     */
    private function getAllSkillCodes(): array
    {
        return \array_merge(
            PhysicalSkillCode::getPossibleValues(),
            PsychicalSkillCode::getPossibleValues(),
            CombinedSkillCode::getPossibleValues()
        );
    }

    public function provideValidSkillsCombination(): array
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $nextLevels = $professionLevels->getProfessionNextLevels();
        $nextLevel = \end($nextLevels);

        return [
            [
                $professionLevels,
                $skillsFromBackground,
                $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
                $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
                $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            ],
            [
                $professionLevels,
                $skillsFromBackground,
                $this->createPhysicalSkillsPaidByOtherSkillPoints($skillsFromBackground, $firstLevel),
                $this->createPsychicalSkillsPaidByOtherSkillPoints($skillsFromBackground, $firstLevel),
                $this->createCombinedSkillsPaidByOtherSkillPoints($skillsFromBackground, $firstLevel),
            ],
            [
                $professionLevels,
                $skillsFromBackground,
                $this->createPhysicalSkillsByNextLevelPropertyIncrease($nextLevel),
                $this->createPsychicalSkillsByNextLevelPropertyIncrease($nextLevel),
                $this->createCombinedSkillsByNextLevelPropertyIncrease($nextLevel),
            ],
        ];
    }

    /**
     * @param ProfessionLevel $firstLevel
     * @param SkillPointsFromBackground $skillsFromBackground
     * @return \Mockery\MockInterface|PhysicalSkills
     * */
    private function createPhysicalSkillsPaidByFirstLevelBackground(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel
    )
    {
        $physicalSkills = $this->createSkillsPaidByFirstLevelBackground(
            $skillsFromBackground, $firstLevel, Swimming::class
        );

        return $physicalSkills;
    }

    /**
     * @param ProfessionLevel $firstLevel
     * @param SkillPointsFromBackground $skillsFromBackground
     * @return PsychicalSkills|\Mockery\MockInterface
     * */
    private function createPsychicalSkillsPaidByFirstLevelBackground(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel
    )
    {
        $psychicalSkills = $this->createSkillsPaidByFirstLevelBackground(
            $skillsFromBackground, $firstLevel, ReadingAndWriting::class
        );

        return $psychicalSkills;
    }

    /**
     * @param ProfessionLevel $firstLevel
     * @param SkillPointsFromBackground $skillsFromBackground
     * @return CombinedSkills|\Mockery\MockInterface
     * */
    private function createCombinedSkillsPaidByFirstLevelBackground(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel
    )
    {
        $combinedSkills = $this->createSkillsPaidByFirstLevelBackground(
            $skillsFromBackground,
            $firstLevel,
            Cooking::class
        );

        return $combinedSkills;
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @param string $firstSkillClass
     * @return \Mockery\MockInterface|CombinedSkills
     */
    private function createSkillsPaidByFirstLevelBackground(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel,
        $firstSkillClass
    )
    {
        $skillsClass = $this->determineSkillsClass($firstSkillClass);
        $skills = $this->mockery($skillsClass);
        $skills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator(
                [$firstSkill = $this->mockery(Skill::class)]
            ));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($firstSkillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$firstSkillRank = $this->mockery(SkillRank::class)]);
        $firstSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $firstSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($firstSkillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground);

        return $skills;
    }

    /**
     * @param string $skillClass
     * @return string
     * @throws \LogicException
     */
    private function determineSkillsClass($skillClass): string
    {
        if (\is_a($skillClass, PhysicalSkill::class, true)) {
            return PhysicalSkills::class;
        }
        if (\is_a($skillClass, PsychicalSkill::class, true)) {
            return PsychicalSkills::class;
        }
        if (\is_a($skillClass, CombinedSkill::class, true)) {
            return CombinedSkills::class;
        }
        throw new \LogicException;
    }

    /**
     * @param string $skillClass
     * @return string
     */
    private function parseSkillName($skillClass): string
    {
        self::assertEquals(1, preg_match('~[\\\](?<basename>\w+)$~', $skillClass, $matches));
        $sutBasename = $matches['basename'];
        $underscored = preg_replace('~([a-z])([A-Z])~', '$1_$2', $sutBasename);
        $underscoredSingleLetters = preg_replace('~([A-Z])([A-Z])~', '$1_$2', $underscored);

        return strtolower($underscoredSingleLetters);
    }

    private function determineSkillPointClass($skillClass): string
    {
        if (\is_a($skillClass, PhysicalSkill::class, true)) {
            return PhysicalSkillPoint::class;
        }
        if (\is_a($skillClass, PsychicalSkill::class, true)) {
            return PsychicalSkillPoint::class;
        }
        if (\is_a($skillClass, CombinedSkill::class, true)) {
            return CombinedSkillPoint::class;
        }
        throw new \LogicException;
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @return PsychicalSkills|\Mockery\MockInterface
     */
    private function createPhysicalSkillsPaidByOtherSkillPoints(SkillPointsFromBackground $skillsFromBackground, ProfessionLevel $firstLevel)
    {
        $psychicalSkills = $this->createSkillsPaidByOtherSkillPoints(
            $skillsFromBackground, $firstLevel, Athletics::class, Cooking::class, ReadingAndWriting::class
        );

        return $psychicalSkills;
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @return PsychicalSkills|\Mockery\MockInterface
     */
    private function createPsychicalSkillsPaidByOtherSkillPoints(SkillPointsFromBackground $skillsFromBackground, ProfessionLevel $firstLevel)
    {
        $psychicalSkills = $this->createSkillsPaidByOtherSkillPoints(
            $skillsFromBackground, $firstLevel, ReadingAndWriting::class, Cooking::class, Athletics::class
        );

        return $psychicalSkills;
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @return PsychicalSkills|\Mockery\MockInterface
     */
    private function createCombinedSkillsPaidByOtherSkillPoints(SkillPointsFromBackground $skillsFromBackground, ProfessionLevel $firstLevel)
    {
        $psychicalSkills = $this->createSkillsPaidByOtherSkillPoints(
            $skillsFromBackground, $firstLevel, Cooking::class, ReadingAndWriting::class, Athletics::class
        );

        return $psychicalSkills;
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @param $firstSkillClass
     * @param $firstOtherSkillClass
     * @param $secondOtherSkillClass
     * @return MockInterface
     */
    private function createSkillsPaidByOtherSkillPoints(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel,
        $firstSkillClass,
        $firstOtherSkillClass,
        $secondOtherSkillClass
    ): MockInterface
    {
        $skills = $this->mockery($this->determineSkillsClass($firstSkillClass));
        $skills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator(
                [$firstSkill = $this->mockery(Skill::class)]
            ));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($firstSkillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$firstSkillRank = $this->mockery(SkillRank::class)]);
        $firstSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $firstSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($firstSkillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByOtherSkillPoints')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getFirstPaidOtherSkillPoint')
            ->andReturn($firstPaidOtherSkillPoint = $this->mockery($this->determineSkillPointClass($firstOtherSkillClass)));
        $firstPaidOtherSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstPaidOtherSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstOtherSkillClass));
        $firstSkillPoint->shouldReceive('getSecondPaidOtherSkillPoint')
            ->andReturn($secondPaidOtherSkillPoint = $this->mockery($this->determineSkillPointClass($secondOtherSkillClass)));
        $secondPaidOtherSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $secondPaidOtherSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($secondOtherSkillClass));
        $firstPaidOtherSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $secondPaidOtherSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $secondPaidOtherSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground);
        $firstPaidOtherSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground);

        return $skills;
    }

    private function determineSkillTypeName($skillClass): string
    {
        if (\is_a($skillClass, PhysicalSkill::class, true)) {
            return PhysicalSkillPoint::PHYSICAL;
        }
        if (\is_a($skillClass, PsychicalSkill::class, true)) {
            return PsychicalSkillPoint::PSYCHICAL;
        }
        if (\is_a($skillClass, CombinedSkill::class, true)) {
            return CombinedSkillPoint::COMBINED;
        }
        throw new \LogicException;
    }

    /**
     * @param ProfessionLevel $nextLevel
     * @return PhysicalSkills
     */
    private function createPhysicalSkillsByNextLevelPropertyIncrease(ProfessionLevel $nextLevel): PhysicalSkills
    {
        $physicalSkillPoints = $this->createSkillsByNextLevelPropertyIncrease($nextLevel, Athletics::class);

        return $physicalSkillPoints;
    }

    /**
     * @param ProfessionLevel $nextLevel
     * @return PsychicalSkills
     */
    private function createPsychicalSkillsByNextLevelPropertyIncrease(ProfessionLevel $nextLevel): PsychicalSkills
    {
        $psychicalSkillPoints = $this->createSkillsByNextLevelPropertyIncrease($nextLevel, ReadingAndWriting::class);

        return $psychicalSkillPoints;
    }

    /**
     * @param ProfessionLevel $nextLevel
     * @return CombinedSkills
     */
    private function createCombinedSkillsByNextLevelPropertyIncrease(ProfessionLevel $nextLevel): CombinedSkills
    {
        $combinedSkills = $this->createSkillsByNextLevelPropertyIncrease($nextLevel, Cooking::class);

        return $combinedSkills;
    }

    /**
     * @param ProfessionLevel $nextLevel
     * @param $skillClass
     * @return MockInterface|CombinedSkills|PsychicalSkills|PhysicalSkills
     */
    private function createSkillsByNextLevelPropertyIncrease(ProfessionLevel $nextLevel, $skillClass): MockInterface
    {
        $kills = $this->mockery($this->determineSkillsClass($skillClass));
        $kills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator(
                [$firstSkill = $this->mockery(Skill::class)]
            ));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($skillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$nextLevelSkillRank = $this->mockery(SkillRank::class)]);
        $nextLevelSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($nextLevel);
        $nextLevelSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($skillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($skillClass));
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByOtherSkillPoints')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByNextLevelPropertyIncrease')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getRelatedProperties')
            ->andReturn($this->determineRelatedProperties($skillClass));

        return $kills;
    }

    private function determineRelatedProperties($skillClass): array
    {
        if (\is_a($skillClass, PhysicalSkill::class, true)) {
            return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
        }
        if (\is_a($skillClass, PsychicalSkill::class, true)) {
            return [PropertyCode::WILL, PropertyCode::INTELLIGENCE];
        }
        if (\is_a($skillClass, CombinedSkill::class, true)) {
            return [PropertyCode::KNACK, PropertyCode::CHARISMA];
        }
        throw new \LogicException;
    }

    /**
     * @param string $professionCode
     * @param int $nextLevelsStrengthModifier = 1
     * @param int $nextLevelsAgilityModifier = 0
     * @param int $nextLevelsKnackModifier = 1
     * @param int $nextLevelsWillModifier = 1
     * @param int $nextLevelsIntelligenceModifier = 0
     * @param int $nextLevelsCharismaModifier = 0
     * @return \Mockery\MockInterface|ProfessionLevels
     */
    private function createProfessionLevels(
        $professionCode = 'foo',
        $nextLevelsStrengthModifier = 1,
        $nextLevelsAgilityModifier = 0,
        $nextLevelsKnackModifier = 1,
        $nextLevelsWillModifier = 1,
        $nextLevelsIntelligenceModifier = 0,
        $nextLevelsCharismaModifier = 0
    )
    {
        $professionLevels = $this->mockery(ProfessionLevels::class);
        $professionLevels->shouldReceive('getFirstLevel')
            ->andReturn($firstLevel = $this->mockery(ProfessionFirstLevel::class));
        $firstLevel->shouldReceive('isFirstLevel')
            ->andReturn(true);
        $firstLevel->shouldReceive('isNextLevel')
            ->andReturn(false);
        $firstLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);
        $professionLevels->shouldReceive('getProfessionNextLevels')
            ->andReturn([$nextLevel = $this->mockery(ProfessionLevel::class)]);
        $nextLevel->shouldReceive('isFirstLevel')
            ->andReturn(false);
        $nextLevel->shouldReceive('isNextLevel')
            ->andReturn(true);
        $nextLevel->shouldReceive('getProfession')
            ->andReturn($profession = $this->mockery(Profession::class));
        $nextLevel->shouldReceive('getLevelRank')
            ->andReturn($nextLevelRank = $this->mockery(LevelRank::class));
        $nextLevelRank->shouldReceive('getValue')
            ->andReturn(2);
        $professionLevels->shouldReceive('getNextLevelsStrengthModifier')
            ->andReturn($nextLevelsStrengthModifier);
        $professionLevels->shouldReceive('getNextLevelsAgilityModifier')
            ->andReturn($nextLevelsAgilityModifier);
        $professionLevels->shouldReceive('getNextLevelsKnackModifier')
            ->andReturn($nextLevelsKnackModifier);
        $professionLevels->shouldReceive('getNextLevelsWillModifier')
            ->andReturn($nextLevelsWillModifier);
        $professionLevels->shouldReceive('getNextLevelsIntelligenceModifier')
            ->andReturn($nextLevelsIntelligenceModifier);
        $professionLevels->shouldReceive('getNextLevelsCharismaModifier')
            ->andReturn($nextLevelsCharismaModifier);

        return $professionLevels;
    }

    /**
     * @param Profession|null $profession
     * @param int $value
     * @param $physicalSkillPoints = 3
     * @param $psychicalSkillPoints = 3
     * @param $combinedSkillPoints = 3
     * @return \Mockery\MockInterface|SkillPointsFromBackground
     */
    private function createSkillPointsFromBackground(
        Profession $profession = null,
        int $value = 976431,
        $physicalSkillPoints = 3,
        $psychicalSkillPoints = 3,
        $combinedSkillPoints = 3
    )
    {
        $skillsFromBackground = $this->mockery(SkillPointsFromBackground::class);
        if ($profession) {
            $skillsFromBackground->shouldReceive('getPhysicalSkillPoints')
                ->zeroOrMoreTimes()
                ->with($profession, Tables::getIt())
                ->andReturn($physicalSkillPoints);
            $skillsFromBackground->shouldReceive('getPsychicalSkillPoints')
                ->zeroOrMoreTimes()
                ->with($profession, Tables::getIt())
                ->andReturn($psychicalSkillPoints);
            $skillsFromBackground->shouldReceive('getCombinedSkillPoints')
                ->zeroOrMoreTimes()
                ->with($profession, Tables::getIt())
                ->andReturn($combinedSkillPoints);
        }
        $skillsFromBackground->shouldReceive('getSpentBackgroundPoints')
            ->andReturn(new PositiveIntegerObject($value));

        return $skillsFromBackground;
    }

    /**
     * @param array $physical
     * @param array $psychical
     * @param array $combined
     * @return array|Skill[]
     */
    private function getSortedExpectedSkills(array $physical, array $psychical, array $combined): array
    {
        $expectedSkills = array_merge($physical, $psychical, $combined);
        usort($expectedSkills, function (Skill $firstSkill, Skill $secondSkill) {
            return strcmp($firstSkill->getName(), $secondSkill->getName());
        });

        return $expectedSkills;
    }

    private function getSortedGivenSkills(Skills $skills): array
    {
        $givenSkills = $skills->getSkills();
        usort($givenSkills, function (Skill $firstSkill, Skill $secondSkill) {
            return strcmp($firstSkill->getName(), $secondSkill->getName());
        });

        return $givenSkills;
    }

    // NEGATIVE TESTS

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\UnknownPaymentForSkillPoint
     */
    public function I_can_not_use_unknown_payment()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $physicalSkills = $this->createPhysicalSkillsWithUnknownPayment($professionLevels->getFirstLevel(), Swimming::class);
        $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $professionLevels->getFirstLevel());
        $combinedSkills = $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $professionLevels->getFirstLevel());

        Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );
    }

    /**
     * @param ProfessionLevel $firstLevel
     * @param $firstSkillClass
     * @return \Mockery\MockInterface|PhysicalSkills
     */
    private function createPhysicalSkillsWithUnknownPayment(ProfessionLevel $firstLevel, $firstSkillClass)
    {
        $skillsClass = $this->determineSkillsClass($firstSkillClass);
        $skills = $this->mockery($skillsClass);
        $skills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator(
                [$firstSkill = $this->mockery(Skill::class)]
            ));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($firstSkillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$firstSkillRank = $this->mockery(SkillRank::class)]);
        $firstSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $firstSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($firstSkillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByOtherSkillPoints')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByNextLevelPropertyIncrease')
            ->andReturn(false);

        return $skills;
    }

    /**
     * @test
     * @dataProvider provideDifferentSkillPointsFromBackground
     * @expectedException \DrdPlus\Skills\Exceptions\SkillPointsFromBackgroundAreNotSame
     * @param ProfessionLevels $professionLevels
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param SkillPointsFromBackground $fromFirstSkillsSkillPointsFromBackground
     * @param SkillPointsFromBackground $fromOtherSkillsSkillPointsFromBackground
     */
    public function I_can_not_use_different_background_skill_points(
        ProfessionLevels $professionLevels,
        SkillPointsFromBackground $skillsFromBackground,
        SkillPointsFromBackground $fromFirstSkillsSkillPointsFromBackground,
        SkillPointsFromBackground $fromOtherSkillsSkillPointsFromBackground
    )
    {
        $physicalSkills = $this->createPhysicalSkillsWithDifferentBackground($fromFirstSkillsSkillPointsFromBackground, $professionLevels->getFirstLevel(), Swimming::class);
        $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($fromOtherSkillsSkillPointsFromBackground, $professionLevels->getFirstLevel());
        $combinedSkills = $this->createCombinedSkillsPaidByFirstLevelBackground($fromOtherSkillsSkillPointsFromBackground, $professionLevels->getFirstLevel());

        Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @param $firstSkillClass
     * @return \Mockery\MockInterface|PhysicalSkills
     */
    private function createPhysicalSkillsWithDifferentBackground(SkillPointsFromBackground $skillsFromBackground, ProfessionLevel $firstLevel, $firstSkillClass)
    {
        $skillsClass = $this->determineSkillsClass($firstSkillClass);
        $skills = $this->mockery($skillsClass);
        $skills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator(
                [$firstSkill = $this->mockery(Skill::class)]
            ));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($firstSkillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$firstSkillRank = $this->mockery(SkillRank::class)]);
        $firstSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $firstSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($firstSkillClass)));
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground /*$this->createSkillPointsFromBackground(null, 'different points value')*/);

        return $skills;
    }

    public function provideDifferentSkillPointsFromBackground(): array
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $differentSkillPointsFromBackground = $this->createSkillPointsFromBackground(null, 741258);

        return [
            [$professionLevels, $skillsFromBackground, $differentSkillPointsFromBackground, $skillsFromBackground],
            [$professionLevels, $skillsFromBackground, $differentSkillPointsFromBackground, $differentSkillPointsFromBackground],
        ];
    }

    /**
     * @test
     * @dataProvider provideSkillsWithTooHighFirstLevelPayment
     * @expectedException \DrdPlus\Skills\Exceptions\HigherSkillRanksFromFirstLevelThanPossible
     * @param ProfessionLevels $professionLevels
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param PhysicalSkills $physicalSkills
     * @param PsychicalSkills $psychicalSkills
     * @param CombinedSkills $combinedSkills
     */
    public function I_can_not_spent_more_background_skill_points_than_available(
        ProfessionLevels $professionLevels,
        SkillPointsFromBackground $skillsFromBackground,
        PhysicalSkills $physicalSkills,
        PsychicalSkills $psychicalSkills,
        CombinedSkills $combinedSkills
    )
    {
        Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );
    }

    public function provideSkillsWithTooHighFirstLevelPayment(): array
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground(
            $professionLevels->getFirstLevel()->getProfession(), 71829, 1, 1, 1
        );
        $firstLevel = $professionLevels->getFirstLevel();
        $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel);
        $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel);
        $combinedSkills = $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel);

        return [
            [
                $professionLevels,
                $skillsFromBackground,
                $this->createSkillsWithTooHighFirstLevelPayment(
                    $skillsFromBackground,
                    $professionLevels->getFirstLevel(),
                    Swimming::class,
                    Athletics::class
                ),
                $psychicalSkills,
                $combinedSkills,
            ],
            [
                $professionLevels,
                $skillsFromBackground,
                $physicalSkills,
                $this->createSkillsWithTooHighFirstLevelPayment(
                    $skillsFromBackground,
                    $professionLevels->getFirstLevel(),
                    ReadingAndWriting::class,
                    Astronomy::class
                ),
                $combinedSkills,
            ],
            [
                $professionLevels,
                $skillsFromBackground,
                $physicalSkills,
                $psychicalSkills,
                $this->createSkillsWithTooHighFirstLevelPayment(
                    $skillsFromBackground,
                    $professionLevels->getFirstLevel(),
                    Cooking::class,
                    BigHandwork::class
                ),
            ],
        ];
    }

    /**
     * @param SkillPointsFromBackground $skillsFromBackground
     * @param ProfessionLevel $firstLevel
     * @param string $firstSkillClass
     * @param string $secondSkillClass
     * @return \Mockery\MockInterface|PhysicalSkills
     */
    private function createSkillsWithTooHighFirstLevelPayment(
        SkillPointsFromBackground $skillsFromBackground,
        ProfessionLevel $firstLevel,
        string $firstSkillClass,
        string $secondSkillClass
    )
    {
        $skillsClass = $this->determineSkillsClass($firstSkillClass);
        $skills = $this->mockery($skillsClass);
        $skills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator([
                $firstSkill = $this->mockery(Skill::class),
                $secondSkill = $this->mockery(Skill::class),
            ]));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($firstSkillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([$firstSkillRank = $this->mockery(SkillRank::class)]);
        $firstSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $firstSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($firstSkillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground);
        $secondSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($secondSkillClass));
        $secondSkill->shouldReceive('getSkillRanks')
            ->andReturn([$secondSkillRank = $this->mockery(SkillRank::class)]);
        $secondSkillRank->shouldReceive('getProfessionLevel')
            ->andReturn($firstLevel);
        $secondSkillRank->shouldReceive('getSkillPoint')
            ->andReturn($secondSkillPoint = $this->mockery($this->determineSkillPointClass($secondSkillClass)));
        $secondSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $secondSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($firstSkillClass));
        $secondSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(true);
        $secondSkillPoint->shouldReceive('getSkillPointsFromBackground')
            ->andReturn($skillsFromBackground);

        return $skills;
    }

    /**
     * @test
     * @dataProvider provideProfessionLevelsWithTooLowPropertyIncrease
     * @expectedException \DrdPlus\Skills\Exceptions\HigherSkillRanksFromNextLevelsThanPossible
     * @param ProfessionLevels $professionLevels
     */
    public function I_can_not_increase_skills_by_next_levels_more_than_provides_property_increments(
        ProfessionLevels $professionLevels
    )
    {
        $nextLevels = $professionLevels->getProfessionNextLevels();
        $nextLevel = \end($nextLevels);
        $physicalSkills = $this->createPhysicalSkillsByNextLevelPropertyIncrease($nextLevel);
        $psychicalSkills = $this->createPsychicalSkillsByNextLevelPropertyIncrease($nextLevel);
        $combinedSkills = $this->createCombinedSkillsByNextLevelPropertyIncrease($nextLevel);
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());

        Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );
    }

    public function provideProfessionLevelsWithTooLowPropertyIncrease(): array
    {
        return [
            [$this->createProfessionLevels('foo', 0)], // physical properties
            [$this->createProfessionLevels('foo', 1, 1, 1, 0)], // psychical properties
            [$this->createProfessionLevels('foo', 1, 1, 0, 1, 1)], // combined properties
        ];
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\TooHighSingleSkillIncrementPerNextLevel
     */
    public function I_can_not_increase_same_skill_more_than_once_per_next_level()
    {
        $professionLevels = $this->createProfessionLevels('foo', 2);
        $nextLevels = $professionLevels->getProfessionNextLevels();
        $physicalSkills = $this->createPhysicalSkillsWithTooHighSkillIncrementPerNextLevel(\end($nextLevels), Swimming::class);
        $psychicalSkills = $this->createPsychicalSkillsByNextLevelPropertyIncrease($professionLevels->getFirstLevel());
        $combinedSkills = $this->createCombinedSkillsByNextLevelPropertyIncrease($professionLevels->getFirstLevel());
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());

        Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills,
            $psychicalSkills,
            $combinedSkills,
            Tables::getIt()
        );
    }

    /**
     * @param ProfessionLevel $nextLevel
     * @param string $skillClass
     * @return PhysicalSkills|\Mockery\MockInterface
     */
    private function createPhysicalSkillsWithTooHighSkillIncrementPerNextLevel(ProfessionLevel $nextLevel, string $skillClass)
    {
        $kills = $this->mockery($this->determineSkillsClass($skillClass));
        $kills->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator([
                $firstSkill = $this->mockery(Skill::class),
            ]));
        $firstSkill->shouldReceive('getName')
            ->andReturn($this->parseSkillName($skillClass));
        $firstSkill->shouldReceive('getSkillRanks')
            ->andReturn([
                $skillFirstRank = $this->mockery(SkillRank::class),
                $skillSecondRank = $this->mockery(SkillRank::class),
            ]);
        $skillFirstRank->shouldReceive('getProfessionLevel')
            ->andReturn($nextLevel);
        $skillFirstRank->shouldReceive('getValue')
            ->andReturn(1);
        $skillFirstRank->shouldReceive('getSkillPoint')
            ->andReturn($firstSkillPoint = $this->mockery($this->determineSkillPointClass($skillClass)));
        $firstSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $firstSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($skillClass));
        $firstSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByOtherSkillPoints')
            ->andReturn(false);
        $firstSkillPoint->shouldReceive('isPaidByNextLevelPropertyIncrease')
            ->andReturn(true);
        $firstSkillPoint->shouldReceive('getRelatedProperties')
            ->andReturn($this->determineRelatedProperties($skillClass));
        $skillSecondRank->shouldReceive('getProfessionLevel')
            ->andReturn($nextLevel);
        $skillSecondRank->shouldReceive('getValue')
            ->andReturn(2);
        $skillSecondRank->shouldReceive('getSkillPoint')
            ->andReturn($secondSkillPoint = $this->mockery($this->determineSkillPointClass($skillClass)));
        $secondSkillPoint->shouldReceive('getValue')
            ->andReturn(1);
        $secondSkillPoint->shouldReceive('getTypeName')
            ->andReturn($this->determineSkillTypeName($skillClass));
        $secondSkillPoint->shouldReceive('isPaidByFirstLevelSkillPointsFromBackground')
            ->andReturn(false);
        $secondSkillPoint->shouldReceive('isPaidByOtherSkillPoints')
            ->andReturn(false);
        $secondSkillPoint->shouldReceive('isPaidByNextLevelPropertyIncrease')
            ->andReturn(true);
        $secondSkillPoint->shouldReceive('getRelatedProperties')
            ->andReturn($this->determineRelatedProperties($skillClass));

        return $kills;
    }

    /**
     * @test
     * @dataProvider provideBattleParameterNames
     * @param string $malusTo
     */
    public function I_can_get_melee_weapon_malus($malusTo)
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );

        if ($malusTo === 'cover') {
            $meleeWeaponCode = $this->createWeaponCode(true /* is melee */);
        } else {
            $meleeWeaponCode = $this->createWeaponlikeCode(true /* is melee */);
        }
        $tables = $this->createTablesWithMissingWeaponSkillTable();
        /**
         * @see \DrdPlus\Skills\Skills::getMalusToFightNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToAttackNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToBaseOfWoundsWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToCoverWithWeapon
         */
        $getMalusToParameter = 'getMalusTo' . \ucfirst($malusTo) . 'WithWeapon' . ($malusTo === 'cover' ? '' : 'like');

        $physicalSkills->shouldReceive($getMalusToParameter)
            ->zeroOrMoreTimes()
            ->with($meleeWeaponCode, $tables, false)
            ->andReturn($singleMeleeWeaponMalus = 123456);
        self::assertSame(
            $singleMeleeWeaponMalus,
            $skills->$getMalusToParameter(
                $meleeWeaponCode,
                $tables,
                false // single weapon used
            )
        );

        $physicalSkills->shouldReceive($getMalusToParameter)
            ->zeroOrMoreTimes()
            ->with($meleeWeaponCode, $tables, true)
            ->andReturn($twoMeleeWeaponsMalus = 798123);
        self::assertSame(
            $twoMeleeWeaponsMalus,
            $skills->$getMalusToParameter(
                $meleeWeaponCode,
                $tables,
                true // two weapons used
            )
        );
    }

    /**
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isShooting
     * @param bool $isProjectile
     * @return \Mockery\MockInterface|WeaponlikeCode
     */
    private function createWeaponlikeCode($isMelee = false, $isThrowing = false, $isShooting = false, $isProjectile = false)
    {
        return $this->createCode(false /* not weapon only (wants weaponlike) */, $isMelee, $isThrowing, $isShooting, $isProjectile);
    }

    /**
     * @param $weaponOnly
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isShooting
     * @param bool $isProjectile
     * @return \Mockery\MockInterface|WeaponCode|WeaponlikeCode
     */
    private function createCode(
        $weaponOnly,
        $isMelee = false,
        $isThrowing = false,
        $isShooting = false,
        $isProjectile = false
    )
    {
        $weaponlikeCode = $this->mockery($weaponOnly ? WeaponCode::class : WeaponlikeCode::class);
        $weaponlikeCode->shouldReceive('isMelee')
            ->andReturn($isMelee);
        $weaponlikeCode->shouldReceive('isThrowingWeapon')
            ->andReturn($isThrowing);
        $weaponlikeCode->shouldReceive('isShootingWeapon')
            ->andReturn($isShooting);
        $weaponlikeCode->shouldReceive('isProjectile')
            ->andReturn($isProjectile);

        return $weaponlikeCode;
    }

    /**
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithMissingWeaponSkillTable()
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMissingWeaponSkillTable')
            ->andReturn($this->mockery(MissingWeaponSkillTable::class));

        return $tables;
    }

    /**
     * @test
     * @dataProvider provideBattleParameterNames
     * @param string $malusTo
     */
    public function I_can_get_malus_for_throwing_weapon($malusTo)
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );

        if ($malusTo === 'cover') {
            $throwingWeaponCode = $this->createWeaponCode(
                false /* not melee */,
                true /* is throwing */
            );
        } else {
            $throwingWeaponCode = $this->createWeaponlikeCode(
                false /* not melee */,
                true /* is throwing */
            );
        }
        $missingWeaponSkillsTable = $this->createTablesWithMissingWeaponSkillTable();
        /**
         * @see \DrdPlus\Skills\Skills::getMalusToFightNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToAttackNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToBaseOfWoundsWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToCoverWithWeapon
         */
        $getMalusToParameter = 'getMalusTo' . \ucfirst($malusTo) . 'WithWeapon' . ($malusTo === 'cover' ? '' : 'like');

        $physicalSkills->shouldReceive($getMalusToParameter)
            ->zeroOrMoreTimes()
            ->with($throwingWeaponCode, $missingWeaponSkillsTable, false)
            ->andReturn($singleThrowingWeaponMalus = 123456);
        self::assertSame(
            $singleThrowingWeaponMalus,
            $skills->$getMalusToParameter(
                $throwingWeaponCode,
                $missingWeaponSkillsTable,
                false // single weapon used
            )
        );

        $physicalSkills->shouldReceive($getMalusToParameter)
            ->zeroOrMoreTimes()
            ->with($throwingWeaponCode, $missingWeaponSkillsTable, true)
            ->andReturn($twoThrowingWeaponsMalus = 789123);
        self::assertSame(
            $twoThrowingWeaponsMalus,
            $skills->$getMalusToParameter(
                $throwingWeaponCode,
                $missingWeaponSkillsTable,
                true // two weapons used
            )
        );
    }

    /**
     * @test
     * @dataProvider provideBattleParameterNames
     * @param string $malusTo
     */
    public function I_can_get_malus_for_shooting_weapon($malusTo)
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $combinedSkills = $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );

        if ($malusTo === 'cover') {
            $shootingWeaponCode = $this->createWeaponCode(
                false /* not melee */,
                false /* not throwing */,
                true /* is shooting */
            );
        } else {
            $shootingWeaponCode = $this->createWeaponlikeCode(
                false /* not melee */,
                false /* not throwing */,
                true /* is shooting */
            );
        }
        $shootingWeaponCode->shouldReceive('convertToRangedWeaponCodeEquivalent')
            ->andReturn($rangeWeaponCode = $this->createRangeWeaponCode());
        $missingWeaponSkillsTable = $this->createTablesWithMissingWeaponSkillTable();
        $combinedSkills->shouldReceive('getMalusTo' . \ucfirst($malusTo) . 'WithShootingWeapon')
            ->zeroOrMoreTimes()
            ->with($rangeWeaponCode, $missingWeaponSkillsTable)
            ->andReturn($shootingWeaponMalus = 987654);
        /**
         * @see \DrdPlus\Skills\Skills::getMalusToFightNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToAttackNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToBaseOfWoundsWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToCoverWithWeapon
         */
        $malusToParameter = 'getMalusTo' . \ucfirst($malusTo) . 'WithWeapon' . ($malusTo === 'cover' ? '' : 'like');
        self::assertSame(
            $shootingWeaponMalus,
            $skills->$malusToParameter(
                $shootingWeaponCode,
                $missingWeaponSkillsTable,
                false /* fight with two weapons should be irrelevant for shooting weapons */
            )
        );
        self::assertSame(
            $shootingWeaponMalus,
            $skills->$malusToParameter(
                $shootingWeaponCode,
                $missingWeaponSkillsTable,
                true /* fight with two weapons should be irrelevant for shooting weapons */
            )
        );
    }

    /**
     * @return \Mockery\MockInterface|RangedWeaponCode
     */
    private function createRangeWeaponCode()
    {
        return $this->mockery(RangedWeaponCode::class);
    }

    /**
     * @test
     * @dataProvider provideBattleParameterNames
     * @param string $malusTo
     */
    public function I_get_zero_malus_for_projectiles($malusTo)
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $combinedSkills = $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );

        if ($malusTo === 'cover') {
            $projectile = $this->createWeaponCode(
                false /* not melee */,
                false /* not throwing */,
                false /* not shooting */,
                true /* projectile */
            );
        } else {
            $projectile = $this->createWeaponlikeCode(
                false /* not melee */,
                false /* not throwing */,
                false /* not shooting */,
                true /* projectile */
            );
        }
        $projectile->shouldReceive('convertToRangedWeaponCodeEquivalent')
            ->andReturn($rangeWeaponCode = $this->createRangeWeaponCode());
        $missingWeaponSkillsTable = $this->createTablesWithMissingWeaponSkillTable();
        /**
         * @see \DrdPlus\Skills\Skills::getMalusToFightNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToAttackNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToBaseOfWoundsWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToCoverWithWeapon
         */
        $malusToParameter = 'getMalusTo' . \ucfirst($malusTo) . 'WithWeapon' . ($malusTo === 'cover' ? '' : 'like');
        self::assertSame(
            0,
            $skills->$malusToParameter(
                $projectile,
                $missingWeaponSkillsTable,
                false /* fight with two weapons should be irrelevant for projectile */
            )
        );
        self::assertSame(
            0,
            $skills->$malusToParameter(
                $projectile,
                $missingWeaponSkillsTable,
                true /* fight with two weapons should be irrelevant for projectile */
            )
        );
    }

    public function provideBattleParameterNames(): array
    {
        return [
            ['fightNumber'],
            ['attackNumber'],
            ['cover'],
            ['baseOfWounds'],
        ];
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_fight_number_with_protective()
    {
        $professionLevels = $this->createProfessionLevels();
        $firstLevel = $professionLevels->getFirstLevel();
        $skillsFromBackground = $this->createSkillPointsFromBackground($firstLevel->getProfession());
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        /** @var Armourer $armourer */
        $armourer = $this->mockery(Armourer::class);
        /** @var ShieldCode $shield */
        $shield = $this->mockery(ShieldCode::class);
        $physicalSkills->shouldReceive('getMalusToFightNumberWithProtective')
            ->zeroOrMoreTimes()
            ->with($shield, $armourer)
            ->andReturn(465321);
        self::assertSame(
            465321,
            $skills->getMalusToFightNumberWithProtective($shield, $armourer)
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Skills\Exceptions\UnknownTypeOfWeapon
     * @dataProvider provideBattleParameterNames
     * @param string $malusTo
     */
    public function I_can_not_get_battle_number_malus_for_unknown_weapon($malusTo)
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        /**
         * @see \DrdPlus\Skills\Skills::getMalusToFightNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToAttackNumberWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToBaseOfWoundsWithWeaponlike
         * @see \DrdPlus\Skills\Skills::getMalusToCoverWithWeapon
         */
        $malusToParameter = 'getMalusTo' . \ucfirst($malusTo) . 'WithWeapon' . ($malusTo === 'cover' ? '' : 'like');
        $skills->$malusToParameter(
            ($malusTo === 'cover' ? $this->createWeaponCode() : $this->createWeaponlikeCode()),
            $this->createTablesWithMissingWeaponSkillTable(),
            false
        );
    }

    /**
     * @param bool $isMelee
     * @param bool $isThrowing
     * @param bool $isShooting
     * @param bool $isProjectile
     * @return \Mockery\MockInterface|WeaponCode
     */
    private function createWeaponCode(
        $isMelee = false,
        $isThrowing = false,
        $isShooting = false,
        $isProjectile = false
    )
    {
        return $this->createCode(true /* weapon only */, $isMelee, $isThrowing, $isShooting, $isProjectile);
    }

    /**
     * @test
     */
    public function I_can_get_malus_to_cover_with_shield()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        $tables = $this->createTablesWithShieldUsageSkillTable();
        $physicalSkills->shouldReceive('getMalusToCoverWithShield')
            ->zeroOrMoreTimes()
            ->with($tables)
            ->andReturn(123654);
        self::assertSame(123654, $skills->getMalusToCoverWithShield($tables));
    }

    /**
     * @return \Mockery\MockInterface|Tables
     */
    private function createTablesWithShieldUsageSkillTable()
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getShieldUsageSkillTable')
            ->andReturn($this->mockery(ShieldUsageSkillTable::class));

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_create_it_with_zero_profession_levels()
    {
        $skills = Skills::createSkills(
            ProfessionLevels::createIt(
                $professionZeroLevel = ProfessionZeroLevel::createZeroLevel(Commoner::getIt()),
                ProfessionFirstLevel::createFirstLevel($profession = Fighter::getIt()),
                [ProfessionNextLevel::createNextLevel(
                    $profession,
                    LevelRank::getIt(2),
                    Strength::getIt(0),
                    Agility::getIt(1),
                    Knack::getIt(0),
                    Will::getIt(0),
                    Intelligence::getIt(1),
                    Charisma::getIt(0)
                )]
            ),
            SkillPointsFromBackground::getIt(
                new PositiveIntegerObject(2),
                Ancestry::getIt(new PositiveIntegerObject(7), Tables::getIt()),
                Tables::getIt()
            ),
            new PhysicalSkills($professionZeroLevel),
            new PsychicalSkills($professionZeroLevel),
            new CombinedSkills($professionZeroLevel),
            Tables::getIt()
        );
        self::assertSame(
            $professionZeroLevel,
            $skills->getCombinedSkills()->getBigHandwork()->getCurrentSkillRank()->getProfessionLevel()
        );
    }

    /**
     * @test
     */
    public function I_can_get_maluses_from_ride()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $physicalSkills = $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        $physicalSkills->shouldReceive('getMalusToFightNumberWhenRiding')
            ->andReturn(-5);
        self::assertSame(-5, $skills->getMalusToFightNumberWhenRiding());
        $physicalSkills->shouldReceive('getMalusToAttackNumberWhenRiding')
            ->andReturn(-3);
        self::assertSame(-3, $skills->getMalusToAttackNumberWhenRiding());
        $physicalSkills->shouldReceive('getMalusToDefenseNumberWhenRiding')
            ->andReturn(-99);
        self::assertSame(-99, $skills->getMalusToDefenseNumberWhenRiding());
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_attack_number_against_free_will_animal()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        $psychicalSkills->shouldReceive('getBonusToAttackNumberAgainstFreeWillAnimal')
            ->andReturn(123456);
        self::assertSame(123456, $skills->getBonusToAttackNumberAgainstFreeWillAnimal());
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_cover_against_free_will_animal()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        $psychicalSkills->shouldReceive('getBonusToCoverAgainstFreeWillAnimal')
            ->andReturn(2345);
        self::assertSame(2345, $skills->getBonusToCoverAgainstFreeWillAnimal());
    }

    /**
     * @test
     */
    public function I_can_get_bonus_to_base_of_wounds_against_free_will_animal()
    {
        $professionLevels = $this->createProfessionLevels();
        $skillsFromBackground = $this->createSkillPointsFromBackground($professionLevels->getFirstLevel()->getProfession());
        $firstLevel = $professionLevels->getFirstLevel();
        $skills = Skills::createSkills(
            $professionLevels,
            $skillsFromBackground,
            $this->createPhysicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $psychicalSkills = $this->createPsychicalSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            $this->createCombinedSkillsPaidByFirstLevelBackground($skillsFromBackground, $firstLevel),
            Tables::getIt()
        );
        $psychicalSkills->shouldReceive('getBonusToBaseOfWoundsAgainstFreeWillAnimal')
            ->andReturn(4569);
        self::assertSame(4569, $skills->getBonusToBaseOfWoundsAgainstFreeWillAnimal());
    }

}