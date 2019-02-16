<?php
declare(strict_types=1);

namespace DrdPlus\Tests\AttackSkeleton;

use DeviceDetector\Parser\Bot;
use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CurrentProperties;
use DrdPlus\AttackSkeleton\CustomArmamentsRegistrar;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\CalculatorSkeleton\Memory;
use DrdPlus\Codes\Armaments\ArmamentCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\BaseProperties\Agility;
use DrdPlus\BaseProperties\Charisma;
use DrdPlus\BaseProperties\Intelligence;
use DrdPlus\BaseProperties\Knack;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\BaseProperties\Will;
use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Properties\Body\Size;
use DrdPlus\RulesSkeleton\Configuration;
use DrdPlus\RulesSkeleton\ServicesContainer;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\CalculatorSkeleton\Partials\AbstractCalculatorContentTest;
use Mockery\MockInterface;

/**
 * @method AttackServicesContainer getServicesContainer
 */
abstract class AbstractAttackTest extends AbstractCalculatorContentTest
{
    use Partials\AttackCalculatorTestTrait;

    /**
     * @param CurrentArmamentsValues $currentArmamentsValues
     * @return CustomArmamentsRegistrar|MockInterface
     */
    protected function createCustomArmamentsRegistrar(CurrentArmamentsValues $currentArmamentsValues): CustomArmamentsRegistrar
    {
        $currentArmamentsRegistrar = $this->mockery(CustomArmamentsRegistrar::class);
        $currentArmamentsRegistrar->shouldReceive('registerCustomArmaments')
            ->with($currentArmamentsValues);

        return $currentArmamentsRegistrar;
    }

    /**
     * @return CurrentArmamentsValues|MockInterface
     */
    protected function createCurrentArmamentValues(): CurrentArmamentsValues
    {
        return $this->mockery(CurrentArmamentsValues::class);
    }

    /**
     * @return PossibleArmaments|MockInterface
     */
    protected function createPossibleArmaments(): PossibleArmaments
    {
        return $this->mockery(PossibleArmaments::class);
    }

    /**
     * @param array|string[] $unusableArmaments
     * @return PossibleArmaments|MockInterface
     */
    protected function createPossibleArmamentsWithUnusable(array $unusableArmaments): PossibleArmaments
    {

        return new PossibleArmaments(
            $this->createArmourerWithUnusableArmament($unusableArmaments),
            $this->createMaximalCurrentProperties(),
            ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS),
            ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS)
        );
    }

    /**
     * @param array|string[] $unusableArmaments
     * @return Armourer|MockInterface
     */
    private function createArmourerWithUnusableArmament(array $unusableArmaments): Armourer
    {
        /** @var Armourer|MockInterface $armourer */
        $armourer = $this->mockery(Armourer::class);
        $armourer->shouldReceive('canUseArmament')
            ->with($this->type(ArmamentCode::class), $this->type(Strength::class), $this->type(Size::class))
            ->andReturnUsing(function (ArmamentCode $armamentCode) use ($unusableArmaments) {
                return !\in_array($armamentCode->getValue(), $unusableArmaments, true);
            });
        $armourer->makePartial();
        $armourer->__construct(Tables::getIt());
        return $armourer;
    }

    /**
     * @return ArmamentsUsabilityMessages|MockInterface
     */
    protected function createArmamentsUsabilityMessages(): ArmamentsUsabilityMessages
    {
        return $this->mockery(ArmamentsUsabilityMessages::class);
    }

    /**
     * @return ArmamentsUsabilityMessages|MockInterface
     */
    protected function getEmptyArmamentsUsabilityMessages(): ArmamentsUsabilityMessages
    {
        static $emptyArmamentsUsabilityMessages;
        if ($emptyArmamentsUsabilityMessages === null) {
            $emptyArmamentsUsabilityMessages = new ArmamentsUsabilityMessages($this->getAllPossibleArmaments());
            self::assertCount(
                0,
                $emptyArmamentsUsabilityMessages->getMessagesAboutHelms(),
                'No messages about usability of helms expected'
            );
            self::assertCount(
                0,
                $emptyArmamentsUsabilityMessages->getMessagesAboutMeleeWeapons(),
                'No messages about usability of melee weapons expected'
            );
            self::assertCount(
                0,
                $emptyArmamentsUsabilityMessages->getMessagesAboutRangedWeapons(),
                'No messages about usability of ranged weapons expected'
            );
            self::assertCount(
                0,
                $emptyArmamentsUsabilityMessages->getMessagesAboutShields(),
                'No messages about usability of shields expected'
            );
            self::assertCount(
                0,
                $emptyArmamentsUsabilityMessages->getMessagesAboutBodyArmors(),
                'No messages about usability of body armors expected'
            );
        }
        return $emptyArmamentsUsabilityMessages;
    }

    protected function getAllPossibleArmaments(): PossibleArmaments
    {
        static $allPossibleArmaments;
        if ($allPossibleArmaments === null) {
            $allPossibleArmaments = new PossibleArmaments(
                Armourer::getIt(),
                $this->createMaximalCurrentProperties(),
                ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS),
                ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS)
            );
        }
        return $allPossibleArmaments;
    }

    /**
     * @param int $activePropertiesMaximum = 999
     * @param int $sizeMaximum = 0
     * @param int $heightInCmMaximum = 200
     * @return CurrentProperties|MockInterface
     */
    protected function createMaximalCurrentProperties(
        int $activePropertiesMaximum = 999,
        int $sizeMaximum = 0,
        int $heightInCmMaximum = 200
    ): CurrentProperties
    {
        $currentProperties = $this->mockery(CurrentProperties::class);
        $currentProperties->shouldReceive('getCurrentStrength')
            ->andReturn(Strength::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentAgility')
            ->andReturn(Agility::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentKnack')
            ->andReturn(Knack::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentWill')
            ->andReturn(Will::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentIntelligence')
            ->andReturn(Intelligence::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentCharisma')
            ->andReturn(Charisma::getIt($activePropertiesMaximum));
        $currentProperties->shouldReceive('getCurrentSize')
            ->andReturn(Size::getIt($sizeMaximum));
        $currentProperties->shouldReceive('getCurrentHeightInCm')
            ->andReturn(HeightInCm::getIt($heightInCmMaximum));
        return $currentProperties;
    }

    protected function getDefaultCurrentArmaments(): CurrentArmaments
    {
        static $defaultCurrentArmaments;
        if ($defaultCurrentArmaments === null) {
            $defaultCurrentArmaments = new CurrentArmaments(
                new CurrentProperties(new CurrentValues([], $this->createEmptyMemory())),
                $currentArmamentValues = $this->getEmptyCurrentArmamentValues(),
                Armourer::getIt(),
                $this->createCustomArmamentsRegistrar($currentArmamentValues)
            );
        }
        return $defaultCurrentArmaments;
    }

    /**
     * @return CurrentArmamentsValues|MockInterface
     */
    protected function getEmptyCurrentArmamentValues(): CurrentArmamentsValues
    {
        static $emptyCurrentArmamentValues;
        if ($emptyCurrentArmamentValues === null) {
            $emptyCurrentArmamentValues = new CurrentArmamentsValues($this->createEmptyCurrentValues());
        }
        return $emptyCurrentArmamentValues;
    }

    protected function createCurrentArmamentsWithUnusable(
        CurrentArmamentsValues $currentArmamentValues,
        array $unusableArmaments
    ): CurrentArmaments
    {
        return new CurrentArmaments(
            $this->createMaximalCurrentProperties(),
            $currentArmamentValues,
            $this->createArmourerWithUnusableArmament($unusableArmaments),
            $this->createCustomArmamentsRegistrar($currentArmamentValues)
        );
    }

    /**
     * @return CurrentValues|MockInterface
     */
    protected function createEmptyCurrentValues(): CurrentValues
    {
        $currentValues = $this->mockery(CurrentValues::class);
        $currentValues->shouldReceive('getCurrentValue')
            ->with($this->type('string'))
            ->andReturnNull();
        $currentValues->shouldReceive('getSelectedValue')
            ->with($this->type('string'))
            ->andReturnNull();
        return $currentValues;
    }

    /**
     * @return Memory|MockInterface
     */
    protected function createEmptyMemory(): Memory
    {
        $memory = $this->mockery(Memory::class);
        $memory->shouldReceive('getValue')
            ->andReturnNull();

        return $memory;
    }

    /**
     * @param Configuration|AttackServicesContainer|null $configuration
     * @param \DrdPlus\RulesSkeleton\HtmlHelper|HtmlHelper|null $htmlHelper
     * @return ServicesContainer|AttackServicesContainer
     */
    protected function createServicesContainer(
        Configuration $configuration = null,
        \DrdPlus\RulesSkeleton\HtmlHelper $htmlHelper = null
    ): ServicesContainer
    {
        return new AttackServicesContainer(
            $configuration ?? $this->getConfiguration(),
            $htmlHelper ?? $this->createHtmlHelper($this->getDirs())
        );
    }

    protected function getBot(): Bot
    {
        static $bot;
        if ($bot === null) {
            $bot = new Bot();
        }
        return $bot;
    }

    protected function getEmptyCustomArmamentsState(): CustomArmamentsState
    {
        static $emptyCustomArmamentsState;
        if ($emptyCustomArmamentsState === null) {
            $emptyCustomArmamentsState = new CustomArmamentsState($this->createEmptyCurrentValues());
        }
        return $emptyCustomArmamentsState;
    }
}