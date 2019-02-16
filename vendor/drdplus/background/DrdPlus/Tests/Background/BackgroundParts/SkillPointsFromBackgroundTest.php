<?php
declare(strict_types = 1);

namespace DrdPlus\Tests\Background\BackgroundParts;

use DrdPlus\Codes\History\AncestryCode;
use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Skills\SkillTypeCode;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Professions\Profession;
use DrdPlus\Tables\History\SkillsByBackgroundPointsTable;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Background\BackgroundParts\Partials\AbstractAncestryDependentTest;
use Granam\Integer\PositiveInteger;
use Granam\Integer\PositiveIntegerObject;

class SkillPointsFromBackgroundTest extends AbstractAncestryDependentTest
{

    /**
     * @test
     * @dataProvider provideSkillType
     * @param string $skillTypeName
     */
    public function I_can_get_skill_points(string $skillTypeName): void
    {
        $tables = $this->createTables();
        $skillsByBackgroundPointsTable = $this->createSkillsByBackgroundPointsTable();
        $tables->shouldReceive('getSkillsByBackgroundPointsTable')
            ->andReturn($skillsByBackgroundPointsTable);
        $professionCode = ProfessionCode::getIt(ProfessionCode::FIGHTER);
        $result = 123;
        $spentBackgroundPoints = new PositiveIntegerObject(7);
        /** @noinspection PhpUnusedParameterInspection */
        $skillsByBackgroundPointsTable->shouldReceive('getSkillPoints')
            ->atLeast()->once()
            ->with($this->type(PositiveInteger::class), $professionCode, $skillType = SkillTypeCode::getIt($skillTypeName))
            ->andReturnUsing(
                function (PositiveInteger $givenSpentBackgroundPoints)
                use ($spentBackgroundPoints, $result) {
                    self::assertEquals($spentBackgroundPoints, $givenSpentBackgroundPoints);

                    return $result;
                });
        $tables->makePartial();
        $backgroundSkillPoints = SkillPointsFromBackground::getIt(
            $spentBackgroundPoints,
            $ancestry = $this->createAncestry($ancestryValue = 456, AncestryCode::getIt(AncestryCode::NOBLE)),
            $tables
        );
        self::assertSame(
            $result,
            $backgroundSkillPoints->getSkillPoints(
                $this->createProfession(ProfessionCode::FIGHTER, $professionCode),
                $skillType,
                $tables
            )
        );
        switch ($skillTypeName) {
            case SkillTypeCode::PHYSICAL :
                self::assertSame(
                    $result,
                    $backgroundSkillPoints->getPhysicalSkillPoints(
                        $this->createProfession(ProfessionCode::FIGHTER, $professionCode),
                        $tables
                    )
                );
                break;
            case SkillTypeCode::PSYCHICAL :
                self::assertSame(
                    $result,
                    $backgroundSkillPoints->getPsychicalSkillPoints(
                        $this->createProfession(ProfessionCode::FIGHTER, $professionCode),
                        $tables
                    )
                );
                break;
            case SkillTypeCode::COMBINED :
                self::assertSame(
                    $result,
                    $backgroundSkillPoints->getCombinedSkillPoints(
                        $this->createProfession(ProfessionCode::FIGHTER, $professionCode),
                        $tables
                    )
                );
                break;
        }
    }

    /**
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(): Tables
    {
        return $this->mockery(Tables::class);
    }

    /**
     * @return \Mockery\MockInterface|SkillsByBackgroundPointsTable
     */
    private function createSkillsByBackgroundPointsTable(): SkillsByBackgroundPointsTable
    {
        return $this->mockery(SkillsByBackgroundPointsTable::class);
    }

    /**
     * @param string $professionName
     * @param ProfessionCode $professionCode
     * @return \Mockery\MockInterface|Profession
     */
    private function createProfession(string $professionName, ProfessionCode $professionCode = null): Profession
    {
        $profession = $this->mockery(Profession::class);
        $profession->shouldReceive('getValue')
            ->andReturn($professionName);
        $profession->shouldReceive('getCode')
            ->andReturn($professionCode ?? ProfessionCode::getIt($professionName));

        return $profession;
    }

    public function provideSkillType(): array
    {
        return \array_map(
            function ($value) {
                return [$value];
            },
            SkillTypeCode::getPossibleValues()
        );
    }
}