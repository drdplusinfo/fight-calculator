<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Codes\Skills\SkillCode;
use DrdPlus\Person\ProfessionLevels\ProfessionFirstLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionLevel;
use DrdPlus\Person\ProfessionLevels\ProfessionZeroLevel;
use DrdPlus\Professions\Commoner;
use DrdPlus\Skills\Combined\CombinedSkillPoint;
use DrdPlus\Skills\Combined\CombinedSkill;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Skills\Psychical\PsychicalSkill;
use DrdPlus\Skills\SameTypeSkills;
use DrdPlus\Skills\Skill;
use DrdPlus\Skills\SkillPoint;
use DrdPlus\Skills\Physical\PhysicalSkillPoint;
use DrdPlus\Skills\Psychical\PsychicalSkillPoint;
use Granam\Tests\Tools\TestWithMockery;

abstract class SameTypeSkillsTest extends TestWithMockery
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_use_it()
    {
        $sutClass = self::getSutClass();
        /** @var SameTypeSkills $sut */
        $sut = new $sutClass(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertCount(\count($this->getSameTypeSkillCodes()), $sut);
        self::assertSame(0, $sut->getFirstLevelSkillRankSummary());
        self::assertSame(0, $sut->getNextLevelsSkillRankSummary());
    }

    protected function getExpectedSkillsTypeName(): string
    {
        $sutClass = self::getSutClass();
        self::assertSame(1, preg_match('~[\\\]?(?<groupName>\w+)Skills$~', $sutClass, $matches));

        return strtolower($matches['groupName']);
    }

    /**
     * @test
     * @dataProvider provideSkillClasses
     * @param string $skillClass
     */
    public function I_can_increase_skill($skillClass)
    {
        $sutClass = self::getSutClass();
        /** @var SameTypeSkills $sut */
        $sut = new $sutClass(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        self::assertSame(0, $sut->getFirstLevelSkillRankSummary());
        self::assertSame(0, $sut->getNextLevelsSkillRankSummary());

        $getSkill = $this->getSkillGetterFromClassName($skillClass);
        /** @var Skill|CombinedSkill|PhysicalSkill|PsychicalSkill $skill */
        $skill = $sut->$getSkill();
        self::assertSame(0, $skill->getCurrentSkillRank()->getValue());

        $skill->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(1, $skill->getCurrentSkillRank()->getValue());
        self::assertSame(1, $sut->getFirstLevelSkillRankSummary());
        self::assertSame(0, $sut->getNextLevelsSkillRankSummary());

        $skill->increaseSkillRank($this->createSkillPoint($this->createProfessionNextLevel()));
        self::assertSame(2, $skill->getCurrentSkillRank()->getValue());
        self::assertSame(1, $sut->getFirstLevelSkillRankSummary());
        self::assertSame(2, $sut->getNextLevelsSkillRankSummary());

        $skill->increaseSkillRank($this->createSkillPoint($this->createProfessionFirstLevel()));
        self::assertSame(3, $skill->getCurrentSkillRank()->getValue());
        self::assertSame(1 + 3, $sut->getFirstLevelSkillRankSummary());
        self::assertSame(2, $sut->getNextLevelsSkillRankSummary());
    }

    public function provideSkillClasses(): array
    {
        $skillClasses = $this->getExpectedSkillClasses();
        foreach ($skillClasses as &$skillClass) {
            $skillClass = [$skillClass];
        }

        return $skillClasses;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getSkillGetterFromClassName($className): string
    {
        $baseName = preg_replace('~.*[\\\]([^\\\]+)$~', '$1', $className);

        return 'get' . $baseName;
    }

    /**
     * @param string $except
     * @return array|string[]
     * @throws \ReflectionException
     */
    protected function getSameTypeSkillCodesExcept($except): array
    {
        return array_diff($this->getSameTypeSkillCodes(), [$except]);
    }

    /**
     * @return array|\string[]
     * @throws \ReflectionException
     */
    protected function getSameTypeSkillCodes(): array
    {
        $type = \preg_replace('~.*[\\\](\w+)Skills$~', '$1', self::getSutClass());
        $skillCodeNamespace = (new \ReflectionClass(SkillCode::class))->getNamespaceName();
        /** @var SkillCode $skillTypeCodeClass */
        $skillTypeCodeClass = "{$skillCodeNamespace}\\{$type}SkillCode";

        return $skillTypeCodeClass::getPossibleValues();
    }

    /**
     * @return array|Skill[]|string[]
     */
    protected function getExpectedSkillClasses(): array
    {
        $namespace = $this->getNamespace();
        $fileBaseNames = $this->getFileBaseNames($namespace);
        $sutClassNames = array_map(
            function ($fileBasename) use ($namespace) {
                $classBasename = preg_replace('~(\w+)\.\w+~', '$1', $fileBasename);
                $className = $namespace . '\\' . $classBasename;
                if (!is_a($className, Skill::class, true)
                    || (new \ReflectionClass($className))->isAbstract()
                ) {
                    return false;
                }

                return $className;
            },
            $fileBaseNames
        );

        return array_filter(
            $sutClassNames,
            function ($sutClassName) {
                return $sutClassName !== false;
            }
        );
    }

    /**
     * @return string
     */
    protected function getNamespace(): string
    {
        return preg_replace('~[\\\]Tests([\\\].+)[\\\]\w+$~', '$1', static::class);
    }

    protected function getFileBaseNames($namespace): array
    {
        $sutNamespaceToDirRelativePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $sutDir = \rtrim($this->getProjectRootDir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . $sutNamespaceToDirRelativePath;
        $files = scandir($sutDir, SCANDIR_SORT_NONE);
        $sutFiles = array_filter($files, function ($filename) {
            return $filename !== '.' && $filename !== '..';
        });

        return $sutFiles;
    }

    private function getProjectRootDir()
    {
        $namespaceAsRelativePath = str_replace('\\', DIRECTORY_SEPARATOR, __NAMESPACE__);
        $projectRootDir = preg_replace('~' . preg_quote($namespaceAsRelativePath, '~') . '.*~', '', __DIR__);

        return $projectRootDir;
    }

    /**
     * @param ProfessionLevel $professionLevel
     * @return \Mockery\MockInterface|SkillPoint|CombinedSkillPoint|PhysicalSkillPoint|PsychicalSkillPoint
     */
    protected function createSkillPoint(ProfessionLevel $professionLevel)
    {
        $skillPointClass = $this->mockery($this->getSkillPointClass());
        $skillPointClass->shouldReceive('getProfessionLevel')
            ->andReturn($professionLevel);
        $skillPointClass->shouldReceive('getValue')
            ->andReturn(1);

        return $skillPointClass;
    }

    /**
     * @return string
     */
    private function getSkillPointClass(): string
    {
        $baseClass = SkillPoint::class;
        $typeName = preg_quote(ucfirst($this->getExpectedSkillsTypeName()), '~');
        $class = preg_replace(
            '~[\\\]SkillPoint$~',
            '\\' . $typeName . '\\' . $typeName . 'SkillPoint',
            $baseClass
        );

        return $class;
    }

    /**
     * @return string
     */
    protected function getSkillAdderName(): string
    {
        $groupName = $this->getExpectedSkillsTypeName();

        /**
         * @see \DrdPlus\Skills\Combined\CombinedSkills::addCombinedSkill
         * @see \DrdPlus\Skills\Physical\PhysicalSkills::addPhysicalSkill
         * @see \DrdPlus\Skills\Psychical\PsychicalSkills::addPsychicalSkill
         */
        return 'add' . \ucfirst($groupName) . 'Skill';
    }

    protected function getSkillGetter(Skill $skill): string
    {
        $class = \get_class($skill);
        self::assertSame(1, preg_match('~[\\\](?<basename>\w+)$~', $class, $matches));

        return 'get' . $matches['basename'];
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    protected function createProfessionFirstLevel()
    {
        $professionFirstLevel = $this->mockery(ProfessionFirstLevel::class);
        $professionFirstLevel->shouldReceive('isFirstLevel')
            ->andReturn(true);
        $professionFirstLevel->shouldReceive('isNextLevel')
            ->andReturn(false);

        return $professionFirstLevel;
    }

    /**
     * @return \Mockery\MockInterface|ProfessionFirstLevel
     */
    protected function createProfessionNextLevel()
    {
        $professionFirstLevel = $this->mockery(ProfessionFirstLevel::class);
        $professionFirstLevel->shouldReceive('isFirstLevel')
            ->andReturn(false);
        $professionFirstLevel->shouldReceive('isNextLevel')
            ->andReturn(true);

        return $professionFirstLevel;
    }

    /**
     * @test
     */
    abstract public function I_can_not_increase_rank_by_zero_skill_point();

    /**
     * @test
     */
    abstract public function I_can_get_unused_skill_points_from_first_level();

    /**
     * @test
     */
    abstract public function I_can_get_unused_skill_points_from_next_levels();

    /**
     * @test
     */
    public function I_can_iterate_through_all_skills()
    {
        $sutClass = self::getSutClass();
        /** @var SameTypeSkills $sut */
        $sut = new $sutClass(ProfessionZeroLevel::createZeroLevel(Commoner::getIt()));
        $skills = [];
        foreach ($sut as $skill) {
            self::assertSame(0, $skill->getCurrentSkillRank()->getValue());
            $skills[] = $skill;
        }
        self::assertSame($skills, $sut->getIterator()->getArrayCopy());
        $skillClasses = [];
        foreach ($skills as $skill) {
            $skillClasses[] = \get_class($skill);
        }
        sort($skillClasses);
        $expectedSkillClasses = $this->getExpectedSkillClasses();
        sort($expectedSkillClasses);
        self::assertSame($skillClasses, $expectedSkillClasses);
    }
}