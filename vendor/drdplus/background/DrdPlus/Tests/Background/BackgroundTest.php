<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Background;

use DrdPlus\Codes\History\FateCode;
use DrdPlus\Background\Background;
use DrdPlus\Background\BackgroundParts\SkillPointsFromBackground;
use DrdPlus\Background\BackgroundParts\Possession;
use DrdPlus\Background\BackgroundParts\Ancestry;
use DrdPlus\Background\BackgroundPoints;
use DrdPlus\Tables\History\BackgroundPointsTable;
use DrdPlus\Tables\Tables;
use Granam\Integer\PositiveIntegerObject;
use Granam\Tests\Tools\TestWithMockery;

class BackgroundTest extends TestWithMockery
{
    /**
     * @test
     * @dataProvider provideBackgroundPoints
     * @param FateCode $fateCode
     * @param int $forAncestrySpentBackgroundPoints
     * @param int $forBackgroundSkillPointsSpentBackgroundPoints
     * @param int $forBelongingsSpentBackgroundPoints
     */
    public function I_can_create_background(
        FateCode $fateCode,
        int $forAncestrySpentBackgroundPoints,
        int $forBackgroundSkillPointsSpentBackgroundPoints,
        int $forBelongingsSpentBackgroundPoints
    ): void
    {
        $background = Background::createIt(
            $fateCode,
            new PositiveIntegerObject($forAncestrySpentBackgroundPoints),
            new PositiveIntegerObject($forBelongingsSpentBackgroundPoints),
            new PositiveIntegerObject($forBackgroundSkillPointsSpentBackgroundPoints),
            Tables::getIt()
        );

        $backgroundPoints = $background->getBackgroundPoints();
        self::assertInstanceOf(BackgroundPoints::class, $backgroundPoints);
        self::assertSame((new BackgroundPointsTable())->getBackgroundPointsByPlayerDecision($fateCode), $backgroundPoints->getValue());

        $ancestry = $background->getAncestry();
        self::assertInstanceOf(Ancestry::class, $ancestry);
        $ancestrySpentBackgroundPoints = $ancestry->getSpentBackgroundPoints();
        self::assertSame($forAncestrySpentBackgroundPoints, $ancestrySpentBackgroundPoints->getValue());

        $backgroundSkillPoints = $background->getSkillPointsFromBackground();
        self::assertInstanceOf(SkillPointsFromBackground::class, $backgroundSkillPoints);
        $skillPointsSpentBackgroundPoints = $backgroundSkillPoints->getSpentBackgroundPoints();
        self::assertSame(
            $forBackgroundSkillPointsSpentBackgroundPoints,
            $skillPointsSpentBackgroundPoints->getValue()
        );

        $possession = $background->getPossession();
        self::assertInstanceOf(Possession::class, $possession);
        $possessionSpentBackgroundPoints = $possession->getSpentBackgroundPoints();
        self::assertSame($forBelongingsSpentBackgroundPoints, $possessionSpentBackgroundPoints->getValue());

        self::assertSame(
            $backgroundPoints->getValue()
            - $ancestry->getSpentBackgroundPoints()->getValue()
            - $backgroundSkillPoints->getSpentBackgroundPoints()->getValue()
            - $possession->getSpentBackgroundPoints()->getValue(),
            $background->getRemainingBackgroundPoints()
        );
    }

    public function provideBackgroundPoints()
    {
        return [
            [FateCode::getIt(FateCode::GOOD_BACKGROUND), 1, 1, 1],
        ];
    }

    /**
     * @test
     */
    public function I_can_not_spent_more_than_available_points_in_total()
    {
        $this->expectException(\DrdPlus\Background\Exceptions\TooMuchSpentBackgroundPoints::class);
        $backgroundPoints = BackgroundPoints::getIt(FateCode::getIt(FateCode::GOOD_BACKGROUND), Tables::getIt());
        $pointsForAncestry = 6;
        $pointsForBackgroundSkillPoints = 5;
        $pointsForBelongings = 6;
        self::assertGreaterThan(
            $backgroundPoints->getValue(),
            $pointsForAncestry
            + $pointsForBackgroundSkillPoints
            + $pointsForBelongings
        );

        Background::createIt(
            FateCode::getIt(FateCode::GOOD_BACKGROUND),
            new PositiveIntegerObject($pointsForAncestry),
            new PositiveIntegerObject($pointsForBelongings),
            new PositiveIntegerObject($pointsForBackgroundSkillPoints),
            Tables::getIt()
        );
    }
}