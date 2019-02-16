# Skills for [DrD+](http://www.altar.cz/drdplus/)

[![Build Status](https://travis-ci.org/jaroslavtyc/drd-plus-skills.svg?branch=master)](https://travis-ci.org/jaroslavtyc/drd-plus-skills)
[![Test Coverage](https://codeclimate.com/github/jaroslavtyc/drd-plus-skills/badges/coverage.svg)](https://codeclimate.com/github/jaroslavtyc/drd-plus-skills/coverage)
[![License](https://poser.pugx.org/drd-plus/skills/license)](https://packagist.org/packages/drd-plus/skills)

*Can you swim? How long you will read that book? Can you hold on horse?*

### Warning about distinction from PPH rules
The **reading and writing** skill gives a "bonus" to reading speed, despite PPH rules where
reading and writing is impossible.

This library gives -164 as a "bonus" to reading speed, which means 100 years.
The reason is mostly technical - because returning *something* is easier and more clear
than *nothing* (by null or exception), and partly because its logical - after ten years you have a chance to decode that
strange symbols, especially when they mean something in a language you already know.

### Structure

Skills are all the person skills on one pile.
-> SameTypeSkills are skills of same type, like physical, on one pile
  -> Skill is specific learned "ability", like horse riding
    -> SkillRank is a "level" of the skill
       => SkillPoint is the only but required price of a SkillRank

SkillPoint is the currency unit for a SkillRank, composed from specific values, in specific combinations
-> BackgroundSkillPoints are standard value given by first level
-> ProfessionLevel is a level increment, cary-ing a property increment, which provides a skill point
-> two SkillPoint(s) of type(s)-different-than-paid-one can be used for trade of new SkillPoint

Checks if payments haven't been used more times elsewhere:
(that should check at least Skills as the final aggregator, or better every aggregator on the way)

SkillPoint
- BackgroundSkillPoints - check their total usage against their availability by \DrdPlus\\Background\BackgroundSkillPoints::getSkillPoints
- ProfessionLevel - there is nothing to check, on every level can be obtained plenty of skill points
- cross-type SkillPoint as a payment - has to be unique, therefore no one else can use it, for payment nor as standard skill point

SkillRank
- SkillPoint has to be unique in whole universe, see SkillPoint cross-type payment check

Skill
- can be used just as an arbiter due its aggregating meaning
- ~~can check if BackgroundSkillPoints are not overused~~ should not check it, its too tricky, let it to Skills
- can check if SkillPoint and SkillRank are unique locally
- can check if cross-type SkillPoint(s) as a payment are unique and not used as a standard point locally

SameTypeSkills
(can sum all the first and next levels skill ranks)
- can be used just as an arbiter due its aggregating meaning

CombinedSkills + PhysicalSkills + PsychicalSkills
(has ability to find out unused skill point values of same-type skills)
- all the same-type skills it aggregate have to be unique in whole universe

Skills
- CombinedSkills + PhysicalSkills + PsychicalSkills has to be unique in whole universe
- should be the final arbiter, ~~recommended way is to re-use existing checks of sub-aggregates~~
