<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Skills;

use DrdPlus\Skills\FightWithWeaponlikeSkill;
use Granam\Tests\Tools\TestWithMockery;

class FightWithWeaponlikeSkillTest extends TestWithMockery
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function I_can_use_every_fight_with_weaponlike_skill_as_this_interface(): void
    {
        $fightWithWeaponlikeClasses = $this->getFightWithWeaponlikeClasses();
        self::assertNotEmpty(
            $fightWithWeaponlikeClasses,
            'No fight with weaponlike classes found'
        );
        foreach ($fightWithWeaponlikeClasses as $fightWithWeaponlikeClass) {
            $reflectionClass = new \ReflectionClass($fightWithWeaponlikeClass);
            self::assertTrue(
                $reflectionClass->implementsInterface(FightWithWeaponlikeSkill::class),
                $fightWithWeaponlikeClass . ' should implements ' . FightWithWeaponlikeSkill::class
            );
        }
    }

    /**
     * @return array|FightWithWeaponlikeSkill[]
     * @throws \ReflectionException
     */
    private function getFightWithWeaponlikeClasses(): array
    {
        $fightWithWeaponlikeSkillReflection = new \ReflectionClass(FightWithWeaponlikeSkill::class);
        $rootDir = \dirname($fightWithWeaponlikeSkillReflection->getFileName());
        $rootNamespace = $fightWithWeaponlikeSkillReflection->getNamespaceName();

        return $this->scanForPotentiallyChildClasses($rootDir, $rootNamespace);
    }

    /**
     * @param string $rootDir
     * @param string $rootNamespace
     * @return array
     * @throws \ReflectionException
     */
    private function scanForPotentiallyChildClasses(string $rootDir, string $rootNamespace): array
    {
        $childClasses = [];
        foreach (\scandir($rootDir, \SCANDIR_SORT_NONE) as $folder) {
            $folderFullPath = $rootDir . DIRECTORY_SEPARATOR . $folder;
            if ($folder !== '.' && $folder !== '..') {
                if (\is_dir($folderFullPath)) {
                    foreach ($this->scanForPotentiallyChildClasses($folderFullPath, $rootNamespace . '\\' . $folder) as $foundCode) {
                        $childClasses[] = $foundCode;
                    }
                } elseif (\is_file($folderFullPath) && \preg_match('~(?<classBasename>FightWith\w+)\.php$~', $folder, $matches)) {
                    $reflectionClass = new \ReflectionClass($rootNamespace . '\\' . $matches['classBasename']);
                    if (!$reflectionClass->isAbstract() && !$reflectionClass->isTrait() && !$reflectionClass->isInterface()) {
                        $childClasses[] = $reflectionClass->getName();
                    }
                }
            }
        }

        return $childClasses;
    }
}