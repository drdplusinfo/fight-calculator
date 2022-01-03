# Table values for [DrD+](http://www.altar.cz/drdplus/)

[![Build Status](https://travis-ci.com/drdplusinfo/tables.svg?branch=master)](https://travis-ci.com/jaroslavtyc/drd-plus-tables)
[![License](https://poser.pugx.org/drdplus/tables/license)](https://packagist.org/packages/drdplus/tables)

### Warning about JumpsAndFallsTable
[JumpsAndFallsTable](/src/Activities/JumpsAndFallsTable.php) automatically lowers wounds from fall (or jump) by an armour protection, despite rules
which DM should decide about portion of reduced damage by himself.

### Description

Over sixty tables used for calculation and information in DrD+.

- Table is optional. Measurement is what made it real.
- Bonus, if it has a sense, is expressed by an entity.
- Measurement HAS TO exist (except for base of wounds, which is for numerical transpose only)
- Measurement HAS TO implement MeasurementInterface
- Measurement HAS TO be able to give its current unit
- Measurement MAY be multi-unit
    - IF Measurement is multi-unit
        - it HAS TO be able to get new self with any of those units
- Measurement MAY be based on Bonus
    - IF Measurement has Bonus,
        - it HAS TO implement MeasurementWithBonusInterface
        - and it HAS TO be able to get equivalent Bonus and Bonus has to be able to get equivalent Measurement
    - Every Bonus HAS TO have getter (getFoo) for related Measurement
    - Every Measurement with Bonus HAS TO have getter (getBar) for related Bonus
- Measurement MAY use Table
- Measurement MAY use a Bonus and a Table at once
    - IF Measurement has a Bonus and a Table,
        - Then that Table HAS to have Measurement converter to Bonus (recommended toBonus)
        - and Bonus converter to Measurement (recommended toFoo)
- Measurement MAY provide conversion helper-methods to value in different unit (but HAS TO provide getter for Measurement in that unit)
- Table SHOULD NOT have conversion methods different that for Measurement to Bonus and vice versa
- Bonus SHOULD NOT have conversion methods different that related Measurement getter
- Measurement type MAY have own Exceptions
    - IF Measurement type has own Exceptions, those HAVE TO follow exception hierarchy

Note: *Price* and *Base of wounds* are special cases.
