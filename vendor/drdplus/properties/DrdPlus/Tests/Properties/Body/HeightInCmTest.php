<?php
declare(strict_types=1);

namespace DrdPlus\Tests\Properties\Body;

use DrdPlus\Properties\Body\HeightInCm;
use DrdPlus\Tables\Tables;
use DrdPlus\Tests\Properties\Partials\AbstractFloatPropertyTest;

class HeightInCmTest extends AbstractFloatPropertyTest
{
    use BodyPropertyTest;

    /**
     * @test
     */
    public function I_can_get_height(): void
    {
        $heightInCm = HeightInCm::getIt(90000);
        self::assertSame(59, $heightInCm->getHeight(Tables::getIt())->getValue());
    }
}