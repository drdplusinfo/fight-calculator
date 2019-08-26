<?php declare(strict_types=1);

namespace DrdPlus\Tests\Skills\Combined;

use DrdPlus\Skills\CausingMalusesToWeaponUsage;
use DrdPlus\Skills\Physical\PhysicalSkill;
use DrdPlus\Tests\Skills\SkillTest;

abstract class CausingMalusesToWeaponUsageTest extends SkillTest
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_malus_to_fight_number(): void
    {
        $sutClasses = self::getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage|PhysicalSkill $sut */
            $sut = new $sutClass($this->createProfessionFirstLevel());
            self::assertSame(-3, $sut->getMalusToFightNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-2, $sut->getMalusToFightNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-1, $sut->getMalusToFightNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(0, $sut->getMalusToFightNumber());
        }
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    protected static function getSutClasses(): array
    {
        $reflection = new \ReflectionClass(self::getSutClass());
        $sutClasses = [];
        foreach (new \DirectoryIterator(\dirname($reflection->getFileName())) as $file) {
            if ($file->isFile()) {
                $baseName = $file->getBasename('.php');
                $foundClass = $reflection->getNamespaceName() . '\\' . $baseName;
                if (\is_subclass_of($foundClass, self::getSutClass(), true)) {
                    $sutClasses[] = $foundClass;
                }
            }
        }

        return $sutClasses;
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_malus_to_attack_number(): void
    {
        $sutClasses = self::getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage|PhysicalSkill $sut */
            $sut = new $sutClass($this->createProfessionFirstLevel());
            self::assertSame(-3, $sut->getMalusToAttackNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-2, $sut->getMalusToAttackNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-1, $sut->getMalusToAttackNumber());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(0, $sut->getMalusToAttackNumber());
        }
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_malus_to_cover(): void
    {
        $sutClasses = self::getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage|PhysicalSkill $sut */
            $sut = new $sutClass($this->createProfessionFirstLevel());
            self::assertSame(-2, $sut->getMalusToCover());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-1, $sut->getMalusToCover());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-1, $sut->getMalusToCover());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(0, $sut->getMalusToCover());
        }
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_get_malus_to_base_of_wounds(): void
    {
        $sutClasses = self::getSutClasses();
        foreach ($sutClasses as $sutClass) {
            /** @var CausingMalusesToWeaponUsage|PhysicalSkill $sut */
            $sut = new $sutClass($this->createProfessionFirstLevel());
            self::assertSame(-1, $sut->getMalusToBaseOfWounds());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(-1, $sut->getMalusToBaseOfWounds());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(0, $sut->getMalusToBaseOfWounds());
            $sut->increaseSkillRank($this->createSkillPoint());
            self::assertSame(0, $sut->getMalusToBaseOfWounds());
        }
    }
}