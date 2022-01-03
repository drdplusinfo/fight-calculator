<?php declare(strict_types=1);

namespace DrdPlus\Tests\Races;

use DrdPlus\Codes\GenderCode;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Codes\RaceCode;
use DrdPlus\Codes\SubRaceCode;
use DrdPlus\Races\Dwarfs\CommonDwarf;
use DrdPlus\Races\Race;
use DrdPlus\Tables\Measurements\Weight\Weight;
use DrdPlus\Tables\Tables;
use Granam\TestWithMockery\TestWithMockery;

abstract class RaceTest extends TestWithMockery
{
    /**
     * @test
     * @return Race
     */
    public function I_can_get_race()
    {
        $subraceClass = $this->getSubraceClass();
        $subrace = $subraceClass::getIt();
        self::assertInstanceOf($subraceClass, $subrace);
        self::assertSame($this->getRaceCode(), $subrace->getRaceCode());
        self::assertSame($this->getSubraceCode(), $subrace->getSubRaceCode());
        self::assertSame($subrace, $subraceClass::getEnum($this->getRaceCode() . '-' . $subrace->getSubRaceCode()));

        return $subrace;
    }

    /**
     * @return string|Race|CommonDwarf
     */
    private function getSubraceClass()
    {
        return preg_replace('~[\\\]Tests(.+)Test$~', '$1', static::class);
    }

    /**
     * @return SubRaceCode
     */
    private function getSubraceCode()
    {
        $subraceCodeString = str_replace((string)$this->getRaceCode(), '', strtolower($this->getSubraceBaseName()));

        return SubRaceCode::getIt($subraceCodeString);
    }

    /**
     * @return string
     */
    private function getSubraceBaseName()
    {
        $subraceClass = $this->getSubraceClass();

        return preg_replace('~(\w+\\\){0,5}(\w+)~', '$2', $subraceClass);
    }

    /**
     * @return RaceCode
     */
    private function getRaceCode()
    {
        $baseNamespace = $this->getSubraceBaseNamespace();
        if (preg_match('~ves$~', $baseNamespace)) {
            $singular = preg_replace('~ves$~', 'f', $baseNamespace);
        } else {
            $singular = preg_replace('~s$~', '', $baseNamespace);
        }

        return RaceCode::getIt(strtolower($singular));
    }

    /**
     * @return string
     */
    private function getSubraceBaseNamespace()
    {
        $namespace = $this->getSubraceNamespace();

        return preg_replace('~(\w+\\\){0,5}(\w+)~', '$2', $namespace);
    }

    /**
     * @return string
     */
    private function getSubraceNamespace()
    {
        $subraceClass = $this->getSubraceClass();

        return preg_replace('~\\\[\w]+$~', '', $subraceClass);
    }

    /**
     * @test
     */
    public function I_can_not_create_it_by_enum_factory_method_with_invalid_code()
    {
        $this->expectException(\DrdPlus\Races\Exceptions\UnknownRaceCode::class);
        $subraceClass = $this->getSubraceClass();
        $subraceClass::getEnum('foo');
    }

    /**
     * @test
     * @depends I_can_get_race
     * @param Race $race
     */
    public function I_can_get_base_property(Race $race)
    {
        $tables = Tables::getIt();
        foreach ($this->getGenders() as $genderCode) {
            foreach ($this->getBasePropertyNames() as $basePropertyName) {
                $basePropertyCode = PropertyCode::getIt($basePropertyName);
                $sameValueByGenericGetter = $race->getProperty($basePropertyCode, $genderCode, $tables);
                switch ($basePropertyName) {
                    case PropertyCode::STRENGTH :
                        $value = $race->getStrength($genderCode, $tables);
                        break;
                    case PropertyCode::AGILITY :
                        $value = $race->getAgility($genderCode, $tables);
                        break;
                    case PropertyCode::KNACK :
                        $value = $race->getKnack($genderCode, $tables);
                        break;
                    case PropertyCode::WILL :
                        $value = $race->getWill($genderCode, $tables);
                        break;
                    case PropertyCode::INTELLIGENCE :
                        $value = $race->getIntelligence($genderCode, $tables);
                        break;
                    case PropertyCode::CHARISMA :
                        $value = $race->getCharisma($genderCode, $tables);
                        break;
                    default :
                        $value = null;
                }
                self::assertSame(
                    $this->getExpectedBaseProperty($genderCode->getValue(), $basePropertyName),
                    $value,
                    "Unexpected {$genderCode} $basePropertyName"
                );
                self::assertSame($sameValueByGenericGetter, $value);
            }
        }
    }

    /**
     * @return array|GenderCode[]
     */
    private function getGenders()
    {
        return [
            GenderCode::getIt(GenderCode::MALE),
            GenderCode::getIt(GenderCode::FEMALE),
        ];
    }

    /**
     * @return array|string[]
     */
    private function getBasePropertyNames()
    {
        return [
            PropertyCode::STRENGTH,
            PropertyCode::AGILITY,
            PropertyCode::KNACK,
            PropertyCode::WILL,
            PropertyCode::INTELLIGENCE,
            PropertyCode::CHARISMA,
        ];
    }

    /**
     * @param string $genderCode
     * @param string $propertyCode
     * @return int
     */
    abstract protected function getExpectedBaseProperty($genderCode, $propertyCode);

    /**
     * @test
     * @depends I_can_get_race
     * @param Race $race
     */
    public function I_can_not_get_property_by_its_invalid_code(Race $race)
    {
        $this->expectException(\DrdPlus\Races\Exceptions\UnknownPropertyCode::class);
        $this->expectExceptionMessageMatches('~soft~');
        $tables = Tables::getIt();
        /** @var GenderCode $genderCode */
        $genderCode = \Mockery::mock(GenderCode::class);
        $race->getProperty($this->createPropertyCode('soft'), $genderCode, $tables);
    }

    /**
     * @param string $value
     * @return \Mockery\MockInterface|PropertyCode
     */
    private function createPropertyCode($value)
    {
        $propertyCode = $this->mockery(PropertyCode::class);
        $propertyCode->shouldReceive('getValue')
            ->andReturn($value);
        $propertyCode->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $propertyCode;
    }

    /**
     * @test
     * @depends I_can_get_race
     * @param Race $race
     * @throws \LogicException
     */
    public function I_can_get_non_base_property(Race $race)
    {
        foreach ($this->getGenders() as $genderCode) {
            foreach ($this->getNonBaseNonDerivedPropertyNames() as $propertyName) {
                $propertyCode = PropertyCode::getIt($propertyName);
                $sameValueByGenericGetter = $race->getProperty($propertyCode, $genderCode, Tables::getIt());
                switch ($propertyName) {
                    case PropertyCode::SENSES :
                        $value = $race->getSenses(Tables::getIt());
                        break;
                    case PropertyCode::TOUGHNESS :
                        $value = $race->getToughness(Tables::getIt());
                        break;
                    case PropertyCode::SIZE :
                        $value = $race->getSize($genderCode, Tables::getIt());
                        break;
                    case PropertyCode::BODY_WEIGHT :
                        $value = $race->getBodyWeight($genderCode, Tables::getIt());
                        break;
                    case PropertyCode::BODY_WEIGHT_IN_KG :
                        $value = $race->getWeightInKg($genderCode, Tables::getIt());
                        break;
                    case PropertyCode::HEIGHT_IN_CM :
                        $value = $race->getHeightInCm(Tables::getIt());
                        break;
                    case PropertyCode::HEIGHT :
                        $value = $race->getHeight(Tables::getIt());
                        break;
                    case PropertyCode::INFRAVISION :
                        $value = $race->hasInfravision(Tables::getIt());
                        break;
                    case PropertyCode::NATIVE_REGENERATION :
                        $value = $race->hasNativeRegeneration(Tables::getIt());
                        break;
                    case PropertyCode::REQUIRES_DM_AGREEMENT :
                        $value = $race->requiresDmAgreement(Tables::getIt());
                        break;
                    case PropertyCode::REMARKABLE_SENSE :
                        $value = $race->getRemarkableSense(Tables::getIt());
                        break;
                    case PropertyCode::AGE :
                        $value = $race->getAge(Tables::getIt());
                        break;
                    default :
                        throw new \LogicException(
                            "Unexpected property {$propertyName} for {$race->getSubRaceCode()} {$race->getRaceCode()} {$genderCode}"
                        );
                }
                if ($propertyName === PropertyCode::BODY_WEIGHT) {
                    $expectedOtherProperty = $this->getExpectedWeightBonus($genderCode, Tables::getIt());
                } else {
                    $expectedOtherProperty = $this->getExpectedOtherProperty($propertyName, $genderCode->getValue());
                }
                self::assertEquals(
                    $expectedOtherProperty,
                    $value,
                    "Unexpected {$propertyName} of {$race->getSubRaceCode()} {$race->getRaceCode()} {$genderCode}"
                );
                self::assertSame($sameValueByGenericGetter, $value);
            }
        }
    }

    /**
     * @return array|string[]
     */
    private function getNonBaseNonDerivedPropertyNames(): array
    {
        return \array_diff(
            PropertyCode::getPossibleValues(),
            PropertyCode::getBasePropertyPossibleValues(),
            PropertyCode::getDerivedPropertyPossibleValues(), // exclude derived properties
            PropertyCode::getRemarkableSensePropertyPossibleValues() // those are just values of a remarkable sense
        );
    }

    /**
     * @param GenderCode $genderCode
     * @param Tables $tables
     * @return int
     */
    private function getExpectedWeightBonus(GenderCode $genderCode, Tables $tables): int
    {
        return (new Weight(
            $this->getExpectedOtherProperty(PropertyCode::BODY_WEIGHT_IN_KG, $genderCode->getValue()),
            Weight::KG,
            $tables->getWeightTable()
        ))->getBonus()->getValue();
    }

    /**
     * @param string $propertyCode
     * @param string $genderCode
     * @return int|float|bool|string
     */
    abstract protected function getExpectedOtherProperty($propertyCode, $genderCode);
}
