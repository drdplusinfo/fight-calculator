<?php declare(strict_types=1);

namespace DrdPlus\FightCalculator;

use DrdPlus\AttackSkeleton\AttackRequest;

class FightRequest extends AttackRequest
{
    public const PROFESSION = 'profession';
    public const MELEE_FIGHT_SKILL = 'melee_fight_skill';
    public const MELEE_FIGHT_SKILL_RANK = 'melee_fight_skill_rank';
    public const RANGED_FIGHT_SKILL = 'ranged_fight_skill';
    public const RANGED_FIGHT_SKILL_RANK = 'ranged_fight_skill_rank';
    public const SHIELD_USAGE_SKILL_RANK = 'shield_usage_skill_rank';
    public const FIGHT_WITH_SHIELDS_SKILL_RANK = 'fight_with_shields_skill_rank';
    public const ARMOR_SKILL_VALUE = 'armor_skill_value';
    public const ON_HORSEBACK = 'on_horseback';
    public const RIDING_SKILL_RANK = 'riding_skill_rank';
    public const FIGHTING_FREE_WILL_ANIMAL = 'fight_free_will_animal';
    public const ZOOLOGY_SKILL_RANK = 'zoology_skill_rank';
    public const RANGED_TARGET_DISTANCE = 'ranged_target_distance';
    public const RANGED_TARGET_SIZE = 'ranged_target_size';
}