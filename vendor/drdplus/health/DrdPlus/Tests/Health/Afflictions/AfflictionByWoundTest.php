<?php
namespace DrdPlus\Tests\Health\Afflictions;

use DrdPlus\Codes\Body\SeriousWoundOriginCode;
use DrdPlus\Codes\Body\WoundOriginCode;
use DrdPlus\Health\Afflictions\AfflictionByWound;
use DrdPlus\Health\Afflictions\AfflictionDangerousness;
use DrdPlus\Health\Afflictions\AfflictionDomain;
use DrdPlus\Health\Afflictions\AfflictionName;
use DrdPlus\Health\Afflictions\AfflictionProperty;
use DrdPlus\Health\Afflictions\AfflictionSize;
use DrdPlus\Health\Afflictions\AfflictionSource;
use DrdPlus\Health\Afflictions\AfflictionVirulence;
use DrdPlus\Health\Afflictions\Effects\AfflictionEffect;
use DrdPlus\Health\Afflictions\ElementalPertinence\ElementalPertinence;
use DrdPlus\Health\GridOfWounds;
use DrdPlus\Health\Health;
use DrdPlus\Health\OrdinaryWound;
use DrdPlus\Health\SeriousWound;
use DrdPlus\Health\WoundSize;
use DrdPlus\Properties\Derived\WoundBoundary;

abstract class AfflictionByWoundTest extends AfflictionTest
{

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_will_malus()
    {
        $afflictionReflection = new \ReflectionClass(self::getSutClass());
        $afflictionConstructor = $afflictionReflection->getConstructor();
        $afflictionConstructor->setAccessible(true);
        /** @var AfflictionByWound $afflictionInstance */
        $afflictionInstance = $afflictionReflection->newInstanceWithoutConstructor();
        self::assertSame(0, $afflictionInstance->getWillMalus());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_intelligence_malus()
    {
        $afflictionReflection = new \ReflectionClass(self::getSutClass());
        $afflictionConstructor = $afflictionReflection->getConstructor();
        $afflictionConstructor->setAccessible(true);
        /** @var AfflictionByWound $afflictionInstance */
        $afflictionInstance = $afflictionReflection->newInstanceWithoutConstructor();
        self::assertSame(0, $afflictionInstance->getIntelligenceMalus());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_charisma_malus()
    {
        $afflictionReflection = new \ReflectionClass(self::getSutClass());
        $afflictionConstructor = $afflictionReflection->getConstructor();
        $afflictionConstructor->setAccessible(true);
        /** @var AfflictionByWound $afflictionInstance */
        $afflictionInstance = $afflictionReflection->newInstanceWithoutConstructor();
        self::assertSame(0, $afflictionInstance->getCharismaMalus());
    }

    /**
     * @test
     * @expectedException \DrdPlus\Health\Afflictions\Exceptions\WoundHasToBeFreshForAffliction
     * @throws \ReflectionException
     */
    public function I_can_not_create_it_with_old_wound()
    {
        $reflection = new \ReflectionClass(self::getSutClass());
        $constructor = $reflection->getConstructor();
        $constructor->setAccessible(true);

        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke(
            $instance,
            $this->createWound(true /* serious */, true /* old */),
            $this->mockery(AfflictionProperty::class),
            $this->mockery(AfflictionDangerousness::class),
            $this->mockery(AfflictionDomain::class),
            $this->mockery(AfflictionVirulence::class),
            $this->mockery(AfflictionSource::class),
            $this->mockery(AfflictionSize::class),
            $this->mockery(ElementalPertinence::class),
            $this->mockery(AfflictionEffect::class),
            $this->mockery(\DateInterval::class),
            $this->mockery(AfflictionName::class)
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function It_is_linked_with_health_immediately()
    {
        $woundBoundary = $this->mockery(WoundBoundary::class);
        $woundBoundary->shouldReceive('getValue')
            ->andReturn(5);
        $health = new Health();
        $woundSize = $this->mockery(WoundSize::class);
        $woundSize->shouldReceive('getValue')
            ->andReturn(5);
        /** @var WoundBoundary $woundBoundary */
        /** @var WoundSize $woundSize */
        $seriousWound = $health->addWound(
            $woundSize,
            SeriousWoundOriginCode::getMechanicalCutWoundOrigin(),
            $woundBoundary
        );
        $afflictionReflection = new \ReflectionClass(self::getSutClass());
        $afflictionConstructor = $afflictionReflection->getConstructor();
        $afflictionConstructor->setAccessible(true);

        $afflictionInstance = $afflictionReflection->newInstanceWithoutConstructor();
        $afflictionConstructor->invoke(
            $afflictionInstance,
            $seriousWound,
            $this->mockery(AfflictionProperty::class),
            $this->mockery(AfflictionDangerousness::class),
            $this->mockery(AfflictionDomain::class),
            $this->mockery(AfflictionVirulence::class),
            $this->mockery(AfflictionSource::class),
            $this->mockery(AfflictionSize::class),
            $this->mockery(ElementalPertinence::class),
            $this->mockery(AfflictionEffect::class),
            $this->mockery(\DateInterval::class),
            $this->mockery(AfflictionName::class)
        );
        self::assertSame([$afflictionInstance], $health->getAfflictions());
    }

    /**
     * @param bool $isSerious
     * @param bool $isOld
     * @param $value
     * @param WoundOriginCode $woundOriginCode
     * @return \Mockery\MockInterface|SeriousWound|OrdinaryWound
     */
    protected function createWound($isSerious = true, $isOld = false, $value = 0, WoundOriginCode $woundOriginCode = null)
    {
        $wound = $this->mockery($isSerious ? SeriousWound::class : OrdinaryWound::class);
        $wound->shouldReceive('getHealth')
            ->andReturn($health = $this->mockery(Health::class));
        $health->shouldReceive('addAffliction')
            ->zeroOrMoreTimes()
            ->with(\Mockery::type(self::getSutClass()));
        $wound->shouldReceive('isSerious')
            ->andReturn($isSerious);
        $wound->shouldReceive('isOld')
            ->andReturn($isOld);
        $wound->shouldReceive('getWoundSize')
            ->andReturn($woundSize = $this->mockery(WoundSize::class));
        $woundSize->shouldReceive('getValue')
            ->andReturn($value);
        $wound->shouldReceive('__toString')
            ->andReturn((string)$value);
        $wound->shouldReceive('getWoundOriginCode')
            ->andReturn($woundOriginCode ?: SeriousWoundOriginCode::getElementalWoundOrigin());

        return $wound;
    }

    /**
     * @param SeriousWound $wound
     * @param WoundBoundary $woundBoundary
     * @param int $filledHalfOfRows
     */
    protected function addSizeCalculation(SeriousWound $wound, WoundBoundary $woundBoundary, $filledHalfOfRows)
    {
        /** @var SeriousWound $wound */
        $health = $wound->getHealth();
        /** @var \Mockery\MockInterface $health */
        $health->shouldReceive('getGridOfWounds')
            ->andReturn($gridOfWounds = $this->mockery(GridOfWounds::class));
        $gridOfWounds->shouldReceive('calculateFilledHalfRowsFor')
            ->zeroOrMoreTimes()
            ->with($wound->getWoundSize(), $woundBoundary)
            ->andReturn($filledHalfOfRows);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|WoundBoundary
     */
    protected function createWoundBoundary($value)
    {
        $woundBoundary = $this->mockery(WoundBoundary::class);
        $woundBoundary->shouldReceive('getValue')
            ->andReturn($value);

        return $woundBoundary;
    }

}