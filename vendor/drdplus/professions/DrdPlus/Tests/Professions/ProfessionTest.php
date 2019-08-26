<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Professions;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Profession;
use DrdPlus\Tables\Professions\ProfessionPrimaryPropertiesTable;
use Granam\Tests\Tools\TestWithMockery;

abstract class ProfessionTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_create_every_profession_by_code()
    {
        foreach (ProfessionCode::getPossibleValues() as $professionCode) {
            $profession = Profession::getItByCode(ProfessionCode::getIt($professionCode));
            $namespace = str_replace('Tests\\', '', __NAMESPACE__);
            $classBaseName = ucfirst($professionCode);
            $professionClass = $namespace . '\\' . $classBaseName;
            self::assertTrue(class_exists($professionClass));
            self::assertInstanceOf($professionClass, $profession);
        }
    }

    /**
     * @test
     */
    public function I_can_not_create_profession_by_unknown_code()
    {
        $this->expectException(\DrdPlus\Professions\Exceptions\ProfessionNotFound::class);
        /** @var ProfessionCode|\Mockery\MockInterface $professionCode */
        $professionCode = $this->mockery(ProfessionCode::class);
        $professionCode->shouldReceive('getValue')
            ->andReturn('muralist');
        Profession::getItByCode($professionCode);
    }

    /**
     * @test
     * @dataProvider getPropertyAndRelation
     * @return Profession
     */
    public function I_can_create_profession_and_get_its_code(): Profession
    {
        $professionClass = self::getSutClass();
        /** @var Profession|Fighter $professionClass */
        $profession = $professionClass::getIt();
        self::assertInstanceOf($professionClass, $profession);
        self::assertSame($this->getProfessionName(), $profession->getValue());

        return $profession;
    }

    /**
     * @test
     * @dataProvider getPropertyAndRelation
     * @depends      I_can_create_profession_and_get_its_code
     * @param string $propertyCode
     * @param string $shouldBePrimary
     */
    public function I_can_detect_primary_property($propertyCode, $shouldBePrimary)
    {
        $professionClass = self::getSutClass();
        /** @var Profession|Fighter $professionClass */
        $profession = $professionClass::getIt();
        self::assertSame(
            $shouldBePrimary,
            $profession->isPrimaryProperty(PropertyCode::getIt($propertyCode)),
            $profession->getValue()
        );
    }

    /**
     * @test
     */
    public function I_can_get_primary_properties()
    {
        $professionClass = self::getSutClass();
        /** @var Profession|Fighter $professionClass */
        $profession = $professionClass::getIt();
        self::assertEquals($this->getExpectedPrimaryProperties(), $profession->getPrimaryProperties(), $profession->getValue());
    }

    protected function getProfessionClassBaseName()
    {
        return preg_replace('~.*[\\\](\w+)$~', '$1', self::getSutClass());
    }

    protected function getProfessionName()
    {
        return strtolower($this->getProfessionClassBaseName());
    }

    public function getPropertyAndRelation()
    {
        return array_merge_recursive(
            [
                [PropertyCode::STRENGTH, in_array(PropertyCode::STRENGTH, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::AGILITY, in_array(PropertyCode::AGILITY, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::KNACK, in_array(PropertyCode::KNACK, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::WILL, in_array(PropertyCode::WILL, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::INTELLIGENCE, in_array(PropertyCode::INTELLIGENCE, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::CHARISMA, in_array(PropertyCode::CHARISMA, $this->getExpectedPrimaryProperties(), true)],
                [PropertyCode::AGE, false],
            ]
        );
    }

    private function getExpectedPrimaryProperties(): array
    {
        $professionPrimaryPropertiesTable = new ProfessionPrimaryPropertiesTable();

        return array_map(
            function (PropertyCode $propertyCode) {
                return $propertyCode->getValue();
            },
            $professionPrimaryPropertiesTable->getPrimaryPropertiesOf(ProfessionCode::getIt($this->getProfessionName()))
        );
    }

    /**
     * @test
     */
    public function I_can_get_profession_code()
    {
        $professionClass = self::getSutClass();
        /** @var Profession|Fighter $professionClass */
        $profession = $professionClass::getIt();
        $professionCode = $profession->getCode();
        self::assertInstanceOf(ProfessionCode::class, $professionCode);
        self::assertSame($this->getProfessionName(), $professionCode->getValue());
    }
}